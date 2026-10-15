<?php
/**
 * ============================================================================
 * la cuponera sv - controlador de cliente (clientecontroller)
 * ============================================================================
 * vista inicial y panel privado de clientes individuales autenticados.
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    /**
     * muestra el panel principal del cliente autenticado
     */
    public function dashboard(): void
    {
        // control estricto de acceso por rol 'cliente'
        Auth::requireRole('cliente');

        $userId = Auth::id();
        $cliente = Cliente::findByUserId($userId);

        if (!$cliente) {
            Flash::error('No se pudo encontrar la información del perfil de cliente.');
            header('Location: ' . url('/login'));
            exit;
        }

        // calcular edad actual a partir de la fecha de nacimiento registrada
        $dob = new DateTime($cliente['fecha_nacimiento']);
        $today = new DateTime('today');
        $edad = $today->diff($dob)->y;

        require __DIR__ . '/../views/cliente/dashboard.php';
    }
}
