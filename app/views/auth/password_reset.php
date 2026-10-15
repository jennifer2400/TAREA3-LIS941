<?php
$pageTitle = 'Recuperación de Contraseña — Base Técnica Compartida';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-gradient-cuponera text-white p-4 text-center border-0">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white text-cuponera rounded-circle p-3 mb-2 shadow-xs" style="width: 56px; height: 56px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">Recuperar Contraseña</h3>
                    <p class="text-white-50 small mb-0">Esqueleto técnico compartido para restablecimiento de credenciales</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- nota de delimitación de alcance académico -->
                    <div class="alert alert-info border-info-subtle rounded-3 small mb-4">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill fs-5 text-info flex-shrink-0"></i>
                            <div>
                                <strong>Nota de Integración (Persona 1):</strong>
                                <p class="mb-0 mt-1">
                                    En este módulo se diseñó e implementó la base relacional técnica (tabla <code>recuperacion_tokens</code>, cálculo de expiración y hashes criptográficos SHA-256). El servicio de envío SMTP y la pantalla de restablecimiento final están reservados para la integración de mis compañeros según las pautas del proyecto.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="<?= url('/recuperar-password') ?>" method="POST" novalidate>
                        <?= Csrf::field() ?>

                        <div class="mb-3">
                            <label for="correo" class="form-label fw-semibold text-dark">
                                Correo Electrónico Registrado <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" 
                                       class="form-control rounded-end-3" 
                                       id="correo" 
                                       name="correo" 
                                       placeholder="nombre@ejemplo.com" 
                                       required>
                            </div>
                            <div class="form-text small">Ingresa la dirección asociada a tu cuenta de cliente o empresa.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-cuponera btn-lg rounded-pill fw-bold shadow-xs py-2">
                                <i class="bi bi-send-fill me-1"></i> Probar Generación de Token Técnico
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top border-light">
                        <a href="<?= url('/login') ?>" class="text-cuponera fw-semibold text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Volver a Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
