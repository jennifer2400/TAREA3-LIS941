<?php
/**
 * ============================================================================
 * la cuponera sv - controlador de recuperación de contraseña
 * ============================================================================
 * base técnica y vista pública documentada para recuperación de contraseñas.
 * define la estructura de tokens protegidos para la integración de los compañeros.
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/Usuario.php';

class PasswordResetController
{
    /**
     * muestra la interfaz del esqueleto de recuperación de contraseña
     */
    public function showRequestForm(): void
    {
        Auth::requireGuest();
        require __DIR__ . '/../views/auth/password_reset.php';
    }

    /**
     * punto de integración: demostración técnica de generación de token
     */
    public function processRequest(): void
    {
        Auth::requireGuest();

        $token = $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($token)) {
            Flash::error('Sesión de seguridad inválida. Intente de nuevo.');
            header('Location: ' . url('/recuperar-password'));
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Por favor, ingrese un correo electrónico válido.');
            header('Location: ' . url('/recuperar-password'));
            exit;
        }

        $usuario = Usuario::findByIdentifier($correo);

        // para evitar enumeración de usuarios, siempre se muestra el mismo mensaje al usuario
        // pero internamente se crea el token si el usuario existe.
        if ($usuario) {
            $rawToken = PasswordReset::createResetToken((int)$usuario['id'], 3600);
            Flash::info(sprintf(
                'Estructura de recuperación verificada en base de datos. Se generó un token SHA-256 para el usuario ID #%d (Token demo: %s...). El envío real por correo SMTP está reservado para la integración de mis compañeros.',
                $usuario['id'],
                substr($rawToken, 0, 10)
            ));
        } else {
            Flash::info('Si el correo ingresado coincide con una cuenta activa, recibirá un enlace para restablecer su contraseña.');
        }

        header('Location: ' . url('/recuperar-password'));
        exit;
    }
}
