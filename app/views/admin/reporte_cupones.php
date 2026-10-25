<?php
$pageTitle = 'Cupones vendidos por empresa';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
$totales = ['vendidos' => 0, 'ingresos' => 0.0, 'ganancias' => 0.0, 'neto_empresa' => 0.0];
foreach ($resumen as $empresa) {
    foreach ($totales as $clave => $valor) {
        $totales[$clave] += (float) $empresa[$clave];
    }
}
?>
<main class="container py-5">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <h1 class="h2 fw-bold mb-0">Cupones vendidos por empresa</h1>
  <a href="<?= url('/admin/reportes') ?>" class="btn btn-outline-secondary">Volver a Reportes</a>
</div>
  <section id="cupones-vendidos" class="card border-0 shadow-sm p-4 mb-4" style="scroll-margin-top:6rem">
    <h2 class="h4 fw-bold mb-2">Reporte de total de cupones vendidos por empresa</h2>
    <p class="text-muted">Número de cupones registrados como comprados y monto facturado por cada empresa.</p>
    <div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Empresa</th><th class="text-end">Cupones vendidos</th><th class="text-end">Total de ventas</th></tr></thead><tbody>
    <?php foreach ($resumen as $empresa): ?><tr><td><?= e($empresa['nombre_comercial']) ?></td><td class="text-end"><?= number_format((int) $empresa['vendidos']) ?></td><td class="text-end">$<?= number_format((float) $empresa['ingresos'], 2) ?></td></tr><?php endforeach; ?>
    <?php if (!$resumen): ?><tr><td colspan="3" class="text-center text-muted py-4">No hay empresas registradas.</td></tr><?php endif; ?>
    </tbody><tfoot class="fw-bold"><tr><td>Total</td><td class="text-end"><?= number_format($totales['vendidos'], 0) ?></td><td class="text-end">$<?= number_format($totales['ingresos'], 2) ?></td></tr></tfoot></table></div>
  </section>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
