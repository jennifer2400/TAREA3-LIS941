<?php
/**
 * ============================================================================
 * la cuponera sv - modelo cliente
 * ============================================================================
 * maneja los datos de los clientes individuales y el registro atómico transaccional.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Usuario.php';

class Cliente
{
    /**
     * verifica si un dui ya se encuentra registrado
     */
    public static function existsByDui(string $dui): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM clientes WHERE dui = :dui");
        $stmt->execute([':dui' => $dui]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * busca un cliente por su número de dui
     */
    public static function findByDui(string $dui): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT c.*, u.usuario, u.correo, u.estado 
                FROM clientes c
                INNER JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.dui = :dui
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':dui' => $dui]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * busca el registro de cliente asociado a un id de usuario
     */
    public static function findByUserId(int $userId): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT c.*, u.usuario, u.correo, u.estado, u.created_at AS cuenta_creada_en
                FROM clientes c
                INNER JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id_usuario = :id_usuario
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_usuario' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * registra atómicamente un nuevo usuario y su perfil de cliente mediante transacción pdo.
     * si ocurre algún error en cualquiera de las tablas, se revierte todo (rollback).
     * 
     * @param array $usuariodata [ 'usuario', 'correo', 'password_hash' ]
     * @param array $clientedata [ 'nombres', 'apellidos', 'dui', 'fecha_nacimiento', 'telefono', 'direccion' ]
     * @return int id del cliente recién creado
     * @throws exception si ocurre un fallo en la transacción
     */
    public static function registrarCliente(array $usuarioData, array $clienteData): int
    {
        $db = Database::getConnection();

        $db->beginTransaction();

        try {
            // asignar rol de cliente (id_rol = 2)
            $usuarioData['id_rol'] = 2;
            $usuarioData['estado'] = 'activo';

            $userId = Usuario::create($usuarioData, $db);

            $sql = "INSERT INTO clientes (id_usuario, nombres, apellidos, dui, fecha_nacimiento, telefono, direccion)
                    VALUES (:id_usuario, :nombres, :apellidos, :dui, :fecha_nacimiento, :telefono, :direccion)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_usuario'        => $userId,
                ':nombres'           => $clienteData['nombres'],
                ':apellidos'         => $clienteData['apellidos'],
                ':dui'               => $clienteData['dui'],
                ':fecha_nacimiento'  => $clienteData['fecha_nacimiento'],
                ':telefono'          => $clienteData['telefono'] ?? null,
                ':direccion'         => $clienteData['direccion'] ?? null,
            ]);

            $clienteId = (int)$db->lastInsertId();

            $db->commit();
            return $clienteId;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
