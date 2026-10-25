<?php
$pageTitle = 'Ganancias obtenidas';
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
  <h1 class="h2 fw-bold mb-0">Ganancias obtenidas</h1>
  <a href="<?= url('/admin/reportes') ?>" class="btn btn-outline-secondary">Volver a Reportes</a>
</div>
  <section id="ganancias-obtenidas" class="card border-0 shadow-sm p-4 mb-4" style="scroll-margin-top:6rem">
    <h2 class="h4 fw-bold mb-2">Reporte de total de ganancias obtenidas</h2>
    <p class="text-muted">Ganancia de la plataforma calculada aplicando el porcentaje de comisión de cada empresa a sus ventas facturadas.</p>
    <div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Empresa</th><th class="text-end">Ventas</th><th class="text-end">Comisión</th><th class="text-end">Ganancia plataforma</th><th class="text-end">Ingreso neto empresa</th></tr></thead><tbody>
    <?php foreach ($resumen as $empresa): ?><tr><td><?= e($empresa['nombre_comercial']) ?></td><td class="text-end">$<?= number_format((float) $empresa['ingresos'], 2) ?></td><td class="text-end"><?= number_format((float) $empresa['comision_porcentaje'], 2) ?>%</td><td class="text-end">$<?= number_format((float) $empresa['ganancias'], 2) ?></td><td class="text-end">$<?= number_format((float) $empresa['neto_empresa'], 2) ?></td></tr><?php endforeach; ?>
    <?php if (!$resumen): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay empresas registradas.</td></tr><?php endif; ?>
    </tbody><tfoot class="fw-bold"><tr><td>Total</td><td class="text-end">$<?= number_format($totales['ingresos'], 2) ?></td><td></td><td class="text-end">$<?= number_format($totales['ganancias'], 2) ?></td><td class="text-end">$<?= number_format($totales['neto_empresa'], 2) ?></td></tr></tfoot></table></div>
  </section>
<p class="small text-muted">Los importes aplican la comisión actual de cada empresa a sus ventas facturadas.</p>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
