<?php
require_once __DIR__ . '/../config/database.php';
class ReporteController {
    public function index(): void {
        Auth::requireRole('administrador');
        require __DIR__ . '/../views/admin/reportes.php';
    }
    public function cupones(): void {
        Auth::requireRole('administrador');
        $resumen = $this->obtenerResumen();
        require __DIR__ . '/../views/admin/reporte_cupones.php';
    }
    public function ganancias(): void {
        Auth::requireRole('administrador');
        $resumen = $this->obtenerResumen();
        require __DIR__ . '/../views/admin/reporte_ganancias.php';
    }
    private function obtenerResumen(): array {
        $db = Database::getConnection();
        return $db->query("SELECT e.id,e.nombre_comercial,e.comision_porcentaje,
   COUNT(c.id) AS vendidos,
   COALESCE(SUM(f.total),0) AS ingresos,
   COALESCE(SUM(f.total * e.comision_porcentaje / 100),0) AS ganancias,
   COALESCE(SUM(f.total * (100-e.comision_porcentaje) / 100),0) AS neto_empresa
   FROM empresas e
   LEFT JOIN promociones p ON p.id_empresa=e.id
   LEFT JOIN cupones_comprados c ON c.id_promocion=p.id
   LEFT JOIN facturas f ON f.id_cupon=c.id
   GROUP BY e.id,e.nombre_comercial,e.comision_porcentaje
   ORDER BY vendidos DESC,e.nombre_comercial")->fetchAll();
    }
}
