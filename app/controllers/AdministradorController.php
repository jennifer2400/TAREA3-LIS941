<?php
require_once __DIR__ . '/../config/database.php';
class AdministradorController {
 public function index():void {Auth::requireRole('administrador');$admins=Database::getConnection()->query("SELECT u.id,u.usuario,u.correo,u.estado FROM usuarios u JOIN roles r ON r.id=u.id_rol WHERE r.nombre='administrador' ORDER BY u.id DESC")->fetchAll();require __DIR__.'/../views/admin/administradores.php';}
 public function guardar():void {Auth::requireRole('administrador');if(!Csrf::validate($_POST['csrf_token']??'')){http_response_code(403);exit('CSRF inválido');}
 $usuario=trim($_POST['usuario']??'');$correo=trim($_POST['correo']??'');$clave=$_POST['password']??'';
 if(!preg_match('/^[a-zA-Z0-9_.]{4,50}$/',$usuario)||!filter_var($correo,FILTER_VALIDATE_EMAIL)||strlen($clave)<10){Flash::error('Usuario de 4 a 50 caracteres, correo válido y contraseña de mínimo 10 caracteres.');header('Location: '.url('/admin/administradores'));exit;}
 try{$db=Database::getConnection();$s=$db->prepare("INSERT INTO usuarios (id_rol,usuario,correo,password_hash,estado) SELECT id,?,?,?,'activo' FROM roles WHERE nombre='administrador'");$s->execute([$usuario,$correo,password_hash($clave,PASSWORD_DEFAULT)]);if(!$s->rowCount())throw new RuntimeException('Falta el rol administrador');Flash::success('Administrador creado');}catch(Throwable $e){Flash::error('No se pudo registrar. Verifica que usuario y correo no estén repetidos.');}header('Location: '.url('/admin/administradores'));exit;
 }
}
