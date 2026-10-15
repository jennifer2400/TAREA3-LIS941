<?php
/**
 * ============================================================================
 * la cuponera sv - helper de mensajes flash
 * ============================================================================
 * permite almacenar y consumir mensajes temporales de sesión entre redirecciones
 * (ej. éxito en registro, errores de validación, confirmación de login/logout).
 */

class Flash
{
    /**
     * guarda un mensaje flash en sesión
     */
    public static function set(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }

        $_SESSION['flash_messages'][$type][] = $message;
    }

    /**
     * accesos rápidos para tipos comunes
     */
    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('error', $message);
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }

    /**
     * obtiene y elimina los mensajes del tipo especificado
     */
    public static function get(string $type): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $messages = $_SESSION['flash_messages'][$type] ?? [];
        unset($_SESSION['flash_messages'][$type]);

        return $messages;
    }

    /**
     * verifica si existen mensajes de un tipo determinado
     */
    public static function has(string $type): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        return !empty($_SESSION['flash_messages'][$type]);
    }

    /**
     * obtiene todos los mensajes flash y los limpia
     */
    public static function getAll(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $all = $_SESSION['flash_messages'] ?? [];
        $_SESSION['flash_messages'] = [];

        return $all;
    }
}
