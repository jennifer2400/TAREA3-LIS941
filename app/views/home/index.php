<?php
$pageTitle = 'Inicio — Las mejores ofertas y cupones de El Salvador';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<!-- =========================================================================
     hero section
     ========================================================================= -->
<header class="hero-section py-5 position-relative overflow-hidden">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-center text-lg-start">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white shadow-xs border border-primary-subtle text-cuponera fw-semibold small mb-3">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>La plataforma de cupones #1 en El Salvador</span>
                </div>
                <h1 class="display-4 fw-extrabold text-dark tracking-tight mb-3">
                    Ahorra en tus lugares favoritos con <span class="text-cuponera">La Cuponera SV</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    Disfruta de hasta un <strong>60% de descuento</strong> en restaurantes, spas, hoteles, salud dental y entretenimiento. Regístrate gratis en menos de 2 minutos.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="<?= url('/registro') ?>" class="btn btn-cuponera btn-lg rounded-pill px-4 py-2 fw-bold shadow">
                        <i class="bi bi-person-plus-fill me-2"></i> Crear cuenta de cliente
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión
                    </a>
                </div>

                <!-- garantías / badges de confianza -->
                <div class="row g-3 pt-4 mt-3 border-top border-secondary-subtle text-muted small">
                    <div class="col-sm-4 d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                        <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                        <span>Registro 100% seguro (18+)</span>
                    </div>
                    <div class="col-sm-4 d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                        <i class="bi bi-shop text-primary fs-5"></i>
                        <span>Comercios salvadoreños</span>
                    </div>
                    <div class="col-sm-4 d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                        <i class="bi bi-qr-code text-warning fs-5"></i>
                        <span>Canje fácil e inmediato</span>
                    </div>
                </div>
            </div>

            <!-- ilustración / tarjeta visual del hero -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden hero-card bg-white position-relative">
                    <div class="card-header bg-gradient-cuponera text-white p-4 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                                <i class="bi bi-fire me-1"></i> OFERTA DESTACADA
                            </span>
                            <span class="badge bg-white text-dark small fw-semibold">DEMO ILUSTRATIVO</span>
                        </div>
                        <h4 class="fw-bold mt-3 mb-1 text-white">Super Combo 2x1 Especial</h4>
                        <p class="small text-white-50 mb-0"><i class="bi bi-geo-alt-fill me-1"></i> San Salvador, El Salvador</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-baseline gap-3 mb-3">
                            <span class="display-6 fw-bold text-cuponera">$12.50</span>
                            <span class="text-muted text-decoration-line-through fs-5">$25.00</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-auto fs-6">
                                -50% DTO
                            </span>
                        </div>
                        <p class="text-secondary small mb-4">
                            Muestra demostrativa de cómo los usuarios visualizan los descuentos y ahorros disponibles antes de iniciar sesión.
                        </p>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Vigencia:</span>
                                <strong class="text-dark">15 días restantes</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-cuponera" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <a href="<?= url('/registro') ?>" class="btn btn-warning text-dark w-100 fw-bold rounded-pill py-2 shadow-xs">
                            <i class="bi bi-ticket-perforated-fill me-1"></i> ¡Regístrate para ver más ofertas!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- =========================================================================
     cómo funciona
     ========================================================================= -->
<section id="como-funciona" class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small fw-bold mb-2">
                PASO A PASO
            </span>
            <h2 class="fw-bold text-dark">¿Cómo funciona La Cuponera SV?</h2>
            <p class="text-secondary">Descubre la manera más inteligente de consentirte y ahorrar dinero en tus comercios locales favoritos.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 h-100 p-4 rounded-4 shadow-xs bg-light hover-lift">
                    <div class="feature-icon bg-cuponera text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-search fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">1. Explora Cupones</h5>
                    <p class="text-secondary small mb-0">
                        Navega por las promociones en gastronomía, spas, entretenimiento y salud. Encuentra descuentos de hasta el 60%.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 p-4 rounded-4 shadow-xs bg-light hover-lift">
                    <div class="feature-icon bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-credit-card fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">2. Regístrate y Adquiere</h5>
                    <p class="text-secondary small mb-0">
                        Crea tu cuenta de cliente con validación segura de DUI y edad (18+). Adquiere tus cupones preferidos al instante.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 p-4 rounded-4 shadow-xs bg-light hover-lift">
                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-gift fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">3. Canjea y Disfruta</h5>
                    <p class="text-secondary small mb-0">
                        Presenta el código del cupón en el establecimiento asociado y disfruta de tu beneficio sin trámites complicados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     sección de promociones demostrativas
     ========================================================================= -->
