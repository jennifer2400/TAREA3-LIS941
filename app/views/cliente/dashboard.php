<?php
$pageTitle = 'Panel de Cliente — ' . e($cliente['nombres']);
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<div class="container py-4 my-auto">
    <!-- encabezado del panel -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold">
                    <i class="bi bi-person-check-fill me-1"></i> ROL: CLIENTE INDIVIDUAL
                </span>
                <span class="badge bg-light text-muted border border-secondary-subtle small">
                    Estado: <strong class="text-success"><?= strtoupper(e($cliente['estado'])) ?></strong>
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-0">
                ¡Hola, <?= e($cliente['nombres']) ?>! 👋
            </h2>
            <p class="text-secondary small mb-0">Bienvenido(a) a tu panel personal en La Cuponera SV.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= url('/ofertas') ?>" class="btn btn-outline-cuponera rounded-pill btn-sm px-3 fw-semibold">
                <i class="bi bi-tag-fill me-1"></i> Ver Catálogo de Ofertas
            </a>
            <form action="<?= url('/logout') ?>" method="POST" class="d-inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm px-3 fw-semibold">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- tarjeta de información del perfil -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                <div class="card-header bg-gradient-cuponera text-white p-4 text-center border-0">
                    <div class="user-avatar-lg bg-white text-cuponera rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-xs mb-2" style="width: 72px; height: 72px; font-size: 1.8rem; font-weight: bold;">
                        <?= strtoupper(substr($cliente['nombres'], 0, 1) . substr($cliente['apellidos'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-0 text-white"><?= e($cliente['nombres'] . ' ' . $cliente['apellidos']) ?></h5>
                    <small class="text-white-50">@<?= e($cliente['usuario']) ?></small>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-cuponera fs-7 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-vcard-fill me-1"></i> Datos Personales Registrados
                    </h6>

                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-3 text-secondary">
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-envelope me-2 text-cuponera"></i> Correo:</span>
                            <strong class="text-dark text-truncate" style="max-width: 170px;" title="<?= e($cliente['correo']) ?>"><?= e($cliente['correo']) ?></strong>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-card-heading me-2 text-cuponera"></i> DUI:</span>
                            <span class="badge bg-light text-dark border font-monospace fs-7"><?= e($cliente['dui']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-calendar-event me-2 text-cuponera"></i> Fecha Nac.:</span>
                            <span class="text-dark fw-semibold"><?= e($cliente['fecha_nacimiento']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-check-circle me-2 text-success"></i> Edad calculada:</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><?= $edad ?> años (Mayor de edad)</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-telephone me-2 text-cuponera"></i> Teléfono:</span>
                            <span class="text-dark"><?= !empty($cliente['telefono']) ? e($cliente['telefono']) : '<em>No registrado</em>' ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-geo-alt me-2 text-cuponera"></i> Dirección:</span>
                            <span class="text-dark text-end" style="max-width: 180px;"><?= !empty($cliente['direccion']) ? e($cliente['direccion']) : '<em>No registrada</em>' ?></span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted"><i class="bi bi-clock-history me-2 text-cuponera"></i> Miembro desde:</span>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($cliente['cuenta_creada_en'])) ?></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4">
                        <div class="text-secondary small">Cupones adquiridos</div>
                        <div class="fs-2 fw-bold text-cuponera"><?= $totalCupones ?></div>
                    </div></div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4">
                        <div class="text-secondary small">Cupones disponibles para canje</div>
                        <div class="fs-2 fw-bold text-success"><?= $totalActivos ?></div>
                    </div></div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white p-4 border-0 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Mis cupones adquiridos</h5>
                        <small class="text-muted">Compras y códigos de canje</small>
                    </div>
                    <span class="badge bg-success-subtle text-success border"><?= $totalActivos ?> disponibles para canje</span>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($cupones)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-ticket-detailed fs-1 text-secondary"></i>
                            <h6 class="fw-bold mt-3">Aún no tienes cupones comprados</h6>
                            <p class="text-secondary small">Explora las ofertas disponibles para realizar tu primera compra.</p>
                            <a href="<?= url('/ofertas') ?>" class="btn btn-cuponera rounded-pill px-4">Explorar ofertas</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead><tr><th>Oferta</th><th>Empresa</th><th>Código</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php foreach (array_slice($cupones, 0, 5) as $cupon): ?>
                                    <?php $vigente = $cupon['estado'] === 'disponible' && $cupon['fecha_limite_canje'] >= date('Y-m-d'); ?>
                                    <tr>
                                        <td><?= e($cupon['titulo']) ?></td>
                                        <td><?= e($cupon['nombre_comercial']) ?></td>
                                        <td><code class="text-break"><?= e($cupon['codigo_cupon']) ?></code></td>
                                        <td><span class="badge <?= $vigente ? 'bg-success' : 'bg-secondary' ?>"><?= e($vigente ? 'disponible' : ($cupon['estado'] === 'disponible' ? 'vencido' : $cupon['estado'])) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="<?= url('/cliente/cupones') ?>" class="btn btn-cuponera rounded-pill">Ver todos mis cupones</a>
                            <a href="<?= url('/cliente/facturas') ?>" class="btn btn-outline-secondary rounded-pill">Mis facturas</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
