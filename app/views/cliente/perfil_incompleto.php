<?php
$pageTitle = 'Perfil de cliente pendiente';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>
<main class="container py-5">
    <div class="card border-warning shadow-sm mx-auto" style="max-width: 720px">
        <div class="card-body p-4">
            <h1 class="h4 mb-3"><i class="bi bi-exclamation-triangle text-warning"></i> Perfil de cliente incompleto</h1>
            <p>Tu usuario pudo iniciar sesión, pero no se encontró su ficha en la tabla <code>clientes</code>. Por seguridad, el sistema no puede mostrar el panel ni permitir compras hasta corregir esta relación.</p>
            <p class="text-muted">Solicita al administrador que revise la relación entre <code>usuarios.id</code> y <code>clientes.id_usuario</code>. No es necesario crear otro usuario.</p>
            <a class="btn btn-outline-primary" href="<?= url('/ofertas') ?>">Ver ofertas</a>
            <form action="<?= url('/logout') ?>" method="POST" class="d-inline-block ms-2">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-outline-secondary">Cerrar sesión</button>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
