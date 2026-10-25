<?php
$pageTitle = 'Reportes';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>
<main class="container py-5">
 <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
  <div><h1 class="h2 fw-bold">Reportes</h1><p class="text-muted">Selecciona el reporte que deseas consultar.</p></div>
  <a href="<?= url('/admin/dashboard') ?>" class="btn btn-outline-secondary">Volver al panel general</a>
 </div>
 <div class="row g-4">
  <div class="col-md-6"><div class="card shadow-sm border-0 h-100 p-4"><h2 class="h4 fw-bold">Cupones vendidos por empresa</h2><p class="text-muted">Cantidad de cupones vendidos y total de ventas por empresa.</p><a href="<?= url('/admin/reportes/cupones') ?>" class="btn btn-primary mt-auto">Ver reporte de cupones vendidos</a></div></div>
  <div class="col-md-6"><div class="card shadow-sm border-0 h-100 p-4"><h2 class="h4 fw-bold">Ganancias obtenidas</h2><p class="text-muted">Ventas, comisión y ganancias por empresa.</p><a href="<?= url('/admin/reportes/ganancias') ?>" class="btn btn-primary mt-auto">Ver reporte de ganancias</a></div></div>
 </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
