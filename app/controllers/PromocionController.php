<?php
require_once __DIR__.'/../config/database.php';
class PromocionController {
 private function db():PDO{return Database::getConnection();}
 private function empresa(?int $id=null):int {
  $admin=Auth::isAdmin();
  if($admin && !$id){http_response_code(422);throw new RuntimeException('Selecciona una empresa aprobada.');}
  $sql=$admin?"SELECT id FROM empresas WHERE id=? AND estado_aprobacion='aprobada'":"SELECT id FROM empresas WHERE id_usuario=? AND estado_aprobacion='aprobada'";
  $s=$this->db()->prepare($sql);$s->execute([$admin?$id:Auth::id()]);$found=$s->fetchColumn();
  if(!$found){http_response_code(403);throw new RuntimeException('Empresa inexistente o pendiente de aprobación.');}
  return (int)$found;
 }
 public function index():void {
  Auth::requireRole('empresa');
  if(Auth::isAdmin()){$promociones=$this->db()->query('SELECT p.*,e.nombre_comercial FROM promociones p JOIN empresas e ON e.id=p.id_empresa ORDER BY p.id DESC')->fetchAll();}
  else {$s=$this->db()->prepare('SELECT p.*,e.nombre_comercial FROM promociones p JOIN empresas e ON e.id=p.id_empresa WHERE e.id_usuario=? ORDER BY p.id DESC');$s->execute([Auth::id()]);$promociones=$s->fetchAll();}
  require __DIR__.'/../views/empresa/promociones.php';
 }
 public function formulario():void {
  Auth::requireRole('empresa');
  if(Auth::isAdmin()){$empresas=$this->db()->query("SELECT id,nombre_comercial FROM empresas WHERE estado_aprobacion='aprobada' ORDER BY nombre_comercial")->fetchAll();}
  else {$this->empresa();$empresas=[];}
  require __DIR__.'/../views/empresa/promocion_form.php';
 }
 public function guardar():void {
  Auth::requireRole('empresa');
  if(!Csrf::validate($_POST['csrf_token']??'')){http_response_code(403);exit('Solicitud inválida');}
  $titulo=trim($_POST['titulo']??'');$desc=trim($_POST['descripcion']??'');
  $regular=filter_var($_POST['precio_regular']??null,FILTER_VALIDATE_FLOAT);$oferta=filter_var($_POST['precio_oferta']??null,FILTER_VALIDATE_FLOAT);
  $inicio=$_POST['fecha_inicio']??'';$fin=$_POST['fecha_fin']??'';$canje=$_POST['fecha_limite_canje']??'';$cant=trim((string)($_POST['cantidad_limite']??''));$estado=$_POST['estado']??'activa';
  $fechaValida=static function($s){$d=DateTimeImmutable::createFromFormat('!Y-m-d',$s);return $d&&$d->format('Y-m-d')===$s;};
  if($titulo===''||mb_strlen($titulo)>150||$desc===''||$regular===false||$oferta===false||$regular<=0||$oferta<=0||$oferta>=$regular||!$fechaValida($inicio)||!$fechaValida($fin)||!$fechaValida($canje)||$fin<$inicio||$canje<$fin||($cant!==''&&(!ctype_digit($cant)||(int)$cant<1))||!in_array($estado,['activa','pausada'],true)){
   Flash::error('Revisa precios, cantidades y fechas.');header('Location: '.url('/empresa/promociones/nueva'));exit;
  }
  try{$empresa=$this->empresa((int)($_POST['id_empresa']??0));
   $s=$this->db()->prepare('INSERT INTO promociones (id_empresa,titulo,descripcion,precio_regular,precio_oferta,fecha_inicio,fecha_fin,fecha_limite_canje,cantidad_limite,estado) VALUES (?,?,?,?,?,?,?,?,?,?)');
   $s->execute([$empresa,$titulo,$desc,$regular,$oferta,$inicio,$fin,$canje,$cant===''?null:(int)$cant,$estado]);
   Flash::success('Oferta registrada.');header('Location: '.url('/empresa/promociones'));exit;
  }catch(RuntimeException $e){Flash::error($e->getMessage());header('Location: '.url('/empresa/promociones/nueva'));exit;}
 }
}
