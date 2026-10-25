<?php
require_once __DIR__ . '/../models/Cupon.php';
class CuponController {
 public function ofertas():void {$ofertas=Cupon::disponibles();$this->vista('cupones/ofertas',['ofertas'=>$ofertas]);}
 private function vista(string $v,array $vars=[]):void {extract($vars);require __DIR__.'/../views/'.$v.'.php';}
 public function comprarFormulario():void {Auth::requireRole('cliente');$id=(int)($_GET['id']??0);$clientes=Auth::isAdmin()?Cupon::db()->query("SELECT cl.id,u.usuario,CONCAT(cl.nombres,' ',cl.apellidos) AS nombre FROM clientes cl JOIN usuarios u ON u.id=cl.id_usuario WHERE u.estado='activo' ORDER BY cl.nombres")->fetchAll():[];$encontrada=null;foreach(Cupon::disponibles() as $p){if((int)$p['id']===$id)$encontrada=$p;}if(!$encontrada){Flash::error('Oferta no disponible');header('Location: '.url('/ofertas'));exit;}$this->vista('cupones/pago',['oferta'=>$encontrada]);}
 public function comprar():void {
  Auth::requireRole('cliente');if(!Csrf::validate($_POST['csrf_token']??'')){http_response_code(403);exit('Token inválido');}
  // Pago simulado: no guardar ni transmitir datos reales de tarjetas a terceros.
  $numero=preg_replace('/\D/','',$_POST['tarjeta']??'');$mes=(int)($_POST['mes']??0);$anio=(int)($_POST['anio']??0);$cvv=$_POST['cvv']??'';
  $actual=(int)date('Ym');if(!preg_match('/^\d{16}$/',$numero)||!preg_match('/^\d{3}$/',$cvv)||$mes<1||$mes>12||$anio*100+$mes<$actual||$anio>=(int)date('Y')+25){Flash::error('Verifica los datos de la tarjeta de demostración y su vigencia.');header('Location: '.url('/comprar?id='.(int)($_POST['id_promocion']??0)));exit;}
  try{$usuarioDestino=(int)Auth::id();if(Auth::isAdmin()){$st=Cupon::db()->prepare("SELECT id_usuario FROM clientes WHERE id=?");$st->execute([(int)($_POST['id_cliente']??0)]);$usuarioDestino=(int)$st->fetchColumn();if(!$usuarioDestino)throw new RuntimeException('Selecciona un cliente válido.');}$factura=Cupon::comprar((int)($_POST['id_promocion']??0),$usuarioDestino);Flash::success('Compra de demostración registrada; no se realizó un cargo real.');header('Location: '.url('/cliente/factura?id='.$factura));exit;}catch(Throwable $e){Flash::error($e->getMessage());header('Location: '.url('/ofertas'));exit;}
 }
 public function misCupones():void {Auth::requireRole('cliente');$cupones=(Auth::isAdmin()?Cupon::todosCupones():Cupon::misCupones((int)Auth::id()));$this->vista('cupones/mis_cupones',compact('cupones'));}
 public function misFacturas():void {Auth::requireRole('cliente');$facturas=(Auth::isAdmin()?Cupon::todasFacturas():Cupon::misFacturas((int)Auth::id()));$this->vista('cupones/facturas',compact('facturas'));}
 public function factura():void {Auth::requireRole('cliente');$factura=(Auth::isAdmin()?Cupon::facturaAdmin((int)($_GET['id']??0)):Cupon::factura((int)($_GET['id']??0),(int)Auth::id()));if(!$factura){http_response_code(404);exit('Factura no encontrada');}$this->vista('cupones/factura',compact('factura'));}
}
