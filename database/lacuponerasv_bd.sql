CREATE DATABASE IF NOT EXISTS `lacuponera_sv`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lacuponera_sv`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(150) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_roles_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rubros` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(60) NOT NULL,
  `descripcion` VARCHAR(150) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_rubros_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_rol` INT NOT NULL,
  `usuario` VARCHAR(50) NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `estado` ENUM('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_usuario` (`usuario`),
  UNIQUE KEY `uq_usuarios_correo` (`correo`),
  KEY `idx_usuarios_estado` (`estado`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `nombres` VARCHAR(80) NOT NULL,
  `apellidos` VARCHAR(80) NOT NULL,
  `dui` VARCHAR(10) NOT NULL COMMENT 'formato 00000000-0',
  `fecha_nacimiento` DATE NOT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_clientes_id_usuario` (`id_usuario`),
  UNIQUE KEY `uq_clientes_dui` (`dui`),
  CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `empresas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `id_rubro` INT DEFAULT NULL,
  `nombre_comercial` VARCHAR(120) NOT NULL,
  `razon_social` VARCHAR(150) NOT NULL,
  `nit` VARCHAR(20) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  `correo_contacto` VARCHAR(100) NOT NULL,
  `direccion` TEXT NOT NULL,
  `comision_porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `estado_aprobacion` ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empresas_id_usuario` (`id_usuario`),
  UNIQUE KEY `uq_empresas_nit` (`nit`),
  KEY `idx_empresas_estado_aprobacion` (`estado_aprobacion`),
  KEY `idx_empresas_rubro` (`id_rubro`),
  CONSTRAINT `fk_empresas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_empresas_rubro` FOREIGN KEY (`id_rubro`) REFERENCES `rubros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Cupones, facturas, recuperación
CREATE TABLE IF NOT EXISTS `promociones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `precio_regular` DECIMAL(10,2) NOT NULL,
  `precio_oferta` DECIMAL(10,2) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `fecha_limite_canje` DATE NOT NULL,
  `cantidad_limite` INT DEFAULT NULL COMMENT 'NULL si es ilimitada',
  `imagen_url` VARCHAR(255) DEFAULT NULL,
  `estado` ENUM('activa','pausada','vencida','descartada') NOT NULL DEFAULT 'activa',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_promociones_empresa` (`id_empresa`),
  CONSTRAINT `fk_promociones_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cupones_comprados` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_promocion` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `codigo_cupon` VARCHAR(20) NOT NULL,
  `fecha_compra` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_canje` DATETIME DEFAULT NULL,
  `estado` ENUM('disponible','canjeado','vencido') NOT NULL DEFAULT 'disponible',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cupones_codigo` (`codigo_cupon`),
  KEY `idx_cupones_promocion` (`id_promocion`),
  KEY `idx_cupones_cliente` (`id_cliente`),
  CONSTRAINT `fk_cupones_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cupones_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facturas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_cupon` INT NOT NULL,
  `numero_factura` VARCHAR(40) NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `metodo_pago` VARCHAR(30) NOT NULL DEFAULT 'simulacion',
  `fecha_emision` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_facturas_cupon` (`id_cupon`),
  UNIQUE KEY `uq_facturas_numero` (`numero_factura`),
  CONSTRAINT `fk_facturas_cupon` FOREIGN KEY (`id_cupon`) REFERENCES `cupones_comprados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `recuperacion_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL,
  `expira_en` DATETIME NOT NULL,
  `usado` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recuperacion_hash` (`token_hash`),
  KEY `idx_recuperacion_expira` (`expira_en`),
  KEY `idx_recuperacion_usuario` (`id_usuario`),
  CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales solicitados. Importar solo en una BD nueva/vacía.
START TRANSACTION;

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'administrador', 'Administrador general del sistema con facultades de gestión y aprobación'),
(2, 'cliente', 'Cliente que adquiere y canjea cupones de descuento'),
(3, 'empresa', 'Empresa proveedora que publica y gestiona ofertas de cupones')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

INSERT INTO `rubros` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Gastronomía y Restaurantes', 'Restaurantes, cafeterías y servicios de comida'),
(2, 'Belleza y Cuidado Personal', 'Spas, salones de belleza y barberías'),
(3, 'Entretenimiento y Turismo', 'Hoteles, parques recreativos y eventos'),
(4, 'Salud y Bienestar', 'Clínicas dentales, ópticas y gimnasios')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `descripcion` = VALUES(`descripcion`);

