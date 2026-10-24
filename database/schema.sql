
CREATE DATABASE IF NOT EXISTS `lacuponera_sv`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lacuponera_sv`;



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `dui` varchar(10) NOT NULL COMMENT 'formato 00000000-0',
  `fecha_nacimiento` date NOT NULL COMMENT 'validar mayoría de edad en la aplicación',
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `clientes` (`id`, `id_usuario`, `nombres`, `apellidos`, `dui`, `fecha_nacimiento`, `telefono`, `direccion`, `created_at`, `updated_at`) VALUES
(1, 7, 'Hazel', 'Garcia Vasquez', '05885386-3', '1999-05-13', '79703887', 'Residencial Miraflores , block c , casa #23 , san salvador', '2026-10-23 22:10:31', '2026-10-23 22:10:31'),
(2, 2, 'Juan', 'Perez', '01234567-8', '1998-05-15', '70000000', 'San Salvador, El Salvador', '2026-10-23 22:40:01', '2026-10-23 22:40:01');


CREATE TABLE `cupones_comprados` (
  `id` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `codigo_cupon` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_canje` datetime DEFAULT NULL,
  `estado` enum('disponible','canjeado','vencido') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `cupones_comprados` (`id`, `id_promocion`, `id_cliente`, `codigo_cupon`, `fecha_compra`, `fecha_canje`, `estado`) VALUES
(1, 1, 2, 'SV-AF5BD1DF978786F7', '2026-10-24 00:00:58', NULL, 'disponible');



CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_rubro` int(11) DEFAULT NULL,
  `nombre_comercial` varchar(120) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo_contacto` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `comision_porcentaje` decimal(5,2) NOT NULL DEFAULT 10.00,
  `estado_aprobacion` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `empresas` (`id`, `id_usuario`, `id_rubro`, `nombre_comercial`, `razon_social`, `nit`, `telefono`, `correo_contacto`, `direccion`, `comision_porcentaje`, `estado_aprobacion`, `created_at`, `updated_at`) VALUES
(1, 6, 2, 'Clinica Tu Sonrisa', 'Clinica dental', '1234-010199-123', '22331100', 'tusonrisa@gmail.com', 'Calle y Edificio La Mascota, Alameda Manuel Enrique Araujo No.11, San Salvador', 15.00, 'aprobada', '2026-10-23 21:34:28', '2026-10-23 21:37:47');


CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `id_cupon` int(11) NOT NULL,
  `numero_factura` varchar(40) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(30) NOT NULL DEFAULT 'simulacion',
  `fecha_emision` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `facturas` (`id`, `id_cupon`, `numero_factura`, `total`, `metodo_pago`, `fecha_emision`) VALUES
(1, 1, 'FAC-20261024-0000001', 10.00, 'simulacion', '2026-10-24 00:00:58');

CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `precio_regular` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_limite_canje` date NOT NULL,
  `cantidad_limite` int(11) DEFAULT NULL COMMENT 'NULL si es ilimitada',
  `imagen_url` varchar(255) DEFAULT NULL,
  `estado` enum('activa','pausada','vencida','descartada') NOT NULL DEFAULT 'activa',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `promociones` (`id`, `id_empresa`, `titulo`, `descripcion`, `precio_regular`, `precio_oferta`, `fecha_inicio`, `fecha_fin`, `fecha_limite_canje`, `cantidad_limite`, `imagen_url`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'Limpieza Dental con Ultrasonido + Pulido Dental + Aplicación de Flúor y Más', '¡Paga $10 en Lugar de $50 por Limpieza Dental con Ultrasonido + Pulido Dental + Aplicación de Flúor + Consulta y Diagnóstico + Evaluación de Cordales!\r\n\r\n¡Paga $10 en Lugar de $50 por Limpieza Dental con Ultrasonido + Pulido Dental + Aplicación de Flúor + Consulta y Diagnóstico + Evaluación de Cordales!', 50.00, 10.00, '2026-10-19', '2026-12-31', '2027-01-08', 2, NULL, 'activa', '2026-10-23 22:07:21', '2026-10-23 22:07:21');


CREATE TABLE `recuperacion_tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------



CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL COMMENT 'administrador, cliente, empresa',
  `descripcion` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'administrador', 'Administración del sistema', '2026-10-22 21:13:55'),
