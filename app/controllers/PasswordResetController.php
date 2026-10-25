<?php
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/Usuario.php';
class PasswordResetController {
    public function showRequestForm(): void {
        Auth::requireGuest();
        require __DIR__ . '/../views/auth/password_reset.php';
    }
    public function processRequest(): void {
        Auth::requireGuest();
        if (!Csrf::validate($_POST['csrf_token']??'')) {Flash::error('Sesión inválida.');header('Location: '.url('/recuperar-password'));exit;}
        $correo=trim((string)($_POST['correo']??''));
        if (!filter_var($correo,FILTER_VALIDATE_EMAIL)) {Flash::error('Ingresa un correo válido.');header('Location: '.url('/recuperar-password'));exit;}
        // Mensaje idéntico tanto si existe como si no existe: evita enumeración.
        $usuario=Usuario::findByIdentifier($correo);
        if ($usuario && strcasecmp($usuario['correo'],$correo)===0 && $usuario['estado']==='activo') {
            $config=require __DIR__.'/../config/config.php';
            $seconds=(int)($config['security']['token_expiration']??3600);
            $raw=PasswordReset::createResetToken((int)$usuario['id'],$seconds);
            $link=url('/restablecer-password').'?token='.rawurlencode($raw);
            // En modo local sin SMTP, el enlace se registra solo en el log privado de PHP.
            // No se muestra el token en pantalla ni se incluye en Git.
            if (($config['app']['env']??'')==='development' && !empty($config['app']['debug'])) {
                $_SESSION['development_reset_link'] = $link;
                error_log('CUPONERA RECUPERACION SOLO DESARROLLO para '.$correo.': '.$link);
            } else {
                // Entrega por correo usando mail() si el servidor está configurado con SMTP.
                if (!mail($correo,'Restablecer contraseña — La Cuponera SV',"Abre este enlace para restablecer tu contraseña:
".$link."
Este enlace caduca en una hora.", 'Content-Type: text/plain; charset=UTF-8')) {
                    error_log('No se pudo enviar correo de recuperación a '.$correo);
                }
            }
        }
        Flash::info('Si el correo corresponde a una cuenta activa, recibirás instrucciones para restablecer la contraseña. En desarrollo revisa el registro privado de errores de PHP.');
        header('Location: '.url('/recuperar-password'));exit;
    }
    public function showResetForm(): void {
        Auth::requireGuest();
        $token=(string)($_GET['token']??'');
        $valid=PasswordReset::findValid($token)!==null;
        require __DIR__.'/../views/auth/password_new.php';
    }
    public function resetPassword(): void {
        Auth::requireGuest();
        if (!Csrf::validate($_POST['csrf_token']??'')) {http_response_code(403);exit('Solicitud inválida.');}
        $token=(string)($_POST['token']??'');
        $password=(string)($_POST['password']??'');
        $check=Validator::validatePassword($password);
        if (!$check['valid'] || $password!==($_POST['password_confirm']??'')) {
            Flash::error('Verifica la contraseña y su confirmación. '.implode(' ',$check['errors']));
            header('Location: '.url('/restablecer-password').'?token='.rawurlencode($token));exit;
        }
        if (!PasswordReset::consumeAndUpdate($token,password_hash($password,PASSWORD_DEFAULT))) {
            Flash::error('El enlace ya fue utilizado o ha caducado.');
            header('Location: '.url('/recuperar-password'));exit;
        }
        Flash::success('Contraseña actualizada. Ahora puedes iniciar sesión.');
        header('Location: '.url('/login'));exit;
    }
}
