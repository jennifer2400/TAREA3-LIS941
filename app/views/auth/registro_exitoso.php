<?php
$pageTitle = '¡Registro Exitoso!';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white text-center p-4 p-md-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow-sm mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg display-5"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold d-block mx-auto mb-2" style="max-width: 180px;">
                        CUENTA CREADA CON ÉXITO
                    </span>
                    <h2 class="fw-bold text-dark mb-2">¡Bienvenido(a), <?= e($nombreCompleto) ?>!</h2>
                    <p class="text-secondary small mb-0">
                        Tu cuenta de cliente en <strong>La Cuponera SV</strong> ha sido registrada y activada correctamente en el sistema.
                    </p>
                </div>

                <div class="p-3 bg-light rounded-3 text-start small mb-4 border">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-cuponera me-1"></i> Resumen de tu cuenta:</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1 text-muted">
                        <li><i class="bi bi-check2 text-success me-1"></i> <strong>Rol:</strong> Cliente individual</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> <strong>Estado:</strong> Activo (Listo para iniciar sesión)</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> <strong>Mayoría de edad:</strong> Verificada (18+ años)</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> <strong>Contraseña:</strong> Protegida con hash criptográfico</li>
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <a href="<?= url('/login') ?>" class="btn btn-cuponera btn-lg rounded-pill fw-bold shadow-xs py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión con mi Cuenta
                    </a>
                    <a href="<?= url('/') ?>" class="btn btn-outline-secondary rounded-pill py-2">
                        <i class="bi bi-house me-1"></i> Volver a la Página Principal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
