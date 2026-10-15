<?php
/**
 * ============================================================================
 * la cuponera sv - archivo de configuración de ejemplo
 * ============================================================================
 * copia este archivo como 'config.php' y ajusta las credenciales locales.
 * este archivo no contiene contraseñas reales y es seguro versionarlo en git.
 */

return [
    'app' => [
        'name'        => 'La Cuponera SV',
        'env'         => 'development', // 'development' o 'production'
        'debug'       => true,
        // url base: puedes dejarla vacía para autodetección o especificarla
        // ejemplos: 'http://localhost/tarea3/public' o 'http://localhost:8000'
        'base_url'    => '',
        'timezone'    => 'America/El_Salvador',
    ],

    'database' => [
        'host'     => '127.0.0.1',
        'port'     => 3307,          // 3306 para instalación estándar, 3307 en xampp con mysql secundario
        'dbname'   => 'lacuponera_sv',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'name'            => 'LACUPONERA_SESSION',
        'lifetime'        => 7200,   // 2 horas en segundos
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure'   => false,  // cambiar a true en producción con https
    ],

    'security' => [
        'bcrypt_cost'       => 10,
        'token_expiration'  => 3600, // 1 hora para recuperación de contraseñas
        'min_age_years'     => 18,   // mayoría de edad requerida para clientes
    ],
];
