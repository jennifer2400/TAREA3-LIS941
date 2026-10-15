<?php
$isAuth = Auth::check();
$userRole = Auth::role();
$currentUser = Auth::user();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-cuponera sticky-top shadow-sm">
    <div class="container">
        <!-- logo e identidad -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= url('/') ?>">
            <span class="brand-icon d-inline-flex align-items-center justify-content-center bg-white text-cuponera rounded-3 shadow-xs">
                <i class="bi bi-tag-fill fs-5"></i>
            </span>
            <span class="brand-text">
                La Cuponera <span class="badge bg-warning text-dark fs-7 py-1 px-2 rounded-pill">SV</span>
            </span>
        </a>

        <!-- botón toggle responsive -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menú de navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- enlaces principales -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/') ?>"><i class="bi bi-house-door me-1"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/#promociones') ?>"><i class="bi bi-percent me-1"></i> Ofertas Demo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/#como-funciona') ?>"><i class="bi bi-info-circle me-1"></i> ¿Cómo funciona?</a>
                </li>
            </ul>

            <!-- sección de usuario / acciones -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($isAuth): ?>
                    <!-- dropdown de usuario autenticado -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill shadow-xs" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar bg-warning text-dark fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                <?= strtoupper(substr($currentUser['usuario'], 0, 2)) ?>
                            </span>
                            <span class="fw-semibold text-truncate" style="max-width: 140px;">
                                <?= e($currentUser['nombre_mostrar']) ?>
                            </span>
                            <span class="badge bg-white text-dark ms-1 text-uppercase" style="font-size: 0.65rem;">
                                <?= e($userRole) ?>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3" aria-labelledby="userMenuButton">
                            <li class="dropdown-header text-muted small pb-1">
                                Conectado como <strong><?= e($currentUser['usuario']) ?></strong>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($userRole === 'cliente'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('/cliente/dashboard') ?>">
                                        <i class="bi bi-person-badge text-cuponera me-2"></i> Mi Panel de Cliente
                                    </a>
                                </li>
                            <?php elseif ($userRole === 'empresa'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('/empresa/dashboard') ?>">
                                        <i class="bi bi-building text-cuponera me-2"></i> Panel Empresarial
                                    </a>
                                </li>
                            <?php elseif ($userRole === 'administrador'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= url('/admin/dashboard') ?>">
                                        <i class="bi bi-shield-lock text-cuponera me-2"></i> Panel de Administración
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= url('/logout') ?>" method="POST" class="d-inline w-100">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="dropdown-item text-danger py-2 w-100 border-0 bg-transparent">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- botones para visitantes -->
                    <a href="<?= url('/login') ?>" class="btn btn-outline-light rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                    <a href="<?= url('/registro') ?>" class="btn btn-warning text-dark rounded-pill px-3 py-1 fw-bold shadow-xs">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
