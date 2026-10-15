<?php
/**
 * ============================================================================
 * la cuponera sv - controlador de empresa (empresacontroller)
 * ============================================================================
 * vista inicial y panel privado preparado para empresas autenticadas.
 * muestra el estado de aprobación y los puntos de integración para publicación de ofertas.
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Empresa.php';

class EmpresaController
{
    /**
     * muestra el panel principal de la empresa autenticada
     */
    public function dashboard(): void
    {
        // control estricto de acceso por rol 'empresa'
        Auth::requireRole('empresa');

        $userId = Auth::id();
        $empresa = Empresa::findByUserId($userId);

        if (!$empresa) {
            Flash::error('No se pudo encontrar el perfil empresarial asociado a la cuenta.');
            header('Location: ' . url('/login'));
            exit;
        }

        require __DIR__ . '/../views/empresa/dashboard.php';
    }
}
