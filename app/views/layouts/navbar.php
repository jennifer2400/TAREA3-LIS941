<?php $isAuth=Auth::check(); $role=Auth::role(); $admin=Auth::isAdmin(); $user=Auth::user();
$inicioUrl = $admin ? '/admin/dashboard' : ($role === 'empresa' ? '/empresa/dashboard' : ($role === 'cliente' ? '/cliente/dashboard' : '/')); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-cuponera sticky-top shadow-sm"><div class="container-fluid px-lg-4">
<a class="navbar-brand fw-bold" href="<?=url($inicioUrl)?>"><i class="bi bi-ticket-perforated-fill me-2"></i>La Cuponera SV</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Mostrar menú"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="menuPrincipal"><ul class="navbar-nav me-auto mb-2 mb-lg-0">
<li class="nav-item"><a class="nav-link" href="<?=url($inicioUrl)?>">Inicio</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/ofertas')?>">Ofertas disponibles</a></li>
<?php if ($admin): ?>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Administración</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="<?=url('/admin/dashboard')?>">Panel general</a></li>
<li><a class="dropdown-item" href="<?=url('/admin/usuarios')?>">Usuarios y perfiles</a></li>
<li><a class="dropdown-item" href="<?=url('/admin/empresas')?>">Empresas y aprobaciones</a></li>
<li><a class="dropdown-item" href="<?=url('/admin/administradores')?>">Registrar administrador</a></li>
</ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-bar-chart-line me-1"></i>Reportes</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="<?=url('/admin/reportes/cupones')?>">Cupones vendidos por empresa</a></li>
<li><a class="dropdown-item" href="<?=url('/admin/reportes/ganancias')?>">Ganancias obtenidas</a></li>
</ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Gestión de empresas</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="<?=url('/empresa/promociones')?>">Todas las promociones</a></li>
<li><a class="dropdown-item" href="<?=url('/empresa/promociones/nueva')?>">Nueva promoción (elegir empresa)</a></li>
<li><a class="dropdown-item" href="<?=url('/empresa/canjes')?>">Validar y canjear cupones</a></li></ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Gestión de clientes</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="<?=url('/ofertas')?>">Compra de prueba para cliente</a></li>
<li><a class="dropdown-item" href="<?=url('/cliente/cupones')?>">Todos los cupones</a></li>
<li><a class="dropdown-item" href="<?=url('/cliente/facturas')?>">Todas las facturas</a></li></ul></li>
<?php elseif ($role==='empresa'): ?>
<li class="nav-item"><a class="nav-link" href="<?=url('/empresa/dashboard')?>">Mi empresa</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/empresa/promociones')?>">Mis promociones</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/empresa/promociones/nueva')?>">Registrar promoción</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/empresa/canjes')?>">Canjear cupones</a></li>
<?php elseif ($role==='cliente'): ?>
<li class="nav-item"><a class="nav-link" href="<?=url('/cliente/dashboard')?>">Mi cuenta</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/cliente/cupones')?>">Mis cupones</a></li>
<li class="nav-item"><a class="nav-link" href="<?=url('/cliente/facturas')?>">Mis facturas</a></li>
<?php endif; ?></ul>
<div class="d-flex gap-2 align-items-center flex-wrap">
<?php if ($isAuth): ?><span class="text-white small"><?=e($user['usuario']??'')?> (<?=e($role??'')?>)</span><form action="<?=url('/logout')?>" method="post" class="m-0"><?=Csrf::field()?><button class="btn btn-warning btn-sm" type="submit">Cerrar sesión</button></form>
<?php else: ?><a class="btn btn-outline-light btn-sm" href="<?=url('/login')?>">Iniciar sesión</a><a class="btn btn-warning btn-sm" href="<?=url('/registro-empresa')?>"><i class="bi bi-building-add me-1"></i> Registrar empresa</a><?php endif; ?>
</div></div></div></nav>
