<?php
$pageTitle = 'Acceso Denegado (403)';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-5 my-auto text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4 mx-auto mb-3" style="width: 88px; height: 88px;">
                    <i class="bi bi-shield-x display-4"></i>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold d-block mx-auto mb-2" style="max-width: 140px;">
                    ERROR 403
                </span>
                <h3 class="fw-bold text-dark mb-2">Acceso No Autorizado</h3>
                <p class="text-secondary small mb-4">
                    Tu rol de usuario actual no dispone de los permisos requeridos para acceder a este recurso del sistema.
                </p>
                <div class="d-grid gap-2">
                    <a href="<?= url('/') ?>" class="btn btn-cuponera rounded-pill fw-bold shadow-xs py-2">
                        <i class="bi bi-house-door-fill me-1"></i> Ir a la Página Principal
                    </a>
                    <?php if (Auth::check()): ?>
                        <button type="button" onclick="history.back()" class="btn btn-outline-secondary rounded-pill py-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver a la Página Anterior
                        </button>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-secondary rounded-pill py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión con Otra Cuenta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
