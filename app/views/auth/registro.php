<?php
$pageTitle = 'Registro de Clientes';
$extraScript = '/js/registro.js';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
require __DIR__ . '/../layouts/alerts.php';

$old = $old ?? [];
$errors = $errors ?? [];
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-gradient-cuponera text-white p-4 text-center border-0">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white text-cuponera rounded-circle p-3 mb-2 shadow-xs" style="width: 56px; height: 56px;">
                        <i class="bi bi-person-fill-add fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">Registro de Cliente</h3>
                    <p class="text-white-50 small mb-0">Crea tu cuenta personal para acceder a cupones exclusivos en El Salvador</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger border-danger-subtle rounded-3 shadow-xs mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-octagon-fill"></i> Se encontraron errores en el formulario:
                            </h6>
                            <ul class="mb-0 small ps-3">
                                <?php foreach ($errors as $campo => $err): ?>
                                    <li><strong><?= e(ucfirst($campo)) ?>:</strong> <?= e($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('/registro') ?>" method="POST" id="formRegistro" novalidate>
                        <?= Csrf::field() ?>

                        <!-- sección 1: datos de la cuenta -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-cuponera fs-7 mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock-fill"></i> 1. Credenciales de Acceso
                            </h6>

                            <div class="row g-3">
                                <!-- nombre de usuario -->
                                <div class="col-md-6">
                                    <label for="usuario" class="form-label fw-semibold small text-dark">
                                        Nombre de Usuario <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-at"></i></span>
                                        <input type="text" 
                                               class="form-control rounded-end-3 <?= isset($errors['usuario']) ? 'is-invalid' : '' ?>" 
                                               id="usuario" 
                                               name="usuario" 
                                               value="<?= e($old['usuario'] ?? '') ?>" 
                                               placeholder="ej. carlos_sv" 
                                               required 
                                               pattern="^[a-zA-Z0-9_]{4,30}$">
                                        <?php if (isset($errors['usuario'])): ?>
                                            <div class="invalid-feedback w-100"><?= e($errors['usuario']) ?></div>
                                        <?php else: ?>
                                            <div class="form-text small w-100">Entre 4 y 30 caracteres alfanuméricos.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- correo electrónico -->
                                <div class="col-md-6">
                                    <label for="correo" class="form-label fw-semibold small text-dark">
                                        Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" 
                                               class="form-control rounded-end-3 <?= isset($errors['correo']) ? 'is-invalid' : '' ?>" 
                                               id="correo" 
                                               name="correo" 
                                               value="<?= e($old['correo'] ?? '') ?>" 
                                               placeholder="nombre@ejemplo.com" 
                                               required>
                                        <?php if (isset($errors['correo'])): ?>
                                            <div class="invalid-feedback w-100"><?= e($errors['correo']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- contraseña -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold small text-dark">
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                        <input type="password" 
                                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Mínimo 8 caracteres" 
                                               required>
                                        <button class="btn btn-outline-secondary border-start-0 rounded-end-3 toggle-password" 
                                                type="button" 
                                                data-target="password"
                                                title="Mostrar / Ocultar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback d-block w-100"><?= e($errors['password']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- confirmar contraseña -->
                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-semibold small text-dark">
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-all"></i></span>
                                        <input type="password" 
                                               class="form-control rounded-end-3 <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                                               id="password_confirm" 
                                               name="password_confirm" 
                                               placeholder="Repite tu contraseña" 
                                               required>
                                        <?php if (isset($errors['password_confirm'])): ?>
                                            <div class="invalid-feedback w-100"><?= e($errors['password_confirm']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- caja interactiva de requisitos de contraseña -->
                            <div class="p-3 bg-light rounded-3 mt-3 border small" id="passwordRulesCard">
                                <span class="fw-bold text-dark d-block mb-1">
                                    <i class="bi bi-shield-check text-cuponera me-1"></i> Requisitos de seguridad para la contraseña:
                                </span>
                                <div class="row g-1 text-muted" id="pwdChecklist">
                                    <div class="col-sm-6" id="rule-length"><i class="bi bi-circle me-1"></i> Mínimo 8 caracteres</div>
                                    <div class="col-sm-6" id="rule-upper"><i class="bi bi-circle me-1"></i> Al menos una mayúscula (A-Z)</div>
                                    <div class="col-sm-6" id="rule-lower"><i class="bi bi-circle me-1"></i> Al menos una minúscula (a-z)</div>
                                    <div class="col-sm-6" id="rule-number"><i class="bi bi-circle me-1"></i> Al menos un número (0-9)</div>
                                    <div class="col-sm-12" id="rule-special"><i class="bi bi-circle me-1"></i> Al menos un carácter especial (ej. !@#$%&*?-)</div>
                                </div>
                            </div>
                        </div>

                        <!-- sección 2: datos personales -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-cuponera fs-7 mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="bi bi-person-lines-fill"></i> 2. Información Personal del Cliente
                            </h6>

                            <div class="row g-3">
                                <!-- nombres -->
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label fw-semibold small text-dark">
                                        Nombres <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control rounded-3 <?= isset($errors['nombres']) ? 'is-invalid' : '' ?>" 
                                           id="nombres" 
                                           name="nombres" 
                                           value="<?= e($old['nombres'] ?? '') ?>" 
                                           placeholder="ej. Juan Carlos" 
                                           required>
                                    <?php if (isset($errors['nombres'])): ?>
                                        <div class="invalid-feedback w-100"><?= e($errors['nombres']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- apellidos -->
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label fw-semibold small text-dark">
                                        Apellidos <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control rounded-3 <?= isset($errors['apellidos']) ? 'is-invalid' : '' ?>" 
                                           id="apellidos" 
                                           name="apellidos" 
                                           value="<?= e($old['apellidos'] ?? '') ?>" 
                                           placeholder="ej. Pérez Alvarado" 
                                           required>
                                    <?php if (isset($errors['apellidos'])): ?>
                                        <div class="invalid-feedback w-100"><?= e($errors['apellidos']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- dui con formateador -->
                                <div class="col-md-6">
                                    <label for="dui" class="form-label fw-semibold small text-dark">
                                        DUI (Documento Único de Identidad) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" 
                                               class="form-control rounded-end-3 <?= isset($errors['dui']) ? 'is-invalid' : '' ?>" 
                                               id="dui" 
                                               name="dui" 
                                               value="<?= e($old['dui'] ?? '') ?>" 
                                               placeholder="00000000-0" 
                                               maxlength="10" 
                                               required>
                                        <?php if (isset($errors['dui'])): ?>
                                            <div class="invalid-feedback w-100"><?= e($errors['dui']) ?></div>
                                        <?php else: ?>
                                            <div class="form-text small w-100">Formato salvadoreño: 8 dígitos + guion + 1 dígito.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- fecha de nacimiento con validación 18+ en vivo -->
                                <div class="col-md-6">
                                    <label for="fecha_nacimiento" class="form-label fw-semibold small text-dark">
                                        Fecha de Nacimiento (18+ años) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" 
                                               class="form-control rounded-end-3 <?= isset($errors['fecha_nacimiento']) ? 'is-invalid' : '' ?>" 
                                               id="fecha_nacimiento" 
                                               name="fecha_nacimiento" 
                                               value="<?= e($old['fecha_nacimiento'] ?? '') ?>" 
                                               max="<?= date('Y-m-d') ?>" 
                                               required>
                                        <?php if (isset($errors['fecha_nacimiento'])): ?>
                                            <div class="invalid-feedback w-100"><?= e($errors['fecha_nacimiento']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- indicador reactivo de mayoría de edad -->
                                    <div id="ageFeedbackBadge" class="mt-1 small"></div>
                                </div>

                                <!-- teléfono (opcional) -->
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label fw-semibold small text-dark">
                                        Teléfono de Contacto <span class="text-muted fw-normal">(Opcional)</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control rounded-3" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?= e($old['telefono'] ?? '') ?>" 
                                           placeholder="ej. 7123-4567">
                                </div>

                                <!-- dirección (opcional) -->
                                <div class="col-md-6">
                                    <label for="direccion" class="form-label fw-semibold small text-dark">
                                        Dirección Residencial <span class="text-muted fw-normal">(Opcional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control rounded-3" 
                                           id="direccion" 
                                           name="direccion" 
                                           value="<?= e($old['direccion'] ?? '') ?>" 
                                           placeholder="Municipio, Departamento">
                                </div>
                            </div>
                        </div>

                        <!-- sección 3: términos y condiciones -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input <?= isset($errors['terminos']) ? 'is-invalid' : '' ?>" 
                                       type="checkbox" 
                                       id="terminos" 
                                       name="terminos" 
                                       value="1" 
                                       required>
                                <label class="form-check-label small text-secondary" for="terminos">
                                    Confirmo que soy <strong>mayor de 18 años</strong>, que los datos suministrados son fidedignos y acepto los <a href="#" class="text-cuponera text-decoration-none">Términos del Servicio</a> y la <a href="#" class="text-cuponera text-decoration-none">Política de Privacidad</a> de La Cuponera SV.
                                </label>
                                <?php if (isset($errors['terminos'])): ?>
                                    <div class="invalid-feedback w-100"><?= e($errors['terminos']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- botón de envío -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-cuponera btn-lg rounded-pill fw-bold shadow-xs py-2" id="btnSubmitRegistro">
                                <i class="bi bi-check-circle-fill me-1"></i> Completar Registro de Cliente
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top border-light">
                        <p class="text-muted small mb-0">
                            ¿Ya tienes una cuenta registrada?
                            <a href="<?= url('/login') ?>" class="text-cuponera fw-bold text-decoration-none">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
