<?php
/**
 * ============================================================================
 * la cuponera sv - configuración del entorno local
 * ============================================================================
 */

return [
    'app' => [
        'name'        => 'La Cuponera SV',
        'env'         => 'development',
        'debug'       => true,
        'base_url'    => '', // autodetectado por defecto
        'timezone'    => 'America/El_Salvador',
    ],

    'database' => [
        'host'     => '127.0.0.1',
        'port'     => 3307,
        'dbname'   => 'lacuponera_sv',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'name'            => 'LACUPONERA_SESSION',
        'lifetime'        => 7200,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure'   => false,
    ],

    'security' => [
        'bcrypt_cost'       => 10,
        'token_expiration'  => 3600,
        'min_age_years'     => 18,
    ],
];
