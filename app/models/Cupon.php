<?php
require_once __DIR__ . '/../config/database.php';
class Cupon {
 public static function db(): PDO {return Database::getConnection();}
 public static function disponibles(): array {
  return self::db()->query("SELECT p.*, e.nombre_comercial, (SELECT COUNT(*) FROM cupones_comprados c WHERE c.id_promocion=p.id) vendidos FROM promociones p JOIN empresas e ON e.id=p.id_empresa JOIN usuarios u ON u.id=e.id_usuario WHERE p.estado='activa' AND e.estado_aprobacion='aprobada' AND u.estado='activo' AND CURRENT_DATE BETWEEN p.fecha_inicio AND p.fecha_fin AND (p.cantidad_limite IS NULL OR (SELECT COUNT(*) FROM cupones_comprados c WHERE c.id_promocion=p.id)<p.cantidad_limite) ORDER BY p.created_at DESC")->fetchAll();
 }
 public static function clienteId(int $uid): ?int {$s=self::db()->prepare('SELECT id FROM clientes WHERE id_usuario=?');$s->execute([$uid]);$v=$s->fetchColumn();return $v?(int)$v:null;}
 public static function todosCupones():array {return self::db()->query('SELECT c.*,p.titulo,p.precio_oferta,p.fecha_limite_canje,e.nombre_comercial,CONCAT(cl.nombres," ",cl.apellidos) AS cliente FROM cupones_comprados c JOIN promociones p ON p.id=c.id_promocion JOIN empresas e ON e.id=p.id_empresa JOIN clientes cl ON cl.id=c.id_cliente ORDER BY c.fecha_compra DESC')->fetchAll();}
 public static function todasFacturas():array {return self::db()->query('SELECT f.*,c.codigo_cupon,p.titulo,CONCAT(cl.nombres," ",cl.apellidos) AS cliente FROM facturas f JOIN cupones_comprados c ON c.id=f.id_cupon JOIN promociones p ON p.id=c.id_promocion JOIN clientes cl ON cl.id=c.id_cliente ORDER BY f.fecha_emision DESC')->fetchAll();}
 public static function facturaAdmin(int $id):?array {$s=self::db()->prepare('SELECT f.*,c.codigo_cupon,p.titulo,cl.nombres,cl.apellidos FROM facturas f JOIN cupones_comprados c ON c.id=f.id_cupon JOIN promociones p ON p.id=c.id_promocion JOIN clientes cl ON cl.id=c.id_cliente WHERE f.id=?');$s->execute([$id]);return $s->fetch()?:null;}
 public static function misCupones(int $uid):array {$s=self::db()->prepare('SELECT c.*,p.titulo,p.precio_oferta,p.fecha_limite_canje,e.nombre_comercial FROM cupones_comprados c JOIN promociones p ON p.id=c.id_promocion JOIN empresas e ON e.id=p.id_empresa JOIN clientes cl ON cl.id=c.id_cliente WHERE cl.id_usuario=? ORDER BY c.fecha_compra DESC');$s->execute([$uid]);return $s->fetchAll();}
 public static function misFacturas(int $uid):array {$s=self::db()->prepare('SELECT f.*,c.codigo_cupon,p.titulo FROM facturas f JOIN cupones_comprados c ON c.id=f.id_cupon JOIN promociones p ON p.id=c.id_promocion JOIN clientes cl ON cl.id=c.id_cliente WHERE cl.id_usuario=? ORDER BY f.fecha_emision DESC');$s->execute([$uid]);return $s->fetchAll();}
 public static function factura(int $id,int $uid):?array {$s=self::db()->prepare('SELECT f.*,c.codigo_cupon,p.titulo,cl.nombres,cl.apellidos FROM facturas f JOIN cupones_comprados c ON c.id=f.id_cupon JOIN promociones p ON p.id=c.id_promocion JOIN clientes cl ON cl.id=c.id_cliente WHERE f.id=? AND cl.id_usuario=?');$s->execute([$id,$uid]);return $s->fetch()?:null;}
 public static function comprar(int $pid,int $uid):int {
  $db=self::db();$db->beginTransaction();
  try {
   $s=$db->prepare('SELECT p.* FROM promociones p JOIN empresas e ON e.id=p.id_empresa JOIN usuarios u ON u.id=e.id_usuario WHERE p.id=? AND p.estado=\'activa\' AND e.estado_aprobacion=\'aprobada\' AND u.estado=\'activo\' AND CURRENT_DATE BETWEEN p.fecha_inicio AND p.fecha_fin FOR UPDATE');$s->execute([$pid]);$p=$s->fetch();if(!$p)throw new RuntimeException('La promoción no está disponible.');
   $cl=self::clienteId($uid);if(!$cl)throw new RuntimeException('No existe perfil de cliente.');
   $s=$db->prepare('SELECT COUNT(*) FROM cupones_comprados WHERE id_promocion=?');$s->execute([$pid]);if($p['cantidad_limite']!==null && (int)$s->fetchColumn()>=(int)$p['cantidad_limite'])throw new RuntimeException('Se agotaron los cupones.');
   $s=$db->prepare('SELECT COUNT(*) FROM cupones_comprados WHERE id_cliente=? AND id_promocion=?');$s->execute([$cl,$pid]);if((int)$s->fetchColumn()>=5)throw new RuntimeException('Máximo 5 cupones de la misma oferta por cliente.');
   $codigo='SV-'.strtoupper(bin2hex(random_bytes(8)));
   $s=$db->prepare('INSERT INTO cupones_comprados(id_promocion,id_cliente,codigo_cupon) VALUES (?,?,?)');$s->execute([$pid,$cl,$codigo]);$cid=(int)$db->lastInsertId();
   $numero='FAC-'.date('Ymd').'-'.str_pad((string)$cid,7,'0',STR_PAD_LEFT);
   $s=$db->prepare('INSERT INTO facturas(id_cupon,numero_factura,total,metodo_pago) VALUES (?,?,?,?)');$s->execute([$cid,$numero,$p['precio_oferta'],'simulacion']);$fid=(int)$db->lastInsertId();$db->commit();return $fid;
  } catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
 }
}
