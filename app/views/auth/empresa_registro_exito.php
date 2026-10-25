<?php
$pageTitle = 'Solicitud de empresa recibida';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>
<main class="container py-5" style="max-width:760px">
    <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center">
        <div class="mb-3"><i class="bi bi-check-circle-fill text-success display-4" aria-hidden="true"></i></div>
        <h1 class="h2 fw-bold">¡Solicitud enviada exitosamente!</h1>
        <p class="lead">Tu empresa se encuentra <strong>pendiente de aprobación</strong> por un administrador de La Cuponera SV.</p>
        <p class="text-secondary">Una vez que el administrador revise y apruebe tu solicitud, y establezca el porcentaje de comisión correspondiente, podrás acceder a las funciones de publicación de ofertas.</p>
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
            <a class="btn btn-primary" href="<?= url('/login') ?>">Ir a iniciar sesión</a>
            <a class="btn btn-outline-secondary" href="<?= url('/') ?>">Volver al inicio</a>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
