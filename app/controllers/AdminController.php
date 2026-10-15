<?php
/**
 * ============================================================================
 * la cuponera sv - controlador administrativo (admincontroller)
 * ============================================================================
 * punto de integración para los módulos de administración general.
 * requiere rol 'administrador' y presenta el esqueleto compartido preparado.
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../config/database.php';

class AdminController
{
    /**
     * muestra el panel base administrativo y los puntos de integración
     */
    public function dashboard(): void
    {
        Auth::requireRole('administrador');

        $db = Database::getConnection();

        // conteos estadísticos de la estructura común
        $totalUsuarios = (int)$db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $totalClientes = (int)$db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        $totalEmpresas = (int)$db->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
        $empresasPendientes = (int)$db->query("SELECT COUNT(*) FROM empresas WHERE estado_aprobacion = 'pendiente'")->fetchColumn();

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
