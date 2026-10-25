<?php
require_once __DIR__ . '/../models/Usuario.php';

class PasswordResetController
{
    // Recuperación sin verificación externa: únicamente para la demostración local.
    private static function enabled(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $config = require __DIR__ . '/../config/config.php';
        return in_array($ip, ['127.0.0.1', '::1'], true)
            && ($config['app']['env'] ?? '') === 'development';
    }

    private static function ensureLocal(): void
    {
        if (!self::enabled()) {
            http_response_code(403);
            exit('Recuperación simplificada no disponible en este entorno.');
        }
        header('Cache-Control: no-store, private');
    }

    private static function go(string $path): void
    {
        header('Location: ' . url($path), true, 303);
        exit;
    }

    public function showRequestForm(): void
    {
        Auth::requireGuest();
        self::ensureLocal();
        require __DIR__ . '/../views/auth/password_reset.php';
    }

    public function processRequest(): void
    {
        Auth::requireGuest();
        self::ensureLocal();
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Flash::error('La solicitud caducó. Actualiza la página e inténtalo nuevamente.');
            self::go('/recuperar-password');
        }

        $now = time();
        $attempts = $_SESSION['recovery_attempts'] ?? ['start' => $now, 'count' => 0];
        if (($now - (int)($attempts['start'] ?? 0)) > 900) {
            $attempts = ['start' => $now, 'count' => 0];
        }
        if ((int)($attempts['count'] ?? 0) >= 5) {
            Flash::error('Demasiados intentos. Inténtalo después de 15 minutos.');
            self::go('/recuperar-password');
        }
        $attempts['count']++;
        $_SESSION['recovery_attempts'] = $attempts;

        $username = trim((string)($_POST['usuario'] ?? ''));
        $email = trim((string)($_POST['correo'] ?? ''));
        if ($username === '' || strlen($username) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Revisa el nombre de usuario y el correo ingresados.');
            self::go('/recuperar-password');
        }

        $user = Usuario::findByIdentifier($username);
        if (!$user || $user['estado'] !== 'activo'
            || strcasecmp((string)$user['usuario'], $username) !== 0
            || strcasecmp((string)$user['correo'], $email) !== 0) {
            Flash::error('No se pudo verificar la combinación de usuario y correo.');
            self::go('/recuperar-password');
        }

        session_regenerate_id(true);
        $_SESSION['recovery_verified'] = ['id' => (int)$user['id'], 'until' => time() + 600];
        self::go('/restablecer-password');
    }

    private static function validSession(): ?int
    {
        $info = $_SESSION['recovery_verified'] ?? null;
        if (!is_array($info) || (int)($info['until'] ?? 0) < time() || (int)($info['id'] ?? 0) <= 0) {
            unset($_SESSION['recovery_verified']);
            return null;
        }
        return (int)$info['id'];
    }

    public function showResetForm(): void
    {
        Auth::requireGuest();
        self::ensureLocal();
        $valid = self::validSession() !== null;
        require __DIR__ . '/../views/auth/password_new.php';
    }

    public function resetPassword(): void
    {
        Auth::requireGuest();
        self::ensureLocal();
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Flash::error('La solicitud caducó.');
            self::go('/recuperar-password');
        }
        $id = self::validSession();
        if ($id === null) {
            Flash::error('La solicitud expiró. Comienza de nuevo.');
            self::go('/recuperar-password');
        }
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');
        $validation = Validator::validatePassword($password);
        if (!$validation['valid'] || $password !== $confirm) {
            $reason = implode(' ', $validation['errors']);
            Flash::error('Las contraseñas no coinciden o no cumplen los requisitos. ' . $reason);
            self::go('/restablecer-password');
        }
        if (!Usuario::updatePassword($id, password_hash($password, PASSWORD_DEFAULT))) {
            Flash::error('No fue posible actualizar la contraseña.');
            self::go('/restablecer-password');
        }
        unset($_SESSION['recovery_verified'], $_SESSION['recovery_attempts']);
        session_regenerate_id(true);
        Flash::success('Contraseña actualizada. Ya puedes iniciar sesión con la nueva contraseña.');
        self::go('/login');
    }
}
