<?php
/**
 * ============================================================================
 * la cuponera sv - modelo recuperación de contraseña (base técnica)
 * ============================================================================
 * provee la estructura criptográfica y de almacenamiento en base de datos
 * para la recuperación de contraseñas mediante tokens seguros.
 * 
 * nota de alcance:
 * este modelo define la base de datos y métodos técnicos compartidos.
 * el flujo completo con envío de correos smtp y formulario de restablecimiento
 * corresponde a la integración de los compañeros según los requerimientos.
 */

require_once __DIR__ . '/../config/database.php';

class PasswordReset
{
    /**
     * genera un token criptográfico seguro, almacena su hash sha-256 en la
     * base de datos con tiempo de expiración y retorna el token en texto claro
     * para que el servicio de correo lo despache.
     * 
     * @param int $userid id del usuario
     * @param int $lifetimeseconds tiempo de validez (por defecto 3600 segundos = 1 hora)
     * @return string token en texto plano para el enlace
     */
    public static function createResetToken(int $userId, int $lifetimeSeconds = 3600): string
    {
        $db = Database::getConnection();

        // token aleatorio criptográficamente seguro de 32 bytes (64 caracteres hex)
        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $expiresAt = date('Y-m-d H:i:s', time() + $lifetimeSeconds);

        // invalidar tokens previos no utilizados para este usuario
        $stmtInvalidate = $db->prepare("UPDATE recuperacion_tokens SET usado = 1 WHERE id_usuario = :id_usuario AND usado = 0");
        $stmtInvalidate->execute([':id_usuario' => $userId]);

        // insertar nuevo token protegido
        $stmt = $db->prepare("INSERT INTO recuperacion_tokens (id_usuario, token_hash, expira_en, usado) 
                              VALUES (:id_usuario, :token_hash, :expira_en, 0)");
        $stmt->execute([
            ':id_usuario' => $userId,
            ':token_hash' => $tokenHash,
            ':expira_en'  => $expiresAt,
        ]);

        return $rawToken;
    }

    /**
     * valida un token recibido en el enlace de recuperación.
     * retorna los datos del token y del usuario si es válido y no ha expirado.
     */
    public static function verifyToken(string $rawToken): ?array
    {
        $db = Database::getConnection();
        $tokenHash = hash('sha256', $rawToken);

        $sql = "SELECT rt.*, u.usuario, u.correo 
                FROM recuperacion_tokens rt
                INNER JOIN usuarios u ON rt.id_usuario = u.id
                WHERE rt.token_hash = :hash 
                  AND rt.usado = 0 
                  AND rt.expira_en > NOW()
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':hash' => $tokenHash]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * marca un token como consumido tras restablecer la contraseña exitosamente
     */
    public static function markAsUsed(int $tokenId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE recuperacion_tokens SET usado = 1 WHERE id = :id");
        return $stmt->execute([':id' => $tokenId]);
    }
}
