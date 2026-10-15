-- =============================================================================
-- proyecto académico: la cuponera sv (lis941)
-- datos demostrativos para pruebas académicas
-- contraseña unificada para usuarios demo: cuponera2025!
-- hash generado con password_hash('cuponera2025!', password_default):
-- $2y$10$/cbzv6k/wf/yrpnee5agfotigz6snjeczr7crf9f5cbsipfwbksf2
-- =============================================================================

SET NAMES utf8mb4;

-- 1. inserción de roles del sistema
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'administrador', 'Administrador general del sistema con facultades de gestión y aprobación'),
(2, 'cliente', 'Cliente que adquiere y canjea cupones de descuento'),
(3, 'empresa', 'Empresa proveedora que publica y gestiona ofertas de cupones')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

-- 2. inserción de rubros comerciales (esqueleto referencial)
INSERT INTO `rubros` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Gastronomía y Restaurantes', 'Restaurantes, cafeterías y servicios de comida'),
(2, 'Belleza y Cuidado Personal', 'Spas, salones de belleza y barberías'),
(3, 'Entretenimiento y Turismo', 'Hoteles, parques recreativos y eventos'),
(4, 'Salud y Bienestar', 'Clínicas dentales, ópticas y gimnasios')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 3. inserción de usuarios demostrativos
-- contraseña común: cuponera2025!
INSERT INTO `usuarios` (`id`, `id_rol`, `usuario`, `correo`, `password_hash`, `estado`) VALUES
-- administrador de demostración (esqueleto)
(1, 1, 'admin_demo', 'admin@lacuponera.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
-- empresa aprobada de demostración
(2, 3, 'pizzanostra', 'contacto@pizzanostra.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
-- empresa pendiente de aprobación (para verificar reglas de control de acceso)
(3, 3, 'cafebistro', 'contacto@cafebistro.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
-- cliente de demostración
(4, 2, 'juanperez', 'juan.perez@example.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo')
ON DUPLICATE KEY UPDATE `usuario` = VALUES(`usuario`);

-- 4. perfil de empresa aprobada
INSERT INTO `empresas` (`id`, `id_usuario`, `id_rubro`, `nombre_comercial`, `razon_social`, `nit`, `telefono`, `correo_contacto`, `direccion`, `comision_porcentaje`, `estado_aprobacion`) VALUES
(1, 2, 1, 'Pizzería La Nostra', 'La Nostra Pizza S.A. de C.V.', '0614-150290-101-2', '2222-3344', 'contacto@pizzanostra.sv', 'Colonia Escalón, Paseo General Escalón #3420, San Salvador', 10.00, 'aprobada'),
(2, 3, 1, 'Café & Bistro San Salvador', 'Café & Bistro El Salvador S.A.', '0614-200595-102-8', '2255-7788', 'contacto@cafebistro.sv', 'Boulevard del Hipódromo #450, San Benito, San Salvador', 12.00, 'pendiente')
ON DUPLICATE KEY UPDATE `nombre_comercial` = VALUES(`nombre_comercial`);

-- 5. perfil de cliente demostrativo
INSERT INTO `clientes` (`id`, `id_usuario`, `nombres`, `apellidos`, `dui`, `fecha_nacimiento`, `telefono`, `direccion`) VALUES
(1, 4, 'Juan Carlos', 'Pérez Alvarado', '02345678-9', '1995-04-12', '7123-4567', 'Residencial San Antonio, Santa Tecla, La Libertad')
ON DUPLICATE KEY UPDATE `nombres` = VALUES(`nombres`);
