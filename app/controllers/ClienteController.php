<?php
/** Panel de cliente. Evita el bucle login -> dashboard -> login si falta el perfil. */
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Cupon.php';

class ClienteController
{
    public function dashboard(): void
    {
        Auth::requireRole('cliente');
        $userId = Auth::id();
        $cliente = Cliente::findByUserId((int)$userId);

        if (!$cliente) {
            // Nunca redirigir a /login cuando ya existe una sesión autenticada.
            http_response_code(409);
            require __DIR__ . '/../views/cliente/perfil_incompleto.php';
            return;
        }

        $cupones = Cupon::misCupones((int)$userId);
        $cuponesActivos = array_values(array_filter($cupones, static function (array $cupon): bool {
            return $cupon['estado'] === 'disponible' && $cupon['fecha_limite_canje'] >= date('Y-m-d');
        }));
        $totalCupones = count($cupones);
        $totalActivos = count($cuponesActivos);

        $dob = new DateTime($cliente['fecha_nacimiento']);
        $today = new DateTime('today');
        $edad = $today->diff($dob)->y;
        require __DIR__ . '/../views/cliente/dashboard.php';
    }
}
