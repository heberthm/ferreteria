-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-01-2026 a las 03:15:35
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ferreteria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_menor`
--

DROP TABLE IF EXISTS `caja_menor`;
CREATE TABLE IF NOT EXISTS `caja_menor` (
  `id_caja` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_actual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cerrada',
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observaciones_apertura` text COLLATE utf8mb4_unicode_ci,
  `observaciones_cierre` text COLLATE utf8mb4_unicode_ci,
  `user_id_apertura` bigint UNSIGNED DEFAULT NULL,
  `user_id_cierre` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_caja`),
  KEY `caja_menor_user_id_apertura_foreign` (`user_id_apertura`),
  KEY `caja_menor_user_id_cierre_foreign` (`user_id_cierre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `userId`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, '1', 'herramientas manuales', 'herramientas de uso manual', '2026-01-12 03:51:04', '2026-01-12 03:51:04'),
(2, '1', 'herramientas eléctricas', 'herramientas de uso eléctrico', '2026-01-12 03:51:27', '2026-01-12 03:51:27'),
(3, '1', 'herramientas de medición', 'herramientas de medición y marcado', '2026-01-12 03:52:21', '2026-01-12 03:52:21'),
(4, '1', 'plomería y tubería', 'elementos de plomería y tubería', '2026-01-12 03:53:04', '2026-01-12 03:53:04'),
(5, '1', 'elementos eléctricos', 'elementos de uso eléctricos', '2026-01-12 03:53:37', '2026-01-12 03:53:37'),
(6, '1', 'pintura y acabados', 'elementos para pintura y acabados', '2026-01-12 03:54:25', '2026-01-12 03:54:25'),
(7, '1', 'materiales de construción', 'materiales para uso en la construcción', '2026-01-12 03:55:06', '2026-01-12 03:55:06'),
(8, '1', 'mariales de aseo', 'materiales para uso de aseo', '2026-01-12 03:55:44', '2026-01-12 03:55:44'),
(9, '1', 'miscelaneos', 'miscelaneos', '2026-01-12 03:56:07', '2026-01-12 03:56:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cliente` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `userId`, `cedula`, `nombre`, `email`, `telefono`, `direccion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1', '31340203', 'luz enith calvo villada', 'luzcalvo@gmail.com', '3334228 - 31233593482', 'calle 23 #  21 - 40', '2026-01-12 16:59:28', '2026-01-12 16:59:28', NULL),
(2, '1', '34248521', 'diana milena soto rodriguez', 'dianasoto@gmail.com', '3345670 - 3124305390', 'cra 4 # 12 - 45', '2026-01-13 16:15:54', '2026-01-13 16:15:54', NULL),
(3, '1', '32482451', 'adiela serna forero', 'adielaserna@gmail.com', '3334228 - 31233593482', 'calle 5 # 45 c - 50', '2026-01-13 16:16:47', '2026-01-13 16:16:47', NULL),
(4, '1', '31457218', 'rosa moreno dominguez', 'rosamoreno@gmail.com', '2943490 - 31345939538', 'calle 15 # 21 -23', '2026-01-13 16:17:33', '2026-01-13 16:17:33', NULL),
(5, '1', '33183485', 'rodrigo gómez solarte', 'rodrigogomez@gmail.com', '3345670 - 3124305390', 'cra 13 # 45 - 82', '2026-01-13 16:18:37', '2026-01-13 16:18:37', NULL),
(6, '1', '34222539', 'maria angelica martinez ramirez', 'marimartinez@gmail.com', '2943490 - 31345939538', 'cra 3 #  5 - 21', '2026-01-13 16:19:35', '2026-01-13 16:19:35', NULL),
(7, '1', '33142583', 'lorena rentería moreno', 'lorenarenteria@gmail.com', '3345670 - 3124305390', 'cra 3 # 11 - 45', '2026-01-13 16:20:27', '2026-01-13 16:20:27', NULL),
(8, '1', '34183593', 'amalfi moreno rodriguez', 'amalfimoreno@gmail.com', '3394209 - 31324031029', 'cra 11 # 21 - 56', '2026-01-13 16:21:47', '2026-01-13 16:21:47', NULL),
(9, '1', '33459128', 'luisa marina tenorio ortega', 'luisatenorio@gmail.com', '3343924  - 3134285827', 'calle 5 # 11 - 49', '2026-01-13 16:24:10', '2026-01-13 16:24:10', NULL),
(10, '1', '94238128', 'mario arcila correa', 'marioarcila@gmail.com', '3394209 - 31324031029', 'calle 6 # 21 - 45', '2026-01-13 16:24:57', '2026-01-13 16:24:57', NULL),
(11, '1', '94291583', 'hernando gonzález perea', 'hernandogonzalez@gmail.com', '2943490 - 31345939538', 'calle 27 # 12 - 43', '2026-01-13 16:25:49', '2026-01-13 16:25:49', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_facturas`
--

DROP TABLE IF EXISTS `detalles_facturas`;
CREATE TABLE IF NOT EXISTS `detalles_facturas` (
  `id_detalle_factura` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_factura` bigint UNSIGNED NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle_factura`),
  KEY `detalles_facturas_id_factura_foreign` (`id_factura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
CREATE TABLE IF NOT EXISTS `detalle_ventas` (
  `id_detalle_venta` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_venta` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle_venta`),
  KEY `detalle_ventas_id_venta_index` (`id_venta`),
  KEY `detalle_ventas_id_producto_index` (`id_producto`),
  KEY `detalle_ventas_id_venta_id_producto_index` (`id_venta`,`id_producto`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle_venta`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '15000.00', '15000.00', '2026-01-13 19:30:50', '2026-01-13 19:30:50'),
(2, 1, 10, 1, '7000.00', '7000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(3, 1, 5, 1, '16000.00', '16000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(4, 1, 9, 1, '29000.00', '29000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(5, 1, 7, 1, '20000.00', '20000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(6, 1, 13, 1, '9000.00', '9000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(7, 1, 14, 1, '15000.00', '15000.00', '2026-01-13 19:30:51', '2026-01-13 19:30:51'),
(8, 5, 1, 1, '15000.00', '15000.00', '2026-01-14 03:04:39', '2026-01-14 03:04:39'),
(9, 5, 3, 1, '22000.00', '22000.00', '2026-01-14 03:04:40', '2026-01-14 03:04:40'),
(10, 5, 4, 1, '7500.00', '7500.00', '2026-01-14 03:04:40', '2026-01-14 03:04:40'),
(11, 5, 5, 1, '16000.00', '16000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(12, 5, 6, 1, '167000.00', '167000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(13, 5, 9, 1, '29000.00', '29000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(14, 5, 10, 1, '7000.00', '7000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(15, 5, 8, 1, '12000.00', '12000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(16, 5, 14, 1, '15000.00', '15000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41'),
(17, 5, 13, 1, '9000.00', '9000.00', '2026-01-14 03:04:41', '2026-01-14 03:04:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `cliente` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medico` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

DROP TABLE IF EXISTS `facturas`;
CREATE TABLE IF NOT EXISTS `facturas` (
  `id_factura` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_cliente` bigint UNSIGNED NOT NULL,
  `id_cita` bigint UNSIGNED DEFAULT NULL,
  `numero_factura` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagada','Cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_factura`),
  UNIQUE KEY `facturas_numero_factura_unique` (`numero_factura`),
  KEY `facturas_id_cliente_foreign` (`id_cliente`),
  KEY `facturas_id_cita_foreign` (`id_cita`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE IF NOT EXISTS `gastos` (
  `id_gasto` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `concepto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `comprobante` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','cheque','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `tipo` enum('diario','mensual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diario',
  `estado` enum('pendiente','pagado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagado',
  `id_categoria_gasto` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_gasto`),
  KEY `gastos_id_categoria_gasto_foreign` (`id_categoria_gasto`),
  KEY `gastos_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

DROP TABLE IF EXISTS `inventarios`;
CREATE TABLE IF NOT EXISTS `inventarios` (
  `id_inventario` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `stock_minimo` int NOT NULL DEFAULT '5',
  `stock_maximo` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_inventario`),
  KEY `inventarios_id_producto_foreign` (`id_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_03_13_191452_create_clientes_table', 1),
(6, '2025_03_14_194925_create_facturas_table', 1),
(7, '2025_03_14_195827_create_detalles_facturas_table', 1),
(8, '2025_04_02_033532_create_events_table', 1),
(9, '2025_05_10_045748_create_caja_menor_table', 1),
(10, '2025_05_10_051846_create_ventas_table', 1),
(11, '2025_05_13_044745_create_productos_table', 1),
(12, '2025_05_13_052441_create_categorias_table', 1),
(13, '2025_05_13_052831_create_proveedores_table', 1),
(14, '2025_05_13_053532_create_detalle_ventas_table', 1),
(15, '2025_05_15_051329_create_gastos_table', 1),
(16, '2025_05_15_161218_create_inventarios_table', 1),
(17, '2025_08_27_135600_create_movimiento_caja_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_caja`
--

DROP TABLE IF EXISTS `movimiento_caja`;
CREATE TABLE IF NOT EXISTS `movimiento_caja` (
  `id_movimiento_caja` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_caja` bigint UNSIGNED NOT NULL,
  `tipo` enum('ingreso','egreso') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `concepto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `comprobante` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userId` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_caja`),
  KEY `movimiento_caja_id_caja_index` (`id_caja`),
  KEY `movimiento_caja_userid_index` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_compra` int NOT NULL,
  `precio_venta` int NOT NULL,
  `stock` int NOT NULL,
  `stock_minimo` int NOT NULL DEFAULT '5',
  `ultima_venta` timestamp NULL DEFAULT NULL,
  `unidad_medida` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `id_categoria` bigint UNSIGNED NOT NULL,
  `id_proveedor` bigint UNSIGNED NOT NULL,
  `frecuente` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_id_categoria_foreign` (`id_categoria`),
  KEY `productos_id_proveedor_foreign` (`id_proveedor`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `userId`, `codigo`, `nombre`, `descripcion`, `marca`, `categoria`, `cantidad`, `precio_compra`, `precio_venta`, `stock`, `stock_minimo`, `ultima_venta`, `unidad_medida`, `ubicacion`, `proveedor`, `imagen`, `activo`, `id_categoria`, `id_proveedor`, `frecuente`, `created_at`, `updated_at`) VALUES
(1, '1', '21389', 'botella alcohol', 'botella de alcohol de 450 cc al 70%', 'medic', 'miscelaneos', 12, 78000, 15000, 37, 5, NULL, 'cc', 'Estante No. 5', 'medic', 'storage/images/vynmXKvCl5YDfd3YhoLK_1768190425.webp', 1, 1, 1, 0, '2026-01-12 04:00:26', '2026-01-14 03:04:40'),
(3, '1', '4290', 'alicate metálico', 'alicate metálico mango de caucho', 'drop', 'herramientas manuales', 10, 11000, 22000, 28, 3, NULL, 'cm', 'Estante No.1', 'drop', 'storage/images/o4hV1deo5Uk23EI7f8sC_1768238276.jpg', 1, 1, 1, 0, '2026-01-12 17:17:56', '2026-01-14 03:04:40'),
(4, '1', '3421', 'bolsas para basura', 'bolsas negras para basura 60x90 cm', 'lifan', 'mariales de aseo', 35, 3600, 7500, 58, 6, NULL, 'cm', 'Estante No. 2', 'lifan', 'storage/images/FoYWiREdUqVdXfCju3ko_1768240123.webp', 1, 1, 1, 0, '2026-01-12 17:48:43', '2026-01-14 03:04:41'),
(5, '1', '3190', 'destornillador mediano', 'destornillador mediano metálico', 'stiller', 'herramientas manuales', 12, 7500, 16000, 47, 6, NULL, 'cm', 'Estante No. 5', 'stiller', 'storage/images/oO0bRWBe6AR29wdH7Eig_1768240405.jpg', 1, 1, 1, 0, '2026-01-12 17:53:26', '2026-01-14 03:04:41'),
(6, '1', '2319', 'galón pintura', 'cuñete de pintura de agua', 'pintuco', 'pintura y acabados', 6, 120000, 167000, 28, 6, NULL, 'Gls', 'Estante No. 4', 'pintuco', 'storage/images/ynZa49voZeWshRHCSi0z_1768240568.png', 1, 1, 1, 0, '2026-01-12 17:56:08', '2026-01-14 03:04:41'),
(7, '1', '3129', 'martillo mango caucho', 'martillo mango caucho', 'stiller', 'herramientas manuales', 12, 8900, 20000, 39, 4, NULL, 'cm', 'Estante No. 5', 'stiller', 'storage/images/1i2OZGhxAnbmeph9CAcG_1768243375.png', 1, 1, 1, 0, '2026-01-12 18:42:55', '2026-01-13 19:30:51'),
(8, '1', '3290', 'pintura anticorrosiva', 'pintura anticorrosiva', 'pintuco', 'pintura y acabados', 10, 6000, 12000, 39, 4, NULL, 'ml', 'Estante No. 3', 'pintuco', 'storage/images/AbQcMYkjrG4edCGbwMLk_1768243622.jpg', 1, 1, 1, 0, '2026-01-12 18:47:02', '2026-01-14 03:04:41'),
(9, '1', '1429', 'serrucho mango caucho', 'serrucho mediano mando de caucho', 'marck', 'herramientas manuales', 4, 14000, 29000, 28, 3, NULL, 'cm', 'Estante No. 3', 'marck', 'storage/images/O1tLgUz7PBrh8r4ANLVN_1768243939.jpg', 1, 1, 1, 0, '2026-01-12 18:52:19', '2026-01-14 03:04:41'),
(10, '1', '2930', 'botella de thiner', 'botella de thiner de 450 ml', 'lafer', 'pintura y acabados', 8, 3500, 7000, 18, 4, NULL, 'ml', 'Estante No. 2', 'lafer', 'storage/images/o28Ts2BTe1AmzgiCYS1s_1768244609.jpeg', 1, 1, 1, 0, '2026-01-12 19:03:29', '2026-01-14 03:04:41'),
(11, '1', '2839', 'Tornillo de 1/2\"', 'tornillo galvanizado rosca fina', 'tolls', 'herramientas manuales', 24, 680, 1800, 60, 4, NULL, 'pulgada', 'Estante No. 2', 'tolls', 'storage/images/PeBtn5T4VugsVw9Cqzm0_1768244783.jpg', 1, 1, 1, 0, '2026-01-12 19:06:23', '2026-01-12 19:06:23'),
(12, '1', '2190', 'tubo de pvc x 1.80 mts', 'tubo de pvc x  1.80 mts', 'rospol', 'materiales de construción', 6, 7600, 16000, 15, 5, NULL, 'mts', 'Estante No. 5', 'drole', 'storage/images/YZ7Lt3E0HLvAld4YuB6D_1768244970.jpg', 1, 1, 1, 0, '2026-01-12 19:09:30', '2026-01-12 19:09:30'),
(13, '1', '1934', 'cinta adhesiva', 'cinta adhesiva 1.5 mts', '3M', 'herramientas de medición', 6, 4900, 9000, 17, 5, NULL, 'mts', 'Estante No. 5', '3M', 'storage/images/MkkOHj3VeibWTGaQjtXd_1768248550.png', 1, 1, 1, 0, '2026-01-12 20:09:11', '2026-01-14 03:04:41'),
(14, '1', '3229', 'brocha mediana', 'brocha mediana', 'ressol', 'pintura y acabados', 12, 6700, 15000, 21, 4, NULL, 'cm', 'Estante No. 4', 'ressol', 'storage/images/cOXIcjZnTrumpS6C37WN_1768248921.jpeg', 1, 1, 1, 0, '2026-01-12 20:15:21', '2026-01-14 03:04:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_contacto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `rol`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'heberth mazuera arana', 'heberth.mazuera@gmail.com', 'admin', NULL, '$2y$10$POoqqaoxJ21D6XLro1/9nuhR.A2FCRSJd0ZEgiZ3OcKWg3Yi0TNVa', NULL, '2021-11-04 05:03:54', '2021-11-04 05:03:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id_venta` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint UNSIGNED DEFAULT NULL,
  `userId` bigint UNSIGNED NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `numero_factura` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productos` json DEFAULT NULL,
  `subtotal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iva` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descuento` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `estado` enum('pendiente','pagado','cancelado','completada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_comprobante` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sistema',
  `referencia_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `efectivo_recibido` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cambio` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`),
  UNIQUE KEY `ventas_numero_factura_unique` (`numero_factura`),
  KEY `ventas_id_cliente_foreign` (`id_cliente`),
  KEY `ventas_userid_foreign` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_cliente`, `userId`, `fecha_venta`, `numero_factura`, `productos`, `subtotal`, `iva`, `total`, `descuento`, `estado`, `notas`, `metodo_pago`, `tipo_comprobante`, `vendedor`, `referencia_pago`, `efectivo_recibido`, `cambio`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-01-13 18:09:43', 'F-00001', '[{\"id\": 1, \"codigo\": \"21389\", \"nombre\": \"botella alcohol\", \"cantidad\": 1, \"subtotal\": 15000, \"categoria\": \"miscelaneos\", \"precio_unitario\": 15000}, {\"id\": 3, \"codigo\": \"4290\", \"nombre\": \"alicate metálico\", \"cantidad\": 1, \"subtotal\": 22000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 22000}, {\"id\": 4, \"codigo\": \"3421\", \"nombre\": \"bolsas para basura\", \"cantidad\": 1, \"subtotal\": 7500, \"categoria\": \"mariales de aseo\", \"precio_unitario\": 7500}, {\"id\": 7, \"codigo\": \"3129\", \"nombre\": \"martillo mango caucho\", \"cantidad\": 1, \"subtotal\": 20000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 20000}, {\"id\": 8, \"codigo\": \"3290\", \"nombre\": \"pintura anticorrosiva\", \"cantidad\": 2, \"subtotal\": 24000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 12000}, {\"id\": 9, \"codigo\": \"1429\", \"nombre\": \"serrucho mango caucho\", \"cantidad\": 1, \"subtotal\": 29000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 29000}, {\"id\": 10, \"codigo\": \"2930\", \"nombre\": \"botella de thiner\", \"cantidad\": 1, \"subtotal\": 7000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 7000}, {\"id\": 12, \"codigo\": \"2190\", \"nombre\": \"tubo de pvc x 1.80 mts\", \"cantidad\": 1, \"subtotal\": 16000, \"categoria\": \"materiales de construción\", \"precio_unitario\": 16000}, {\"id\": 13, \"codigo\": \"1934\", \"nombre\": \"cinta adhesiva\", \"cantidad\": 1, \"subtotal\": 9000, \"categoria\": \"herramientas de medición\", \"precio_unitario\": 9000}]', '149500', '23920', '173420', '0', 'completada', NULL, 'efectivo', 'ticket', 'Sistema', NULL, '190000', '17', '2026-01-13 23:09:44', '2026-01-13 23:09:44'),
(2, 8, 1, '2026-01-13 19:33:01', 'F-00002', '[{\"id\": 1, \"codigo\": \"21389\", \"nombre\": \"botella alcohol\", \"cantidad\": 1, \"subtotal\": 15000, \"categoria\": \"miscelaneos\", \"precio_unitario\": 15000}, {\"id\": 4, \"codigo\": \"3421\", \"nombre\": \"bolsas para basura\", \"cantidad\": 1, \"subtotal\": 7500, \"categoria\": \"mariales de aseo\", \"precio_unitario\": 7500}, {\"id\": 3, \"codigo\": \"4290\", \"nombre\": \"alicate metálico\", \"cantidad\": 1, \"subtotal\": 22000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 22000}, {\"id\": 5, \"codigo\": \"3190\", \"nombre\": \"destornillador mediano\", \"cantidad\": 1, \"subtotal\": 16000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 16000}, {\"id\": 6, \"codigo\": \"2319\", \"nombre\": \"galón pintura\", \"cantidad\": 1, \"subtotal\": 167000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 167000}, {\"id\": 7, \"codigo\": \"3129\", \"nombre\": \"martillo mango caucho\", \"cantidad\": 1, \"subtotal\": 20000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 20000}, {\"id\": 8, \"codigo\": \"3290\", \"nombre\": \"pintura anticorrosiva\", \"cantidad\": 1, \"subtotal\": 12000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 12000}, {\"id\": 9, \"codigo\": \"1429\", \"nombre\": \"serrucho mango caucho\", \"cantidad\": 1, \"subtotal\": 29000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 29000}, {\"id\": 10, \"codigo\": \"2930\", \"nombre\": \"botella de thiner\", \"cantidad\": 2, \"subtotal\": 14000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 7000}, {\"id\": 11, \"codigo\": \"2839\", \"nombre\": \"Tornillo de 1/2\\\"\", \"cantidad\": 2, \"subtotal\": 3600, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 1800}, {\"id\": 12, \"codigo\": \"2190\", \"nombre\": \"tubo de pvc x 1.80 mts\", \"cantidad\": 2, \"subtotal\": 32000, \"categoria\": \"materiales de construción\", \"precio_unitario\": 16000}, {\"id\": 13, \"codigo\": \"1934\", \"nombre\": \"cinta adhesiva\", \"cantidad\": 2, \"subtotal\": 18000, \"categoria\": \"herramientas de medición\", \"precio_unitario\": 9000}, {\"id\": 14, \"codigo\": \"3229\", \"nombre\": \"brocha mediana\", \"cantidad\": 1, \"subtotal\": 15000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 15000}]', '371100', '59376', '430476', '0', 'completada', NULL, 'efectivo', 'ticket', 'Sistema', NULL, '500000', '69524', '2026-01-14 00:33:01', '2026-01-14 00:33:01'),
(3, 8, 1, '2026-01-13 19:39:24', 'F-00003', '[{\"id\": 1, \"codigo\": \"21389\", \"nombre\": \"botella alcohol\", \"cantidad\": 1, \"subtotal\": 15000, \"categoria\": \"miscelaneos\", \"precio_unitario\": 15000}, {\"id\": 3, \"codigo\": \"4290\", \"nombre\": \"alicate metálico\", \"cantidad\": 1, \"subtotal\": 22000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 22000}, {\"id\": 4, \"codigo\": \"3421\", \"nombre\": \"bolsas para basura\", \"cantidad\": 1, \"subtotal\": 7500, \"categoria\": \"mariales de aseo\", \"precio_unitario\": 7500}, {\"id\": 5, \"codigo\": \"3190\", \"nombre\": \"destornillador mediano\", \"cantidad\": 1, \"subtotal\": 16000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 16000}, {\"id\": 6, \"codigo\": \"2319\", \"nombre\": \"galón pintura\", \"cantidad\": 1, \"subtotal\": 167000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 167000}, {\"id\": 7, \"codigo\": \"3129\", \"nombre\": \"martillo mango caucho\", \"cantidad\": 1, \"subtotal\": 20000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 20000}, {\"id\": 8, \"codigo\": \"3290\", \"nombre\": \"pintura anticorrosiva\", \"cantidad\": 1, \"subtotal\": 12000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 12000}, {\"id\": 9, \"codigo\": \"1429\", \"nombre\": \"serrucho mango caucho\", \"cantidad\": 1, \"subtotal\": 29000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 29000}, {\"id\": 10, \"codigo\": \"2930\", \"nombre\": \"botella de thiner\", \"cantidad\": 1, \"subtotal\": 7000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 7000}, {\"id\": 11, \"codigo\": \"2839\", \"nombre\": \"Tornillo de 1/2\\\"\", \"cantidad\": 1, \"subtotal\": 1800, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 1800}, {\"id\": 12, \"codigo\": \"2190\", \"nombre\": \"tubo de pvc x 1.80 mts\", \"cantidad\": 1, \"subtotal\": 16000, \"categoria\": \"materiales de construción\", \"precio_unitario\": 16000}, {\"id\": 13, \"codigo\": \"1934\", \"nombre\": \"cinta adhesiva\", \"cantidad\": 1, \"subtotal\": 9000, \"categoria\": \"herramientas de medición\", \"precio_unitario\": 9000}]', '322300', '51568', '373868', '0', 'completada', NULL, 'efectivo', 'ticket', 'Sistema', NULL, '380000', '6132', '2026-01-14 00:39:24', '2026-01-14 00:39:24'),
(4, 6, 1, '2026-01-13 21:11:01', 'F-00004', '[{\"id\": 1, \"codigo\": \"21389\", \"nombre\": \"botella alcohol\", \"cantidad\": 1, \"subtotal\": 15000, \"categoria\": \"miscelaneos\", \"precio_unitario\": 15000}, {\"id\": 3, \"codigo\": \"4290\", \"nombre\": \"alicate metálico\", \"cantidad\": 1, \"subtotal\": 22000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 22000}, {\"id\": 5, \"codigo\": \"3190\", \"nombre\": \"destornillador mediano\", \"cantidad\": 1, \"subtotal\": 16000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 16000}, {\"id\": 6, \"codigo\": \"2319\", \"nombre\": \"galón pintura\", \"cantidad\": 1, \"subtotal\": 167000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 167000}, {\"id\": 7, \"codigo\": \"3129\", \"nombre\": \"martillo mango caucho\", \"cantidad\": 1, \"subtotal\": 20000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 20000}, {\"id\": 8, \"codigo\": \"3290\", \"nombre\": \"pintura anticorrosiva\", \"cantidad\": 1, \"subtotal\": 12000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 12000}, {\"id\": 9, \"codigo\": \"1429\", \"nombre\": \"serrucho mango caucho\", \"cantidad\": 1, \"subtotal\": 29000, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 29000}, {\"id\": 10, \"codigo\": \"2930\", \"nombre\": \"botella de thiner\", \"cantidad\": 1, \"subtotal\": 7000, \"categoria\": \"pintura y acabados\", \"precio_unitario\": 7000}, {\"id\": 11, \"codigo\": \"2839\", \"nombre\": \"Tornillo de 1/2\\\"\", \"cantidad\": 1, \"subtotal\": 1800, \"categoria\": \"herramientas manuales\", \"precio_unitario\": 1800}]', '289800', '46368', '336168', '0', 'completada', NULL, 'efectivo', 'ticket', 'Sistema', NULL, '350000', '13832', '2026-01-14 02:11:01', '2026-01-14 02:11:01'),
(5, 8, 1, '2026-01-13 22:04:39', 'F-00005', NULL, '299500', '47920', '347420', '0', 'completada', NULL, 'efectivo', 'ticket', 'Sistema', NULL, '350000', '2580', '2026-01-14 03:04:39', '2026-01-14 03:04:39');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
