<?php
require_once __DIR__ . '/../config/database.php';
class PasswordReset {
    public static function createResetToken(int $userId, int $seconds = 3600): string {
        $token=bin2hex(random_bytes(32));
        $db=Database::getConnection();
        $db->prepare('DELETE FROM recuperacion_tokens WHERE id_usuario=?')->execute([$userId]);
        $st=$db->prepare('INSERT INTO recuperacion_tokens (id_usuario,token_hash,expira_en,usado) VALUES (?,?,DATE_ADD(NOW(), INTERVAL ? SECOND),0)');
        $st->execute([$userId,hash('sha256',$token),$seconds]);
        return $token;
    }
    public static function findValid(string $token): ?array {
        if (!preg_match('/^[a-f0-9]{64}$/D',$token)) return null;
        $st=Database::getConnection()->prepare('SELECT id,id_usuario FROM recuperacion_tokens WHERE token_hash=? AND usado=0 AND expira_en > NOW() LIMIT 1');
        $st->execute([hash('sha256',$token)]);
        return $st->fetch() ?: null;
    }
    public static function consumeAndUpdate(string $token, string $hash): bool {
        if (!preg_match('/^[a-f0-9]{64}$/D',$token)) return false;
        $db=Database::getConnection();
        try {
            $db->beginTransaction();
            $st=$db->prepare('SELECT id,id_usuario FROM recuperacion_tokens WHERE token_hash=? AND usado=0 AND expira_en>NOW() LIMIT 1 FOR UPDATE');
            $st->execute([hash('sha256',$token)]);
            $row=$st->fetch();
            if (!$row) {$db->rollBack();return false;}
            $st=$db->prepare('UPDATE usuarios SET password_hash=?, updated_at=NOW() WHERE id=?');
            $st->execute([$hash,$row['id_usuario']]);
            if ($st->rowCount()<1) {$db->rollBack();return false;}
            $db->prepare('UPDATE recuperacion_tokens SET usado=1 WHERE id=?')->execute([$row['id']]);
            $db->prepare('DELETE FROM recuperacion_tokens WHERE id_usuario=? AND id<>?')->execute([$row['id_usuario'],$row['id']]);
            $db->commit();return true;
        } catch(Throwable $e) {if($db->inTransaction())$db->rollBack();throw $e;}
    }
}