<section id="promociones" class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-4">
            <div>
                <div class="d-inline-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded small">
                        <i class="bi bi-info-circle me-1"></i> VISTA PREVIA DEL CATÁLOGO
                    </span>
                    <span class="badge bg-warning text-dark small fw-bold">DATOS DEMOSTRATIVOS</span>
                </div>
                <h2 class="fw-bold text-dark mb-1">Promociones Ilustrativas</h2>
                <p class="text-secondary mb-0 small">
                    Ofertas diseñadas exclusivamente con fines de prueba y demostración visual para la Persona 1.
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= url('/registro') ?>" class="btn btn-outline-cuponera rounded-pill fw-semibold btn-sm px-3">
                    <i class="bi bi-unlock-fill me-1"></i> Desbloquear acceso completo
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($promocionesDemo as $promo): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden promo-card bg-white position-relative hover-lift">
                        <!-- badge de demostración visible en la tarjeta -->
                        <div class="position-absolute top-0 end-0 m-3 z-2">
                            <span class="badge bg-<?= $promo['badge_color'] ?> text-white fw-bold px-2 py-1 shadow-xs">
                                -<?= $promo['descuento'] ?>%
                            </span>
                        </div>

                        <div class="promo-thumb-header bg-gradient-light p-4 text-center position-relative">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-cuponera shadow-xs p-3 mb-2" style="width: 58px; height: 58px;">
                                <i class="bi <?= $promo['icono'] ?> fs-3"></i>
                            </div>
                            <span class="d-block badge bg-light text-muted border border-secondary-subtle mx-auto small text-truncate" style="max-width: 180px;">
                                <?= e($promo['rubro']) ?>
                            </span>
                        </div>

                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle small py-1 px-2">
                                    [DEMO]
                                </span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> <?= $promo['fecha_limite'] ?></small>
                            </div>

                            <h6 class="fw-bold text-dark mb-1 promo-title" title="<?= e($promo['titulo']) ?>">
                                <?= e($promo['titulo']) ?>
                            </h6>
                            <p class="text-muted small mb-2"><i class="bi bi-shop me-1"></i> <?= e($promo['empresa']) ?></p>

                            <p class="text-secondary small mb-3 flex-grow-1" style="font-size: 0.8rem;">
                                <?= e($promo['descripcion']) ?>
                            </p>

                            <div class="pt-2 border-top border-light mt-auto">
                                <div class="d-flex align-items-baseline justify-content-between mb-2">
                                    <div>
                                        <span class="fs-5 fw-bold text-cuponera">$<?= number_format($promo['precio_oferta'], 2) ?></span>
                                        <small class="text-muted text-decoration-line-through ms-1">$<?= number_format($promo['precio_regular'], 2) ?></small>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        Ahorras $<?= number_format($promo['precio_regular'] - $promo['precio_oferta'], 2) ?>
                                    </span>
                                </div>
                                <a href="<?= url('/login') ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-cart-plus me-1"></i> Iniciar sesión para comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     banner llamado a la acción (cta)
     ========================================================================= -->
<section class="py-5 bg-cuponera text-white position-relative overflow-hidden">
    <div class="container py-3 text-center position-relative z-1">
        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-3">ÚNETE HOY</span>
        <h2 class="display-6 fw-bold mb-3">¿Eres cliente o representas una empresa?</h2>
        <p class="lead text-white-50 max-w-xl mx-auto mb-4">
            Como cliente podrás adquirir las mejores ofertas. Si tienes un negocio, podrás unirte como empresa asociada para llegar a miles de usuarios salvadoreños.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= url('/registro') ?>" class="btn btn-warning text-dark btn-lg rounded-pill px-4 fw-bold shadow">
                <i class="bi bi-person-fill-add me-2"></i> Registrarme como cliente
            </a>
            <a href="<?= url('/login') ?>" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-building me-2"></i> Acceso Empresas y Clientes
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
