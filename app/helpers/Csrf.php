<?php
/**
 * ============================================================================
 * la cuponera sv - helper de protección csrf
 * ============================================================================
 * previene ataques de falsificación de peticiones en sitios cruzados (csrf)
 * generando y validando tokens criptográficos seguros por sesión.
 */

class Csrf
{
    /**
     * obtiene el token csrf actual o genera uno nuevo si no existe
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * genera un nuevo token forzando su rotación
     */
    public static function regenerate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * valida si el token enviado coincide exactamente con el de la sesión
     */
    public static function validate(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * renderiza un campo oculto html para incrustar en formularios
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
