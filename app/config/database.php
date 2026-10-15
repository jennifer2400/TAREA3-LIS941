<?php
/**
 * ============================================================================
 * la cuponera sv - clase de conexión pdo a base de datos
 * ============================================================================
 * implementa el patrón singleton para reutilizar una única instancia de conexión
 * pdo durante el ciclo de vida de la petición http.
 */

class Database
{
    private static ?PDO $instance = null;

    /**
     * obtiene la conexión pdo singleton
     * 
     * @return pdo
     * @throws runtimeexception si no es posible establecer la conexión
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/config.php';
            $db = $config['database'];

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['dbname'],
                $db['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, $db['username'], $db['password'], $options);
            } catch (PDOException $e) {
                // registro interno sin exponer contraseñas al usuario final
                error_log('Error de conexión a la base de datos: ' . $e->getMessage());

                $debug = $config['app']['debug'] ?? false;
                if ($debug) {
                    throw new RuntimeException('No se pudo conectar a la base de datos (' . $db['host'] . ':' . $db['port'] . '): ' . $e->getMessage());
                } else {
                    throw new RuntimeException('El servicio no se encuentra disponible temporalmente. Intente más tarde.');
                }
            }
        }

        return self::$instance;
    }

    /**
     * evita la clonación de la instancia
     */
    private function __construct() {}
    private function __clone() {}
}
