<?php
$pageTitle = 'Nueva contraseña';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>
<div class="container py-5" style="max-width:600px">
  <div class="card border-0 shadow-sm rounded-4 p-4">
    <h2 class="mb-3">Establecer nueva contraseña</h2>
    <?php if (!$valid): ?>
      <div class="alert alert-warning">La solicitud expiró. Vuelve a verificar tus datos.</div>
      <a class="btn btn-primary" href="<?= e(url('/recuperar-password')) ?>">Volver a intentar</a>
    <?php else: ?>
      <form method="post" action="<?= e(url('/restablecer-password')) ?>">
        <?= Csrf::field() ?>
        <label for="password" class="form-label">Nueva contraseña</label>
        <input type="password" class="form-control mb-2" name="password" id="password" autocomplete="new-password" minlength="8" required>
        <p class="small text-muted">Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.</p>
        <label for="password_confirm" class="form-label">Confirmar contraseña</label>
        <input type="password" class="form-control mb-3" name="password_confirm" id="password_confirm" autocomplete="new-password" minlength="8" required>
        <button type="submit" class="btn btn-primary">Restablecer contraseña</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
