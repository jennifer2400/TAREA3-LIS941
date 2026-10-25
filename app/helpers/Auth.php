<?php
/**
 * ============================================================================
 * la cuponera sv - helper de autenticación y control de acceso (auth)
 * ============================================================================
 * maneja el ciclo de vida de la sesión autenticada, control de roles y
 * protección de rutas privadas.
 */

class Auth
{
    /**
     * inicia la sesión de php con directivas de seguridad adecuadas
     */
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../config/config.php';
            $sessionConfig = $config['session'];

            if (!headers_sent()) {
                ini_set('session.use_strict_mode', '1');
                ini_set('session.cookie_httponly', $sessionConfig['cookie_httponly'] ? '1' : '0');
                ini_set('session.cookie_samesite', $sessionConfig['cookie_samesite']);
                if (!empty($sessionConfig['cookie_secure'])) {
                    ini_set('session.cookie_secure', '1');
                }
                session_name($sessionConfig['name']);
            }

            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
        }
    }

    /**
     * registra al usuario en la sesión tras autenticar con éxito.
     * regenera el identificador de sesión para mitigar ataques de fijación de sesión.
     */
    public static function login(array $usuario, array $perfil = []): void
    {
        self::initSession();

        // mitigación de session fixation
        session_regenerate_id(true);

        $_SESSION['is_authenticated'] = true;
        $_SESSION['user_id']          = (int)$usuario['id'];
        $_SESSION['usuario']          = $usuario['usuario'];
        $_SESSION['correo']           = $usuario['correo'];
        $_SESSION['id_rol']           = (int)$usuario['id_rol'];
        $_SESSION['rol']              = $usuario['rol_nombre'] ?? 'cliente';
        $_SESSION['nombre_mostrar']   = $perfil['nombre_mostrar'] ?? $usuario['usuario'];
        $_SESSION['perfil']           = $perfil;
        $_SESSION['logged_at']        = time();
    }

    /**
     * destruye la sesión activa y elimina las cookies asociadas
     */
    public static function logout(): void
    {
        self::initSession();

        $_SESSION = [];

        if (!headers_sent() && ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
        }
    }

    /**
     * comprueba si existe un usuario autenticado
     */
    public static function check(): bool
    {
        self::initSession();
        return !empty($_SESSION['is_authenticated']) && !empty($_SESSION['user_id']);
    }

    /**
     * retorna el id del usuario autenticado
     */
    public static function id(): ?int
    {
        self::initSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * retorna el rol del usuario autenticado
     */
    public static function role(): ?string
    {
        self::initSession();
        return $_SESSION['rol'] ?? null;
    }

    /**
     * retorna los datos de la sesión del usuario
     */
    public static function user(): ?array
    {
        self::initSession();
        if (!self::check()) {
            return null;
        }

        return [
            'id'             => $_SESSION['user_id'],
            'usuario'        => $_SESSION['usuario'],
            'correo'         => $_SESSION['correo'],
            'id_rol'         => $_SESSION['id_rol'],
            'rol'            => $_SESSION['rol'],
            'nombre_mostrar' => $_SESSION['nombre_mostrar'],
            'perfil'         => $_SESSION['perfil'] ?? [],
        ];
    }

    /**
     * verifica si el usuario autenticado tiene un rol específico
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!self::check()) {
            return false;
        }

        $userRole = self::role();
        if (is_array($roles)) {
            return in_array($userRole, $roles, true);
        }

        return $userRole === $roles;
    }

    public static function isCliente(): bool
    {
        return self::hasRole('cliente');
    }

    public static function isEmpresa(): bool
    {
        return self::hasRole('empresa');
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('administrador');
    }

    /**
     * exige que el usuario esté autenticado. si no lo está, redirige a login.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            Flash::warning('Debe iniciar sesión para acceder a esta sección.');
            header('Location: ' . url('/login'));
            exit;
        }
    }

    /**
     * exige que el usuario sea visitante (no autenticado).
     * si ya está autenticado, lo redirige a su panel respectivo.
     */
    public static function requireGuest(): void
    {
        if (self::check()) {
            self::redirectToDashboard();
        }
    }

    /**
     * exige que el usuario autenticado cuente con al menos uno de los roles indicados.
     */
    public static function requireRole(string|array $roles): void
    {
        self::requireAuth();

        if (!self::hasRole($roles) && !self::isAdmin()) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    /**
     * redirige al panel correspondiente según el rol del usuario
     */
    public static function redirectToDashboard(): void
    {
        $role = self::role();
        switch ($role) {
            case 'administrador':
                header('Location: ' . url('/admin/dashboard'));
                break;
            case 'empresa':
                header('Location: ' . url('/empresa/dashboard'));
                break;
            case 'cliente':
            default:
                header('Location: ' . url('/cliente/dashboard'));
                break;
        }
        exit;
    }
}
