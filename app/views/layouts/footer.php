<footer class="bg-dark text-white pt-5 pb-4 mt-auto border-top border-secondary-subtle">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- columna 1: presentación institucional -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-icon d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-3" style="width: 32px; height: 32px;">
                        <i class="bi bi-tag-fill fs-6"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-white">La Cuponera <span class="badge bg-warning text-dark fs-7">SV</span></h5>
                </div>
                <p class="text-white-50 small mb-3">
                    Plataforma académica integral de cupones, promociones y descuentos para el mercado de El Salvador. Proyecto desarrollado para la asignatura LIS941.
                </p>
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
                    <i class="bi bi-person-check-fill me-1"></i> Persona 1: Autenticación, Registro y Esqueleto
                </div>
            </div>

            <!-- columna 2: enlaces rápidos -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-uppercase text-warning fs-7 mb-3">Navegación</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li><a href="<?= url('/') ?>" class="text-white-50 text-decoration-none hover-white">Inicio</a></li>
                    <li><a href="<?= url('/#promociones') ?>" class="text-white-50 text-decoration-none hover-white">Ofertas Demostrativas</a></li>
                    <li><a href="<?= url('/#como-funciona') ?>" class="text-white-50 text-decoration-none hover-white">¿Cómo Funciona?</a></li>
                    <li><a href="<?= url('/login') ?>" class="text-white-50 text-decoration-none hover-white">Iniciar Sesión</a></li>
                    <li><a href="<?= url('/registro') ?>" class="text-white-50 text-decoration-none hover-white">Registro de Clientes</a></li>
                </ul>
            </div>

            <!-- columna 3: información académica lis941 -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-uppercase text-warning fs-7 mb-3">Ficha Académica</h6>
                <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                    <li><i class="bi bi-mortarboard-fill text-warning me-2"></i> Lenguajes Interpretados en el Servidor (LIS941)</li>
                    <li><i class="bi bi-code-slash text-warning me-2"></i> PHP 8 + PDO + MySQL + Bootstrap 5</li>
                    <li><i class="bi bi-diagram-3-fill text-warning me-2"></i> Arquitectura Modelo-Vista-Controlador (MVC)</li>
                    <li><i class="bi bi-shield-lock-fill text-warning me-2"></i> Criptografía con <code>password_hash()</code></li>
                </ul>
            </div>

            <!-- columna 4: alcance y limitaciones del módulo -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-uppercase text-warning fs-7 mb-3">Alcance Persona 1</h6>
                <p class="small text-white-50 mb-2">
                    Este módulo implementa el 100% funcional del registro de clientes (18+), autenticación unificada clientes/empresas, y el esqueleto común de base de datos.
                </p>
                <p class="small text-white-50 mb-0">
                    <span class="text-warning">*</span> Publicación de ofertas, canje, facturación y reportes reservados para la integración de los compañeros.
                </p>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
            <div>
                &copy; <?= date('Y') ?> <strong>La Cuponera SV</strong> — Proyecto Académico LIS941. Todos los derechos reservados.
            </div>
            <div class="d-flex gap-3">
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">El Salvador &bull; Zona Horaria America/El_Salvador</span>
            </div>
        </div>
    </div>
</footer>

<!-- bootstrap 5.3.3 js bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- scripts propios de la cuponera sv -->
<script src="<?= asset('/js/app.js') ?>"></script>
<?php if (isset($extraScript)): ?>
    <script src="<?= asset($extraScript) ?>"></script>
<?php endif; ?>
</body>
</html>
