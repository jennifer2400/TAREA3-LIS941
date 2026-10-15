<?php
$pageTitle = 'Iniciar Sesión — Clientes y Empresas';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-gradient-cuponera text-white p-4 text-center border-0">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white text-cuponera rounded-circle p-3 mb-2 shadow-xs" style="width: 56px; height: 56px;">
                        <i class="bi bi-person-fill-lock fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">Iniciar Sesión</h3>
                    <p class="text-white-50 small mb-0">Acceso unificado para Clientes y Empresas registradas</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="<?= url('/login') ?>" method="POST" id="formLogin" novalidate>
                        <?= Csrf::field() ?>

                        <!-- campo: usuario o correo -->
                        <div class="mb-3">
                            <label for="identifier" class="form-label fw-semibold text-dark">
                                <i class="bi bi-person-circle text-cuponera me-1"></i> Usuario o Correo Electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg rounded-3 fs-6" 
                                   id="identifier" 
                                   name="identifier" 
                                   value="<?= e($oldIdentifier ?? '') ?>" 
                                   placeholder="ej. juanperez o juan@correo.com" 
                                   required 
                                   autofocus>
                            <div class="invalid-feedback">Por favor, ingresa tu usuario o correo electrónico.</div>
                        </div>

                        <!-- campo: contraseña con toggle ocular -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold text-dark mb-0">
                                    <i class="bi bi-key-fill text-cuponera me-1"></i> Contraseña <span class="text-danger">*</span>
                                </label>
                                <a href="<?= url('/recuperar-password') ?>" class="small text-cuponera text-decoration-none fw-semibold">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg rounded-start-3 fs-6" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Ingresa tu contraseña" 
                                       required>
                                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 toggle-password" 
                                        type="button" 
                                        data-target="password"
                                        title="Mostrar / Ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <div class="invalid-feedback">Por favor, ingresa tu contraseña.</div>
                            </div>
                        </div>

                        <!-- botón de envío -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-cuponera btn-lg rounded-pill fw-bold shadow-xs py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al Sistema
                            </button>
                        </div>
                    </form>

                    <!-- acceso a registro -->
                    <div class="text-center mt-4 pt-3 border-top border-light">
                        <p class="text-muted small mb-0">
                            ¿Aún no tienes una cuenta de cliente?
                            <a href="<?= url('/registro') ?>" class="text-cuponera fw-bold text-decoration-none">
                                Regístrate aquí gratis
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- caja informativa para el evaluador docente -->
            <div class="card border-0 shadow-xs rounded-4 mt-4 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-bold small">
                            <i class="bi bi-mortarboard-fill me-1"></i> CUENTAS DEMOSTRATIVAS DE PRUEBA
                        </span>
                        <small class="text-muted">Contraseña común: <code>Cuponera2025!</code></small>
                    </div>
                    <p class="small text-muted mb-2">
                        Haz clic en cualquiera de las siguientes credenciales para autocompletar el formulario rápidamente:
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-fill-demo" data-user="juanperez" data-pass="Cuponera2025!">
                            <i class="bi bi-person-fill"></i> Cliente: <strong>juanperez</strong>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill btn-fill-demo" data-user="pizzanostra" data-pass="Cuponera2025!">
                            <i class="bi bi-building"></i> Empresa (Aprobada): <strong>pizzanostra</strong>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill btn-fill-demo" data-user="cafebistro" data-pass="Cuponera2025!">
                            <i class="bi bi-clock-history"></i> Empresa (Pendiente): <strong>cafebistro</strong>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill btn-fill-demo" data-user="admin_demo" data-pass="Cuponera2025!">
                            <i class="bi bi-shield-lock"></i> Admin: <strong>admin_demo</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
