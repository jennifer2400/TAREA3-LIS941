<?php
$pageTitle = 'Panel Empresarial — ' . e($empresa['nombre_comercial']);
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';

$estadoAprobacion = $empresa['estado_aprobacion'];
?>

<div class="container py-4 my-auto">
    <!-- encabezado del panel empresarial -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small fw-bold">
                    <i class="bi bi-building me-1"></i> ROL: EMPRESA PROVEEDORA
                </span>
                <?php if ($estadoAprobacion === 'aprobada'): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                        <i class="bi bi-patch-check-fill me-1"></i> APROBADA
                    </span>
                <?php elseif ($estadoAprobacion === 'pendiente'): ?>
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">
                        <i class="bi bi-clock-history me-1"></i> PENDIENTE DE APROBACIÓN
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">
                        <i class="bi bi-x-circle-fill me-1"></i> RECHAZADA
                    </span>
                <?php endif; ?>
            </div>
            <h2 class="fw-bold text-dark mb-0">
                <?= e($empresa['nombre_comercial']) ?>
            </h2>
            <p class="text-secondary small mb-0">Razón Social: <strong><?= e($empresa['razon_social']) ?></strong></p>
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

    <!-- banner informativo de estado de aprobación -->
    <?php if ($estadoAprobacion === 'pendiente'): ?>
        <div class="alert alert-warning border-warning-subtle rounded-3 shadow-xs mb-4">
            <div class="d-flex gap-3 align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning flex-shrink-0"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Empresa en proceso de revisión administrativa</h6>
                    <p class="small text-secondary mb-0">
                        Tu cuenta de empresa está autenticada correctamente en el sistema, pero la publicación activa de promociones y venta de cupones se habilitará una vez que el módulo de administración (desarrollado por mis compañeros de equipo) apruebe los documentos y NIT de la empresa.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success border-success-subtle rounded-3 shadow-xs mb-4">
            <div class="d-flex gap-3 align-items-center">
                <i class="bi bi-shield-check fs-3 text-success flex-shrink-0"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Estado de Empresa: Aprobada para Operar</h6>
                    <small class="text-secondary">Tu empresa cumple con los requisitos comerciales para la publicación de ofertas en La Cuponera SV.</small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- columna 1: perfil empresarial -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-gradient-cuponera text-white p-4 border-0 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white text-cuponera rounded-circle p-3 mb-2 shadow-xs" style="width: 64px; height: 64px;">
                        <i class="bi bi-shop fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-white"><?= e($empresa['nombre_comercial']) ?></h5>
                    <small class="text-white-50">Rubro: <?= e($empresa['rubro_nombre'] ?? 'Comercial') ?></small>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-cuponera fs-7 mb-3 pb-2 border-bottom">
                        <i class="bi bi-file-earmark-text-fill me-1"></i> Información Tributaria y Contacto
                    </h6>

                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-3 text-secondary">
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-credit-card-2-front me-2 text-cuponera"></i> NIT:</span>
                            <span class="badge bg-light text-dark border font-monospace fs-7"><?= e($empresa['nit']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-envelope me-2 text-cuponera"></i> Correo de contacto:</span>
                            <strong class="text-dark text-truncate" style="max-width: 170px;" title="<?= e($empresa['correo_contacto']) ?>"><?= e($empresa['correo_contacto']) ?></strong>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-telephone me-2 text-cuponera"></i> Teléfono:</span>
                            <span class="text-dark"><?= e($empresa['telefono']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-percent me-2 text-cuponera"></i> Comisión pactada:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= number_format($empresa['comision_porcentaje'], 2) ?>%</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-geo-alt me-2 text-cuponera"></i> Dirección:</span>
                            <span class="text-dark text-end" style="max-width: 200px;"><?= e($empresa['direccion']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted"><i class="bi bi-clock-history me-2 text-cuponera"></i> Fecha de Registro:</span>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($empresa['cuenta_creada_en'])) ?></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- columna 2: esqueleto de módulos futuros (puntos de integración) -->
        <div class="col-lg-7">
            <!-- tarjeta: gestión de promociones (punto de integración) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Promociones y Ofertas Publicadas</h5>
                        <small class="text-muted">Catálogo de cupones de tu comercio</small>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary border">Punto de Integración</span>
                </div>
                <div class="card-body p-4 text-center py-4">
                    <div class="p-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center text-muted mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-megaphone fs-3 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Módulo Reservado para Persona 2 / Persona 3</h6>
                    <p class="small text-secondary max-w-md mx-auto mb-3">
                        La creación de ofertas, fijación de precios con descuento, fechas límite y control de stock está estructurada en la tabla relacional <code>promociones</code> y lista para la integración del compañero encargado.
                    </p>
                    <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>
                        <i class="bi bi-plus-circle me-1"></i> Publicar Nueva Oferta (Próximamente)
                    </button>
                </div>
            </div>

            <!-- tarjeta: validación y canje de cupones (punto de integración) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Canje de Cupones en Tienda</h5>
                        <small class="text-muted">Validación de códigos presentados por clientes</small>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary border">Punto de Integración</span>
                </div>
                <div class="card-body p-4 text-center py-4">
                    <div class="p-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center text-muted mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-qr-code-scan fs-3 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Estructura Preparada en Base de Datos</h6>
                    <p class="small text-secondary max-w-md mx-auto mb-0">
                        La verificación de códigos únicos de cupón (tabla <code>cupones_comprados</code>) y actualización de estado 'canjeado' será conectada en la fase de compañeros.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
