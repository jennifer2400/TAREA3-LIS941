<?php
/**
 * ============================================================================
 * la cuponera sv - front controller & enrutador principal
 * ============================================================================
 * punto de entrada único de la aplicación web. inicializa sesiones seguras,
 * carga la configuración y despacha las peticiones http hacia los controladores.
 */

declare(strict_types=1);

// 1. carga de configuración y funciones auxiliares
$config = require_once __DIR__ . '/../app/config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'America/El_Salvador');

if (!empty($config['app']['debug'])) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 2. carga de helpers del núcleo
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/helpers/Flash.php';
require_once __DIR__ . '/../app/helpers/Csrf.php';
require_once __DIR__ . '/../app/helpers/Validator.php';
require_once __DIR__ . '/../app/helpers/Auth.php';

// iniciar sesión con directivas de seguridad
Auth::initSession();

// 3. resolución de la ruta solicitada
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$route = '/';

if (!empty($_GET['route'])) {
    // modo de enrutamiento por parámetro (fallback de alta compatibilidad)
    $route = '/' . ltrim($_GET['route'], '/');
} else {
    // modo path_info o request_uri limpio
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

    // remover el subdirectorio base si se ejecuta dentro de xampp / subcarpetas
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir !== '/' && $scriptDir !== '.' && !empty($scriptDir) && str_starts_with($path, $scriptDir)) {
        $path = substr($path, strlen($scriptDir));
    }

    // remover index.php si estuviera en la url
    if (str_starts_with($path, '/index.php')) {
        $path = substr($path, strlen('/index.php'));
    }

    $route = '/' . trim($path, '/');
}

// normalizar la ruta raíz
if ($route === '' || $route === '//') {
    $route = '/';
}

// 4. tabla de enrutamiento
try {
    switch ($route) {
        // --- página pública de inicio ---
        case '/':
            require_once __DIR__ . '/../app/controllers/HomeController.php';
            (new HomeController())->index();
            break;

        // --- inicio de sesión ---
        case '/login':
            require_once __DIR__ . '/../app/controllers/AuthController.php';
            $authController = new AuthController();
            if ($requestMethod === 'POST') {
                $authController->login();
            } else {
                $authController->showLogin();
            }
            break;

        // --- cierre de sesión ---
        case '/logout':
            require_once __DIR__ . '/../app/controllers/AuthController.php';
            (new AuthController())->logout();
            break;

        // --- registro de clientes ---
        case '/registro':
            require_once __DIR__ . '/../app/controllers/AuthController.php';
            $authController = new AuthController();
            if ($requestMethod === 'POST') {
                $authController->registro();
            } else {
                $authController->showRegistro();
            }
            break;

        // --- confirmación de registro ---
        case '/registro/exitoso':
            require_once __DIR__ . '/../app/controllers/AuthController.php';
            (new AuthController())->showRegistroExitoso();
            break;

        // --- recuperación de contraseña (base técnica compartida) ---
        case '/recuperar-password':
            require_once __DIR__ . '/../app/controllers/PasswordResetController.php';
            $resetController = new PasswordResetController();
            if ($requestMethod === 'POST') {
                $resetController->processRequest();
            } else {
                $resetController->showRequestForm();
            }
            break;

        // --- paneles privados según rol ---
        case '/cliente/dashboard':
        case '/cliente':
            require_once __DIR__ . '/../app/controllers/ClienteController.php';
            (new ClienteController())->dashboard();
            break;

        case '/empresa/dashboard':
        case '/empresa':
            require_once __DIR__ . '/../app/controllers/EmpresaController.php';
            (new EmpresaController())->dashboard();
            break;

        case '/admin/dashboard':
        case '/admin':
            require_once __DIR__ . '/../app/controllers/AdminController.php';
            (new AdminController())->dashboard();
            break;

        // --- ruta no encontrada (404) ---
        default:
            http_response_code(404);
            require __DIR__ . '/../app/views/errors/404.php';
            break;
    }
} catch (Throwable $e) {
    error_log('Error no capturado en la aplicación: ' . $e->getMessage());
    if (!empty($config['app']['debug'])) {
        http_response_code(500);
        echo '<div style="font-family:sans-serif;padding:2rem;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;margin:2rem;">';
        echo '<h2>Error 500 — Excepción en la Aplicación</h2>';
        echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . ' (Línea ' . $e->getLine() . ')</p>';
        echo '<pre style="background:#fff;padding:1rem;border-radius:4px;overflow:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        http_response_code(500);
        echo '<h1>Error 500 — Servicio no disponible temporalmente</h1>';
    }
}
