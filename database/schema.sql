-- =============================================================================
-- proyecto académico: la cuponera sv (lis941)
-- base de datos: lacuponera_sv
-- módulo: persona 1 (autenticación, registro de clientes y esqueleto compartido)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. tabla: roles
-- catálogo de roles del sistema.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(30) NOT NULL UNIQUE COMMENT 'administrador, cliente, empresa',
    `descripcion` VARCHAR(150) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. tabla: usuarios
-- núcleo de autenticación compartido para todos los actores del sistema.
-- separa credenciales de los datos específicos de cada perfil.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_rol` INT NOT NULL,
    `usuario` VARCHAR(50) NOT NULL UNIQUE,
    `correo` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `estado` ENUM('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_usuarios_usuario` ON `usuarios` (`usuario`);
CREATE INDEX `idx_usuarios_correo` ON `usuarios` (`correo`);
CREATE INDEX `idx_usuarios_estado` ON `usuarios` (`estado`);

-- -----------------------------------------------------------------------------
-- 3. tabla: clientes
-- perfil de cliente individual (módulo persona 1).
-- vinculado 1:1 con la tabla usuarios.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL UNIQUE,
    `nombres` VARCHAR(80) NOT NULL,
    `apellidos` VARCHAR(80) NOT NULL,
    `dui` VARCHAR(10) NOT NULL UNIQUE COMMENT 'formato 00000000-0 (8 dígitos + guion + 1 dígito)',
    `fecha_nacimiento` DATE NOT NULL COMMENT 'debe ser mayor de 18 años al registrarse',
    `telefono` VARCHAR(20) NULL,
    `direccion` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_clientes_dui` ON `clientes` (`dui`);

-- -----------------------------------------------------------------------------
-- 4. tabla: rubros (esqueleto compartido para catálogo comercial)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rubros` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(60) NOT NULL UNIQUE,
    `descripcion` VARCHAR(150) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. tabla: empresas
-- perfil empresarial (esqueleto compartido para integración de compañeros).
-- vinculado 1:1 con la tabla usuarios.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `empresas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL UNIQUE,
    `id_rubro` INT NULL,
    `nombre_comercial` VARCHAR(120) NOT NULL,
    `razon_social` VARCHAR(150) NOT NULL,
    `nit` VARCHAR(20) NOT NULL UNIQUE COMMENT 'formato salvadoreño nit',
    `telefono` VARCHAR(20) NOT NULL,
    `correo_contacto` VARCHAR(100) NOT NULL,
    `direccion` TEXT NOT NULL,
    `comision_porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    `estado_aprobacion` ENUM('pendiente', 'aprobada', 'rechazada') NOT NULL DEFAULT 'pendiente',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_empresas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_empresas_rubro` FOREIGN KEY (`id_rubro`) REFERENCES `rubros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_empresas_nit` ON `empresas` (`nit`);
CREATE INDEX `idx_empresas_estado_aprobacion` ON `empresas` (`estado_aprobacion`);

-- -----------------------------------------------------------------------------
-- 6. tabla: recuperacion_tokens
-- base técnica compartida para recuperación de contraseña.
-- almacena un token hash protegido, su expiración y si ya fue consumido.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperacion_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL UNIQUE COMMENT 'hash sha-256 del token enviado por correo',
    `expira_en` DATETIME NOT NULL,
    `usado` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_recuperacion_hash` ON `recuperacion_tokens` (`token_hash`);
CREATE INDEX `idx_recuperacion_expira` ON `recuperacion_tokens` (`expira_en`);

-- -----------------------------------------------------------------------------
-- 7. esqueleto compartido: estructura referencial para integración de compañeros
-- las siguientes tablas se definen como base relacional documentada para que
-- persona 2 y persona 3 integren ofertas, compras y canjes sin colisiones.
-- -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `promociones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_empresa` INT NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `precio_regular` DECIMAL(10,2) NOT NULL,
    `precio_oferta` DECIMAL(10,2) NOT NULL,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE NOT NULL,
    `fecha_limite_canje` DATE NOT NULL,
    `cantidad_limite` INT NULL COMMENT 'null si es ilimitada',
    `imagen_url` VARCHAR(255) NULL,
    `estado` ENUM('activa', 'pausada', 'vencida', 'descartada') NOT NULL DEFAULT 'activa',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_promociones_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cupones_comprados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_promocion` INT NOT NULL,
    `id_cliente` INT NOT NULL,
    `codigo_cupon` VARCHAR(20) NOT NULL UNIQUE,
    `fecha_compra` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_canje` DATETIME NULL,
    `estado` ENUM('disponible', 'canjeado', 'vencido') NOT NULL DEFAULT 'disponible',
    CONSTRAINT `fk_cupones_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_cupones_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
