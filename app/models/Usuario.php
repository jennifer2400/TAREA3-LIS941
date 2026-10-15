<?php
/**
 * ============================================================================
 * la cuponera sv - modelo usuario
 * ============================================================================
 * gestiona la autenticación, credenciales y consultas del núcleo de usuarios.
 */

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    /**
     * busca un usuario por su nombre de usuario o por su correo electrónico.
     * incluye el nombre del rol asociado.
     */
    public static function findByIdentifier(string $identifier): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT u.id, u.id_rol, u.usuario, u.correo, u.password_hash, u.estado, 
                       u.created_at, r.nombre AS rol_nombre, r.descripcion AS rol_descripcion
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id
                WHERE u.usuario = :id_user OR u.correo = :id_mail
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id_user' => $identifier,
            ':id_mail' => $identifier,
        ]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * busca un usuario por id primario
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $sql = "SELECT u.id, u.id_rol, u.usuario, u.correo, u.password_hash, u.estado, 
                       u.created_at, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * verifica si un nombre de usuario ya se encuentra registrado
     */
    public static function existsByUsername(string $username): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $username]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * verifica si un correo electrónico ya se encuentra registrado
     */
    public static function existsByEmail(string $email): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = :correo");
        $stmt->execute([':correo' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * crea un usuario con rol específico
     */
    public static function create(array $data, ?PDO $pdo = null): int
    {
        $db = $pdo ?? Database::getConnection();

        $sql = "INSERT INTO usuarios (id_rol, usuario, correo, password_hash, estado) 
                VALUES (:id_rol, :usuario, :correo, :password_hash, :estado)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id_rol'        => $data['id_rol'],
            ':usuario'       => $data['usuario'],
            ':correo'        => $data['correo'],
            ':password_hash' => $data['password_hash'],
            ':estado'        => $data['estado'] ?? 'activo',
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * actualiza la contraseña hasheada de un usuario (base para recuperación)
     */
    public static function updatePassword(int $userId, string $newPasswordHash): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET password_hash = :hash, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            ':hash' => $newPasswordHash,
            ':id'   => $userId
        ]);
    }
}
