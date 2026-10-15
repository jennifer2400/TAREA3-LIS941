<?php
$pageTitle = 'Página No Encontrada (404)';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-5 my-auto text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis rounded-circle p-4 mx-auto mb-3" style="width: 88px; height: 88px;">
                    <i class="bi bi-question-diamond display-4 text-warning"></i>
                </div>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill small fw-bold d-block mx-auto mb-2" style="max-width: 140px;">
                    ERROR 404
                </span>
                <h3 class="fw-bold text-dark mb-2">Página No Encontrada</h3>
                <p class="text-secondary small mb-4">
                    La dirección web que buscas no existe o fue trasladada. Verifica la URL o regresa a la página de inicio.
                </p>
                <div class="d-grid gap-2">
                    <a href="<?= url('/') ?>" class="btn btn-cuponera rounded-pill fw-bold shadow-xs py-2">
                        <i class="bi bi-house-door-fill me-1"></i> Volver al Inicio
                    </a>
                    <button type="button" onclick="history.back()" class="btn btn-outline-secondary rounded-pill py-2">
                        <i class="bi bi-arrow-left me-1"></i> Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