-- Los hashes de los usuarios de demostración fueron aportados en el enunciado.
-- La contraseña correspondiente al hash suministrado debe verificarse con password_verify().
INSERT INTO `usuarios` (`id`, `id_rol`, `usuario`, `correo`, `password_hash`, `estado`) VALUES
(1, 1, 'admin_demo', 'admin@lacuponera.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
(2, 3, 'pizzanostra', 'contacto@pizzanostra.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
(3, 3, 'cafebistro', 'contacto@cafebistro.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
(4, 2, 'juanperez', 'juan.perez@example.sv', '$2y$10$/CBZv6K/WF/yRPNeE5AgfOtiGZ6sNJecZr7crF9F5cbSiPFWbksf2', 'activo'),
-- Cuenta vinculada a la única empresa conservada de los datos anteriores.
(6, 3, 'Clinica_TuSonrisa', 'tusonrisa@gmail.com', '$2y$10$ZJ43Hjr2LbAPFjC/97Bshet8ElfPINd2oyrGreTute3RPSVMZ6qTq', 'activo')
ON DUPLICATE KEY UPDATE `usuario` = VALUES(`usuario`);

INSERT INTO `empresas` (`id`, `id_usuario`, `id_rubro`, `nombre_comercial`, `razon_social`, `nit`, `telefono`, `correo_contacto`, `direccion`, `comision_porcentaje`, `estado_aprobacion`) VALUES
(1, 2, 1, 'Pizzería La Nostra', 'La Nostra Pizza S.A. de C.V.', '0614-150290-101-2', '2222-3344', 'contacto@pizzanostra.sv', 'Colonia Escalón, Paseo General Escalón #3420, San Salvador', 10.00, 'aprobada'),
(2, 3, 1, 'Café & Bistro San Salvador', 'Café & Bistro El Salvador S.A.', '0614-200595-102-8', '2255-7788', 'contacto@cafebistro.sv', 'Boulevard del Hipódromo #450, San Benito, San Salvador', 12.00, 'pendiente'),
-- Clínica Tu Sonrisa, única empresa del respaldo original que se conserva.
(3, 6, 4, 'Clinica Tu Sonrisa', 'Clinica dental', '1234-010199-123', '22331100', 'tusonrisa@gmail.com', 'Calle y Edificio La Mascota, Alameda Manuel Enrique Araujo No.11, San Salvador', 15.00, 'aprobada')
ON DUPLICATE KEY UPDATE `nombre_comercial` = VALUES(`nombre_comercial`);

INSERT INTO `clientes` (`id`, `id_usuario`, `nombres`, `apellidos`, `dui`, `fecha_nacimiento`, `telefono`, `direccion`) VALUES
(1, 4, 'Juan Carlos', 'Pérez Alvarado', '02345678-9', '1995-04-12', '7123-4567', 'Residencial San Antonio, Santa Tecla, La Libertad')
ON DUPLICATE KEY UPDATE `nombres` = VALUES(`nombres`);

-- Única promoción conservada del respaldo anterior; se actualiza id_empresa a 3.
INSERT INTO `promociones` (`id`, `id_empresa`, `titulo`, `descripcion`, `precio_regular`, `precio_oferta`, `fecha_inicio`, `fecha_fin`, `fecha_limite_canje`, `cantidad_limite`, `imagen_url`, `estado`) VALUES
(1, 3, 'Limpieza Dental con Ultrasonido + Pulido Dental + Aplicación de Flúor y Más',
 '¡Paga $10 en Lugar de $50 por Limpieza Dental con Ultrasonido + Pulido Dental + Aplicación de Flúor + Consulta y Diagnóstico + Evaluación de Cordales!',
 50.00, 10.00, '2026-10-19', '2026-12-31', '2027-01-08', 2, NULL, 'activa')
ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);

INSERT INTO `cupones_comprados`
  (`id`, `id_promocion`, `id_cliente`, `codigo_cupon`, `fecha_compra`, `fecha_canje`, `estado`)
VALUES
  (1, 1, 1, 'SV-AF5BD1DF978786F7', '2026-10-24 00:00:58', NULL, 'disponible')
ON DUPLICATE KEY UPDATE
  `id_promocion` = VALUES(`id_promocion`),
  `id_cliente` = VALUES(`id_cliente`),
  `codigo_cupon` = VALUES(`codigo_cupon`),
  `fecha_compra` = VALUES(`fecha_compra`),
  `fecha_canje` = VALUES(`fecha_canje`),
  `estado` = VALUES(`estado`);


INSERT INTO `facturas`
  (`id`, `id_cupon`, `numero_factura`, `total`, `metodo_pago`, `fecha_emision`)
VALUES
  (1, 1, 'FAC-20261024-0000001', 10.00, 'simulacion', '2026-10-24 00:00:58')
ON DUPLICATE KEY UPDATE
  `id_cupon` = VALUES(`id_cupon`),
  `numero_factura` = VALUES(`numero_factura`),
  `total` = VALUES(`total`),
  `metodo_pago` = VALUES(`metodo_pago`),
  `fecha_emision` = VALUES(`fecha_emision`);

COMMIT;


SELECT 'roles' AS tabla, COUNT(*) AS registros FROM roles
UNION ALL SELECT 'rubros', COUNT(*) FROM rubros
UNION ALL SELECT 'usuarios', COUNT(*) FROM usuarios
UNION ALL SELECT 'empresas', COUNT(*) FROM empresas
UNION ALL SELECT 'clientes', COUNT(*) FROM clientes
UNION ALL SELECT 'promociones', COUNT(*) FROM promociones
UNION ALL SELECT 'cupones_comprados', COUNT(*) FROM cupones_comprados
UNION ALL SELECT 'facturas', COUNT(*) FROM facturas;



SELECT f.numero_factura, f.total, f.fecha_emision,
       cc.codigo_cupon, c.nombres, c.apellidos,
       p.titulo AS promocion, e.nombre_comercial AS empresa
FROM facturas f
INNER JOIN cupones_comprados cc ON cc.id = f.id_cupon
INNER JOIN clientes c ON c.id = cc.id_cliente
INNER JOIN promociones p ON p.id = cc.id_promocion
INNER JOIN empresas e ON e.id = p.id_empresa
WHERE f.id = 1;


SELECT cc.id, cc.codigo_cupon, cc.estado, cc.fecha_compra,
       CONCAT(c.nombres, ' ', c.apellidos) AS cliente,
       p.titulo AS promocion, e.nombre_comercial AS empresa,
       f.numero_factura, f.total AS total_facturado
FROM cupones_comprados AS cc
JOIN clientes AS c ON c.id = cc.id_cliente
JOIN promociones AS p ON p.id = cc.id_promocion
JOIN empresas AS e ON e.id = p.id_empresa
LEFT JOIN facturas AS f ON f.id_cupon = cc.id
ORDER BY cc.id;
