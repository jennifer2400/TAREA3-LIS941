<?php
require_once __DIR__ . '/../config/database.php';
class AprobacionEmpresaController {
    public function index(): void {
        Auth::requireRole('administrador');
        $empresas=Database::getConnection()->query("SELECT e.id,e.nombre_comercial,e.razon_social,e.nit,e.estado_aprobacion,e.created_at,r.nombre rubro,u.correo FROM empresas e JOIN usuarios u ON u.id=e.id_usuario LEFT JOIN rubros r ON r.id=e.id_rubro ORDER BY FIELD(e.estado_aprobacion,'pendiente','rechazada','aprobada'), e.created_at DESC")->fetchAll();
        require __DIR__ . '/../views/admin/empresas.php';
    }
    public function aprobar(): void {
        Auth::requireRole('administrador');
        if (!Csrf::validate($_POST['csrf_token']??'')) {http_response_code(403);exit('Solicitud inválida.');}
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        $estado=$_POST['estado']??'';
        $comision=filter_var($_POST['comision_porcentaje']??null,FILTER_VALIDATE_FLOAT);
        if (!$id || !in_array($estado,['aprobada','rechazada'],true) || ($estado==='aprobada' && ($comision===false || $comision<0 || $comision>100))) {Flash::error('Solicitud inválida.');header('Location: '.url('/admin/empresas'));exit;}
        $db=Database::getConnection();
        $st=$db->prepare("UPDATE empresas SET estado_aprobacion=?, comision_porcentaje=?, updated_at=NOW() WHERE id=? AND estado_aprobacion='pendiente'");
        $st->execute([$estado,$estado==='aprobada'?$comision:0,$id]);
        if ($st->rowCount()) Flash::success('Solicitud actualizada a '.$estado.'.');
        else Flash::warning('La empresa ya fue revisada o no existe.');
        header('Location: '.url('/admin/empresas'));exit;
    }
}