(2, 'cliente', 'Cliente que compra cupones', '2026-10-22 21:13:55'),
(3, 'empresa', 'Empresa que publica promociones', '2026-10-22 21:13:55');


CREATE TABLE `rubros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `rubros` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Restaurantes', 'Gastronomía', '2026-10-22 23:20:00'),
(2, 'Salud', 'Bienestar', '2026-10-22 23:20:00');


CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `usuarios` (`id`, `id_rol`, `usuario`, `correo`, `password_hash`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin_demo', 'admin_demo@udb.com', '$2y$12$rnFVJpaJI3Sys0X0jLarOuAOKIc2qxbX4yRdfRo7ErK2VqBgrepcG', 'activo', '2026-10-22 22:28:40', '2026-10-22 22:44:23'),
(2, 2, 'juanperez', 'juanperez@gmail.com', '$2y$12$rnFVJpaJI3Sys0X0jLarOuAOKIc2qxbX4yRdfRo7ErK2VqBgrepcG', 'activo', '2026-10-18 22:28:40', '2026-10-22 22:44:23'),
(3, 3, 'pizzanostra', 'pizzanostra@gmail.com', '$2y$12$rnFVJpaJI3Sys0X0jLarOuAOKIc2qxbX4yRdfRo7ErK2VqBgrepcG', 'activo', '2026-10-19 22:31:07', '2026-10-22 22:44:23'),
(4, 1, 'support_demo', 'support_demo@gmail.com', '$2y$10$aOFs3EZr1EUMlpOBFGJDWesGkKeDznbkq7DihfgE0iSeJv7qkSPEC', 'activo', '2026-10-22 22:59:11', '2026-10-22 22:59:11'),
(5, 1, 'support2_demo', 'support2_demo@gmail.com', '$2y$10$jvF39RsbgnHSmLARLczxsOzOScEQuIyCBVOcat5Lrf6OAk6vJb/Pa', 'activo', '2026-10-23 20:56:12', '2026-10-23 20:56:12'),
(6, 3, 'Clinica_TuSonrisa', 'tusonrisa@gmail.com', '$2y$10$ZJ43Hjr2LbAPFjC/97Bshet8ElfPINd2oyrGreTute3RPSVMZ6qTq', 'activo', '2026-10-23 21:34:28', '2026-10-23 21:34:28'),
(7, 2, 'hgarcia', 'hazel.alexia@gmail.com', '$2y$10$Vuh410Mq5/F70oLIN4Oaae2S.iXmnomlcHGYuBOCSj.lvvxD1bpn6', 'activo', '2026-10-23 22:10:31', '2026-10-23 22:10:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD UNIQUE KEY `dui` (`dui`);


ALTER TABLE `cupones_comprados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_cupon` (`codigo_cupon`),
  ADD KEY `fk_cupones_promocion` (`id_promocion`),
  ADD KEY `fk_cupones_cliente` (`id_cliente`);

ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD UNIQUE KEY `nit` (`nit`),
  ADD KEY `idx_empresas_estado_aprobacion` (`estado_aprobacion`),
  ADD KEY `fk_empresas_rubro` (`id_rubro`);

ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_cupon` (`id_cupon`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`);


ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_promociones_empresa` (`id_empresa`);

--
-- Indices de la tabla `recuperacion_tokens`
--
ALTER TABLE `recuperacion_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `idx_recuperacion_expira` (`expira_en`),
  ADD KEY `fk_recuperacion_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);


ALTER TABLE `rubros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);


ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_usuarios_estado` (`estado`),
  ADD KEY `fk_usuarios_rol` (`id_rol`);


ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `cupones_comprados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `recuperacion_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `rubros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;


ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `cupones_comprados`
  ADD CONSTRAINT `fk_cupones_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cupones_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id`) ON UPDATE CASCADE;


ALTER TABLE `empresas`
  ADD CONSTRAINT `fk_empresas_rubro` FOREIGN KEY (`id_rubro`) REFERENCES `rubros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_empresas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `facturas`
  ADD CONSTRAINT `fk_facturas_cupon` FOREIGN KEY (`id_cupon`) REFERENCES `cupones_comprados` (`id`);


ALTER TABLE `promociones`
  ADD CONSTRAINT `fk_promociones_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `recuperacion_tokens`
  ADD CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
