<?php
$pageTitle = 'Panel de Administración';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<div class="container py-4 my-auto">
    <!-- encabezado del panel administrativo -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-white px-3 py-1 rounded-pill small fw-bold">
                    <i class="bi bi-shield-lock-fill me-1"></i> ROL: ADMINISTRADOR DEL SISTEMA
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                    Acceso global por rol
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-0">Panel de Control Administrativo</h2>
            <p class="text-secondary small mb-0">Gestión general de usuarios, empresas, promociones, compras, facturas, canjes y reportes.</p>
        </div>

        <div class="d-flex gap-2">
            <form action="<?= url('/logout') ?>" method="POST" class="d-inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm px-3 fw-semibold">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4"><a class="btn btn-primary" href="<?=url('/admin/empresas')?>">Aprobar empresas</a><a class="btn btn-outline-primary" href="<?=url('/admin/administradores')?>">Registrar administradores</a></div>
    <div class="d-flex flex-wrap gap-2 mb-4"><a class="btn btn-outline-primary" href="<?=url('/admin/usuarios')?>">Usuarios</a><a class="btn btn-outline-primary" href="<?=url('/empresa/promociones')?>">Promociones</a><a class="btn btn-outline-primary" href="<?=url('/cliente/cupones')?>">Cupones vendidos</a><a class="btn btn-outline-primary" href="<?=url('/cliente/facturas')?>">Facturas</a><a class="btn btn-outline-primary" href="<?=url('/empresa/canjes')?>">Canjes</a><a class="btn btn-outline-primary" href="<?=url('/admin/reportes')?>">Reportes</a></div>
<!-- métricas del sistema -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 hover-lift">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Usuarios</span>
                        <h3 class="fw-bold text-dark mb-0"><?= $totalUsuarios ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 hover-lift">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Clientes Registrados</span>
                        <h3 class="fw-bold text-dark mb-0"><?= $totalClientes ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 hover-lift">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-info-subtle text-info rounded-3">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Empresas en Sistema</span>
                        <h3 class="fw-bold text-dark mb-0"><?= $totalEmpresas ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 hover-lift">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-warning-subtle text-warning rounded-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Empresas Pendientes</span>
                        <h3 class="fw-bold text-dark mb-0"><?= $empresasPendientes ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- tarjeta de contratos de integración para compañeros -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="fw-bold text-dark mb-1">Puntos de Integración para el Equipo</h5>
            <p class="text-secondary small mb-0">Contratos relacionales y de código preparados para que mis compañeros conecten sus partes sin duplicar esfuerzo:</p>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check2-square text-primary"></i> 1. Aprobación de Empresas
                        </h6>
                        <p class="small text-secondary mb-2">
                            La tabla <code>empresas</code> incluye el campo <code>estado_aprobacion</code> con valores <em>'pendiente', 'aprobada', 'rechazada'</em>.
                        </p>
                        <span class="badge bg-secondary-subtle text-secondary small">Pendiente: Desarrollo por Compañero</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-key-fill text-warning"></i> 2. Restablecimiento Contraseñas
                        </h6>
                        <p class="small text-secondary mb-2">
                            La tabla <code>recuperacion_tokens</code> almacena tokens SHA-256 con fecha de expiración. El modelo <code>PasswordReset</code> está listo para llamar desde el servicio SMTP.
                        </p>
                        <span class="badge bg-secondary-subtle text-secondary small">Pendiente: Integración SMTP</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-tags-fill text-success"></i> 3. Gestión y Compra de Ofertas
                        </h6>
                        <p class="small text-secondary mb-2">
                            Las tablas relacionales <code>promociones</code> y <code>cupones_comprados</code> están creadas con claves foráneas hacia <code>empresas</code> y <code>clientes</code>.
                        </p>
                        <span class="badge bg-secondary-subtle text-secondary small">Pendiente: Lógica de Compra y Canje</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
