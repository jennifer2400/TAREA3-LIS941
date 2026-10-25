<?php
require_once __DIR__ . '/../config/database.php';
class CanjeController {
    public function index(): void {
        Auth::requireRole('empresa');
        $db=Database::getConnection();
        $empresas=Auth::isAdmin()?$db->query("SELECT id,nombre_comercial FROM empresas WHERE estado_aprobacion='aprobada' ORDER BY nombre_comercial")->fetchAll():[];
        $st=$db->prepare("SELECT c.codigo_cupon,c.estado,c.fecha_compra,c.fecha_canje,p.titulo,p.fecha_limite_canje FROM cupones_comprados c JOIN promociones p ON p.id=c.id_promocion JOIN empresas e ON e.id=p.id_empresa WHERE (?=1 OR e.id_usuario=?) ORDER BY c.fecha_compra DESC LIMIT 100");
        $st->execute([Auth::isAdmin()?1:0,Auth::id()]);
        $cupones=$st->fetchAll();
        require __DIR__.'/../views/empresa/canjes.php';
    }
    public function canjear(): void {
        Auth::requireRole('empresa');
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {http_response_code(403);exit('Solicitud inválida');}
        $codigo=strtoupper(trim((string)($_POST['codigo_cupon'] ?? '')));
        if (!preg_match('/^SV-[A-F0-9]{16}$/',$codigo)) {Flash::error('Código de cupón inválido.');header('Location: '.url('/empresa/canjes'));exit;}
        $db=Database::getConnection();
        try {
            $db->beginTransaction();
            $st=$db->prepare("SELECT c.id,c.estado,p.fecha_limite_canje,e.estado_aprobacion FROM cupones_comprados c JOIN promociones p ON p.id=c.id_promocion JOIN empresas e ON e.id=p.id_empresa WHERE c.codigo_cupon=? AND ((?=1 AND e.id=?) OR (?=0 AND e.id_usuario=?)) FOR UPDATE");
            $admin=Auth::isAdmin()?1:0;$st->execute([$codigo,$admin,(int)($_POST['id_empresa']??0),$admin,Auth::id()]);$c=$st->fetch();
            if (!$c) throw new RuntimeException('Cupón inexistente o pertenece a otra empresa.');
            if ($c['estado']!=='disponible') throw new RuntimeException('El cupón ya fue canjeado o está vencido.');
            if ($c['fecha_limite_canje'] < date('Y-m-d')) throw new RuntimeException('El cupón superó su fecha límite de canje.');
            if ($c['estado_aprobacion']!=='aprobada') throw new RuntimeException('Empresa no aprobada.');
            $st=$db->prepare("UPDATE cupones_comprados SET estado='canjeado', fecha_canje=NOW() WHERE id=? AND estado='disponible'");
            $st->execute([(int)$c['id']]);if ($st->rowCount()!==1) throw new RuntimeException('No pudo registrarse el canje.');
            $db->commit();Flash::success('Cupón canjeado correctamente.');
        } catch(Throwable $e){if($db->inTransaction())$db->rollBack();Flash::error($e->getMessage());}
        header('Location: '.url('/empresa/canjes'));exit;
    }
}
