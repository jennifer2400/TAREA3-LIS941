<?php
require_once __DIR__.'/../config/database.php';
class UsuariosController {
 public function index():void {
  Auth::requireRole('administrador');
  $usuarios=Database::getConnection()->query("SELECT u.id,u.usuario,u.correo,u.estado,r.nombre AS rol,u.created_at FROM usuarios u JOIN roles r ON r.id=u.id_rol ORDER BY u.created_at DESC")->fetchAll();
  require __DIR__.'/../views/admin/usuarios.php';
 }
}
