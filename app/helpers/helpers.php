<?php
/**
 * ============================================================================
 * la cuponera sv - funciones globales de ayuda (helpers)
 * ============================================================================
 */

/**
 * escapa cadenas para prevenir ataques xss al generar html
 */
function e(?string $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * genera urls absolutas o relativas al contexto de ejecución (local, xampp o php server)
 */
function url(string $path = '/'): string
{
    $config = require __DIR__ . '/../config/config.php';
    $customBase = $config['app']['base_url'] ?? '';

    if (!empty($customBase)) {
        return rtrim($customBase, '/') . '/' . ltrim($path, '/');
    }

    // autodetección del subdirectorio en apache/xampp
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($dir === '.' || $dir === '/' || $dir === '\\') {
        $dir = '';
    }

    $cleanPath = '/' . ltrim($path, '/');
    return $dir . $cleanPath;
}

/**
 * retorna la ruta web a un archivo de recurso estático (css, js, imágenes)
 */
function asset(string $path): string
{
    return url('/' . ltrim($path, '/'));
}
