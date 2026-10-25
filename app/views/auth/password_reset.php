<?php
$pageTitle = 'Recuperar contraseña';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>
<div class="container py-5" style="max-width:600px">
  <div class="card border-0 shadow-sm rounded-4 p-4">
    <h2 class="mb-2">Recuperar contraseña</h2>
    <p class="text-muted">Ingresa tu nombre de usuario y el correo con el que te registraste.</p>
    <form method="post" action="<?= e(url('/recuperar-password')) ?>">
      <?= Csrf::field() ?>
      <label for="usuario" class="form-label">Nombre de usuario</label>
      <input class="form-control mb-3" type="text" name="usuario" id="usuario" autocomplete="username" maxlength="50" required>
      <label for="correo" class="form-label">Correo registrado</label>
      <input class="form-control mb-3" type="email" name="correo" id="correo" autocomplete="email" maxlength="100" required>
      <button type="submit" class="btn btn-primary">Continuar</button>
      <a class="btn btn-link" href="<?= e(url('/login')) ?>">Volver al inicio de sesión</a>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
