<?php
/**
 * ============================================================================
 * la cuponera sv - modelo empresa (esqueleto compartido)
 * ============================================================================
 * estructura inicial para empresas. permite autenticar y cargar el perfil de
 * empresas registradas y sirve como base de integración para los módulos de
 * administración y publicación de ofertas.
 */

require_once __DIR__ . '/../config/database.php';

class Empresa
{
    /**
     * busca los datos de la empresa por su id de usuario en sesión
     */
    public static function findByUserId(int $userId): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT e.*, rub.nombre AS rubro_nombre, u.usuario, u.correo, u.estado AS usuario_estado,
                       u.created_at AS cuenta_creada_en
                FROM empresas e
                INNER JOIN usuarios u ON e.id_usuario = u.id
                LEFT JOIN rubros rub ON e.id_rubro = rub.id
                WHERE e.id_usuario = :id_usuario
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_usuario' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * busca una empresa por su nit
     */
    public static function findByNit(string $nit): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM empresas WHERE nit = :nit LIMIT 1");
        $stmt->execute([':nit' => $nit]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * verifica si un nit ya se encuentra registrado
     */
    public static function existsByNit(string $nit): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM empresas WHERE nit = :nit");
        $stmt->execute([':nit' => $nit]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * verifica si la empresa cuenta con aprobación para operar
     */
    public static function isApproved(int $userId): bool
    {
        $empresa = self::findByUserId($userId);
        return $empresa && ($empresa['estado_aprobacion'] === 'aprobada');
    }

    /**
     * punto de integración para el administrador: actualizar estado de aprobación
     * (reservado para desarrollo del compañero de módulo administrativo)
     */
    public static function updateApprovalStatus(int $empresaId, string $status): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE empresas SET estado_aprobacion = :estado, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            ':estado' => $status,
            ':id'     => $empresaId
        ]);
    }
}
