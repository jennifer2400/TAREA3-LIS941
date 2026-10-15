<?php
$flashes = Flash::getAll();
?>
<?php if (!empty($flashes)): ?>
<div class="container mt-3">
    <?php foreach ($flashes as $type => $messages): ?>
        <?php 
            $alertClass = match($type) {
                'success' => 'alert-success border-success-subtle text-success-emphasis',
                'error'   => 'alert-danger border-danger-subtle text-danger-emphasis',
                'warning' => 'alert-warning border-warning-subtle text-warning-emphasis',
                'info'    => 'alert-info border-info-subtle text-info-emphasis',
                default   => 'alert-secondary'
            };
            $icon = match($type) {
                'success' => 'bi-check-circle-fill text-success',
                'error'   => 'bi-exclamation-triangle-fill text-danger',
                'warning' => 'bi-exclamation-circle-fill text-warning',
                'info'    => 'bi-info-circle-fill text-info',
                default   => 'bi-bell-fill'
            };
        ?>
        <?php foreach ($messages as $msg): ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show shadow-xs rounded-3 d-flex align-items-center mb-2" role="alert">
                <i class="bi <?= $icon ?> fs-5 me-2 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <?= e($msg) ?>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
