<?php
/** Punto de entrada para Apache / XAMPP. */
// Mantener esta comprobación compatible incluso con versiones antiguas de PHP.
if (PHP_VERSION_ID < 80000) {
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    exit('<h2>La Cuponera SV requiere PHP 8.0 o superior.</h2>'
       . '<p>La version instalada es ' . htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') . '.</p>'
       . '<p>Actualiza XAMPP a una version con PHP 8.0 o superior y reinicia Apache.</p>');
}
require_once __DIR__ . '/helpers/helpers.php';
require_once __DIR__ . '/helpers/Auth.php';
require_once __DIR__ . '/helpers/Flash.php';
require_once __DIR__ . '/helpers/Csrf.php';
require_once __DIR__ . '/helpers/Validator.php';

if (!is_file(__DIR__ . '/config/config.php')) {
    exit('Falta config/config.php. Copia config/config.example.php a config/config.php');
}
$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'America/El_Salvador');
ini_set('display_errors', !empty($config['app']['debug']) ? '1' : '0');
Auth::initSession();

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$base = ($base === '.' || $base === '/') ? '' : rtrim($base, '/');
if ($base !== '' && ($path === $base || substr($path, 0, strlen($base) + 1) === $base . '/')) {
    $path = substr($path, strlen($base)) ?: '/';
}
$path = '/' . trim(rawurldecode($path), '/');
if ($path === '//') $path = '/';

$routes = [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/login' => ['AuthController', 'showLogin'],
        '/registro' => ['AuthController', 'showRegistro'],
        '/registro-empresa' => ['RegistroEmpresaController', 'formulario'],
        '/registro-empresa/exito' => ['RegistroEmpresaController', 'exito'],
        '/admin/empresas' => ['AprobacionEmpresaController', 'index'],
        '/restablecer-password' => ['PasswordResetController', 'showResetForm'],
        '/registro/exitoso' => ['AuthController', 'showRegistroExitoso'],
        '/recuperar-password' => ['PasswordResetController', 'showRequestForm'],
        '/cliente/dashboard' => ['ClienteController', 'dashboard'],
        '/empresa/dashboard' => ['EmpresaController', 'dashboard'],
        '/admin/dashboard' => ['AdminController', 'dashboard'],
        '/ofertas' => ['CuponController', 'ofertas'],
        '/comprar' => ['CuponController', 'comprarFormulario'],
        '/cliente/cupones' => ['CuponController', 'misCupones'],
        '/cliente/facturas' => ['CuponController', 'misFacturas'],
        '/cliente/factura' => ['CuponController', 'factura'],
        '/empresa/promociones' => ['PromocionController', 'index'],
        '/empresa/promociones/nueva' => ['PromocionController', 'formulario'],
        '/empresa/canjes' => ['CanjeController','index'],
        '/admin/reportes' => ['ReporteController', 'index'],
        '/admin/reportes/cupones' => ['ReporteController', 'cupones'],
        '/admin/reportes/ganancias' => ['ReporteController', 'ganancias'],
        '/admin/administradores' => ['AdministradorController', 'index'],
        '/admin/usuarios' => ['UsuariosController', 'index'],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/registro' => ['AuthController', 'registro'],
        '/registro-empresa' => ['RegistroEmpresaController', 'guardar'],
        '/admin/empresas/aprobar' => ['AprobacionEmpresaController', 'aprobar'],
        '/restablecer-password' => ['PasswordResetController', 'resetPassword'],
        '/recuperar-password' => ['PasswordResetController', 'processRequest'],
        '/logout' => ['AuthController', 'logout'],
        '/comprar' => ['CuponController', 'comprar'],
        '/empresa/promociones/nueva' => ['PromocionController', 'guardar'],
        '/empresa/canjes' => ['CanjeController','canjear'],
        '/admin/administradores' => ['AdministradorController', 'guardar'],
    ],
];

if ($path === '/logout' && $method === 'GET') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
// Inicio privado: la portada comercial solo es para visitantes.
// Resolverlo en el enrutador impide que un HomeController antiguo
// vuelva a presentar los botones de registro a un usuario autenticado.
if ($method === 'GET' && $path === '/' && Auth::check()) {
    $role = Auth::role();
    if ($role === 'administrador') {
        $path = '/admin/dashboard';
    } elseif ($role === 'empresa') {
        $path = '/empresa/dashboard';
    } elseif ($role === 'cliente') {
        $path = '/cliente/dashboard';
    } else {
        http_response_code(403);
        exit('Rol de usuario no reconocido.');
    }
}
$handler = $routes[$method][$path] ?? null;
if (!$handler) {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    exit;
}
[$class, $action] = $handler;
require_once __DIR__ . '/controllers/' . $class . '.php';
(new $class())->$action();
