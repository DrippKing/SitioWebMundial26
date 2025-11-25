-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 25-11-2025 a las 09:43:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `poi_database`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(3) NOT NULL,
  `grupo` char(1) NOT NULL,
  `bandera` varchar(255) DEFAULT NULL,
  `puntos` int(11) DEFAULT 0,
  `partidos_jugados` int(11) DEFAULT 0,
  `partidos_ganados` int(11) DEFAULT 0,
  `partidos_empatados` int(11) DEFAULT 0,
  `partidos_perdidos` int(11) DEFAULT 0,
  `goles_favor` int(11) DEFAULT 0,
  `goles_contra` int(11) DEFAULT 0,
  `diferencia_goles` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `codigo`, `grupo`, `bandera`, `puntos`, `partidos_jugados`, `partidos_ganados`, `partidos_empatados`, `partidos_perdidos`, `goles_favor`, `goles_contra`, `diferencia_goles`, `created_at`) VALUES
(1, 'Qatar', 'QAT', 'A', 'qatar.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(2, 'Ecuador', 'ECU', 'A', 'ecuador.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(3, 'Senegal', 'SEN', 'A', 'senegal.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(4, 'Países Bajos', 'NED', 'A', 'paisesbajos.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(5, 'Inglaterra', 'ENG', 'B', 'inglaterra.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(6, 'Irán', 'IRN', 'B', 'iran.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(7, 'Estados Unidos', 'USA', 'B', 'usa.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(8, 'Gales', 'WAL', 'B', 'gales.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(9, 'Argentina', 'ARG', 'C', 'argentina.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(10, 'Arabia Saudita', 'KSA', 'C', 'arabiasaudita.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(11, 'México', 'MEX', 'C', 'mexico.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(12, 'Polonia', 'POL', 'C', 'polonia.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(13, 'Francia', 'FRA', 'D', 'francia.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(14, 'Australia', 'AUS', 'D', 'australia.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(15, 'Dinamarca', 'DEN', 'D', 'dinamarca.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(16, 'Túnez', 'TUN', 'D', 'tunez.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(17, 'España', 'ESP', 'E', 'espana.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(18, 'Costa Rica', 'CRC', 'E', 'costarica.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(19, 'Alemania', 'GER', 'E', 'alemania.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(20, 'Japón', 'JPN', 'E', 'japon.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(21, 'Bélgica', 'BEL', 'F', 'belgica.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(22, 'Canadá', 'CAN', 'F', 'canada.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(23, 'Marruecos', 'MAR', 'F', 'marruecos.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(24, 'Croacia', 'CRO', 'F', 'croacia.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(25, 'Brasil', 'BRA', 'G', 'brasil.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(26, 'Serbia', 'SRB', 'G', 'serbia.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(27, 'Suiza', 'SUI', 'G', 'suiza.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(28, 'Camerún', 'CMR', 'G', 'camerun.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(29, 'Portugal', 'POR', 'H', 'portugal.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(30, 'Ghana', 'GHA', 'H', 'ghana.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(31, 'Uruguay', 'URU', 'H', 'uruguay.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19'),
(32, 'Corea del Sur', 'KOR', 'H', 'coreadelsur.png', 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-24 18:47:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `created_at`) VALUES
(1, 4, 1, '2025-11-24 16:57:24'),
(2, 1, 4, '2025-11-24 16:57:24'),
(3, 6, 1, '2025-11-25 04:59:00'),
(4, 1, 6, '2025-11-25 04:59:00'),
(5, 6, 4, '2025-11-25 05:00:13'),
(6, 4, 6, '2025-11-25 05:00:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `sender_id`, `receiver_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 5, 'pending', '2025-11-24 16:53:41', '2025-11-24 16:53:41'),
(2, 4, 1, 'accepted', '2025-11-24 16:53:56', '2025-11-24 16:57:24'),
(3, 1, 2, 'pending', '2025-11-24 19:51:38', '2025-11-24 19:51:38'),
(4, 4, 2, 'pending', '2025-11-25 03:33:45', '2025-11-25 03:33:45'),
(5, 6, 4, 'accepted', '2025-11-25 04:53:09', '2025-11-25 05:00:13'),
(6, 6, 5, 'pending', '2025-11-25 04:57:26', '2025-11-25 04:57:26'),
(7, 6, 1, 'accepted', '2025-11-25 04:57:29', '2025-11-25 04:59:00'),
(8, 6, 2, 'pending', '2025-11-25 07:33:58', '2025-11-25 07:33:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `foto_grupo` varchar(255) DEFAULT 'default_group.jpg',
  `creador_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`, `descripcion`, `foto_grupo`, `creador_id`, `created_at`) VALUES
(1, 'TILINES', 'Grupo de amigos', 'default_group.jpg', 1, '2025-11-24 06:26:02'),
(2, 'LMEADOS', 'Equipo de trabajo', 'default_group.jpg', 1, '2025-11-24 06:26:02'),
(3, 'TILINES', 'Grupo de amigos', 'default_group.jpg', 1, '2025-11-24 06:26:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_miembros`
--

CREATE TABLE `grupo_miembros` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `es_admin` tinyint(1) DEFAULT 0,
  `unido_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_miembros`
--

INSERT INTO `grupo_miembros` (`id`, `grupo_id`, `usuario_id`, `es_admin`, `unido_at`) VALUES
(1, 1, 4, 0, '2025-11-24 06:27:07'),
(2, 1, 5, 0, '2025-11-24 06:27:07'),
(3, 1, 1, 0, '2025-11-24 06:27:07'),
(4, 1, 2, 0, '2025-11-24 06:27:07'),
(5, 2, 4, 0, '2025-11-24 06:27:07'),
(7, 2, 1, 0, '2025-11-24 06:27:07'),
(9, 3, 4, 0, '2025-11-24 06:27:07'),
(10, 3, 5, 0, '2025-11-24 06:27:07'),
(11, 3, 1, 0, '2025-11-24 06:27:07'),
(12, 3, 2, 0, '2025-11-24 06:27:07'),
(35, 2, 6, 0, '2025-11-25 06:58:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medallas`
--

CREATE TABLE `medallas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(255) NOT NULL,
  `condicion_tipo` enum('amigos','victorias','derrotas','ranking') NOT NULL,
  `condicion_valor` int(11) NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `medallas`
--

INSERT INTO `medallas` (`id`, `codigo`, `nombre`, `descripcion`, `icono`, `condicion_tipo`, `condicion_valor`, `activa`, `fecha_creacion`) VALUES
(1, 'primer_amigo', 'Primer Amigo', 'Agregaste a tu primer amigo', '../MEDALLAS/logro_primeramigo.png', 'amigos', 1, 1, '2025-11-24 20:35:17'),
(2, 'primera_victoria', 'Primera Victoria', 'Acertaste tu primera predicción', '../MEDALLAS/logro_primeravictoria.png', 'victorias', 1, 1, '2025-11-24 20:35:17'),
(3, 'primera_derrota', 'Primera Derrota', 'Fallaste tu primera predicción', '../MEDALLAS/logro_primeraderrota.png', 'derrotas', 1, 1, '2025-11-24 20:35:17'),
(4, 'salado', 'Salado', 'Perdiste 10 predicciones', '../MEDALLAS/logro_salado.png', 'derrotas', 10, 1, '2025-11-24 20:35:17'),
(5, 'top_global', 'Top Global', 'Llegaste al top 3 del ranking final', '../MEDALLAS/logro_topglobal.png', 'ranking', 3, 1, '2025-11-24 20:35:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL COMMENT 'ID del usuario que envía el mensaje',
  `receiver_id` int(11) NOT NULL COMMENT 'ID del usuario que recibe el mensaje',
  `message_text` text NOT NULL COMMENT 'Contenido del mensaje',
  `is_read` tinyint(1) DEFAULT 0,
  `message_type` enum('text','image','file') DEFAULT 'text',
  `file_url` varchar(255) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora de envío',
  `is_encrypted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `sender_id`, `receiver_id`, `message_text`, `is_read`, `message_type`, `file_url`, `timestamp`, `is_encrypted`) VALUES
(1, 1, 2, 'Hola', 0, 'text', NULL, '2025-10-20 23:28:16', 0),
(2, 1, 2, 'Hola', 0, 'text', NULL, '2025-10-20 23:33:54', 0),
(3, 1, 4, 'Hola', 1, 'text', NULL, '2025-10-20 23:34:00', 0),
(4, 1, 4, 'Hola', 1, 'text', NULL, '2025-10-20 23:34:13', 0),
(5, 1, 4, 'QUE HACES', 1, 'text', NULL, '2025-10-20 23:34:20', 0),
(6, 1, 4, 'Hola', 1, 'text', NULL, '2025-10-20 23:36:02', 0),
(7, 1, 4, 'Laaa taaan', 1, 'text', NULL, '2025-10-20 23:36:26', 0),
(8, 1, 4, 'Hola', 1, 'text', NULL, '2025-10-21 17:08:29', 0),
(9, 4, 1, 'xd', 1, 'text', NULL, '2025-10-21 17:08:38', 0),
(10, 1, 4, 'Soyese', 1, 'text', NULL, '2025-10-21 18:33:35', 0),
(11, 1, 4, 'Algo bierno', 1, 'text', NULL, '2025-10-21 18:34:01', 0),
(12, 1, 4, 'Hola eli', 1, 'text', NULL, '2025-10-21 18:34:13', 0),
(13, 4, 1, 'xdd', 1, 'text', NULL, '2025-10-21 18:34:24', 0),
(14, 1, 4, 'Holii danna', 1, 'text', NULL, '2025-10-21 18:49:27', 0),
(15, 1, 4, 'Chii?', 1, 'text', NULL, '2025-10-21 18:49:43', 0),
(16, 1, 4, 'Uwu', 1, 'text', NULL, '2025-10-21 18:49:49', 0),
(17, 4, 1, 'LAAA TAAAN', 1, 'text', NULL, '2025-10-21 18:50:24', 0),
(18, 1, 5, 'Hola Chris', 0, 'text', NULL, '2025-10-21 18:52:13', 0),
(19, 1, 4, 'Xdxd', 1, 'text', NULL, '2025-10-21 18:52:36', 0),
(20, 5, 1, 'xd', 1, 'text', NULL, '2025-10-21 18:53:40', 0),
(21, 1, 4, '21/10/25', 1, 'text', NULL, '2025-10-21 18:58:38', 0),
(22, 5, 1, 'No se que fecha es hoy', 1, 'text', NULL, '2025-10-21 18:58:56', 0),
(23, 4, 1, 'NOCHES', 1, 'text', NULL, '2025-10-24 20:25:26', 0),
(24, 1, 4, 'xdxdxxddxdxdxdxd', 1, 'text', NULL, '2025-10-24 20:25:46', 0),
(25, 4, 1, 'Hola', 1, 'text', NULL, '2025-11-23 23:23:18', 0),
(26, 4, 1, 'xdxdxd', 1, 'text', NULL, '2025-11-23 23:23:31', 0),
(27, 1, 4, 'Interesante', 1, 'text', NULL, '2025-11-23 23:25:39', 0),
(28, 4, 1, 'xdxd', 1, 'text', NULL, '2025-11-23 23:30:25', 0),
(29, 4, 1, 'xdxd', 1, 'text', NULL, '2025-11-23 23:41:35', 0),
(30, 4, 1, 'xd', 1, 'text', NULL, '2025-11-23 23:42:41', 0),
(31, 4, 1, 'xddd', 1, 'text', NULL, '2025-11-23 23:53:50', 0),
(32, 1, 4, 'Holaaa', 1, 'text', NULL, '2025-11-23 23:57:28', 0),
(33, 1, 4, 'Xdxd', 1, 'text', NULL, '2025-11-24 00:03:32', 0),
(34, 4, 1, 'xdxd', 1, 'text', NULL, '2025-11-24 00:03:41', 0),
(35, 4, 1, '???? oliv.jpg', 1, 'text', NULL, '2025-11-24 00:10:48', 0),
(36, 4, 1, '????', 1, 'text', NULL, '2025-11-24 00:11:03', 0),
(37, 4, 1, '???? La novia.docx', 1, 'text', NULL, '2025-11-24 00:11:50', 0),
(38, 4, 1, '???? La novia.docx', 1, 'text', NULL, '2025-11-24 00:13:16', 0),
(39, 4, 1, '???? 85ebb4fe-e9cf-4eed-be52-2f8cc4c48d18.png', 1, 'file', '6923f9e94d426_1763965417.png', '2025-11-24 00:23:37', 0),
(40, 4, 1, '????', 1, 'text', NULL, '2025-11-24 00:23:44', 0),
(41, 4, 1, '???? a.pdf', 1, 'file', '6923fa92795eb_1763965586.pdf', '2025-11-24 00:26:26', 0),
(42, 1, 5, 'Aaa', 0, 'text', NULL, '2025-11-24 00:28:08', 0),
(43, 1, 4, 'Xdddd', 1, 'text', NULL, '2025-11-24 00:28:17', 0),
(44, 1, 4, 'Increíble', 1, 'text', NULL, '2025-11-24 00:28:25', 0),
(45, 4, 1, 'xdxd', 1, 'text', NULL, '2025-11-24 00:28:29', 0),
(46, 1, 4, 'Papu', 1, 'text', NULL, '2025-11-24 00:28:37', 0),
(47, 1, 4, 'Xd', 1, 'text', NULL, '2025-11-24 00:30:18', 0),
(48, 1, 4, 'xdd', 1, 'text', NULL, '2025-11-24 10:57:54', 0),
(49, 4, 1, '????', 1, 'text', NULL, '2025-11-24 21:30:01', 0),
(50, 4, 1, '???? El Descanso y el Sueño.docx', 1, 'file', '692522c164856_1764041409.docx', '2025-11-24 21:30:09', 0),
(51, 4, 1, 'xd', 1, 'text', NULL, '2025-11-24 21:30:40', 0),
(52, 1, 4, 'Xd', 1, 'text', NULL, '2025-11-24 21:31:11', 0),
(53, 1, 4, 'Xd', 1, 'text', NULL, '2025-11-24 21:31:18', 0),
(54, 4, 1, 'xd', 1, 'text', NULL, '2025-11-24 21:31:25', 0),
(55, 4, 1, 'xd', 1, 'text', NULL, '2025-11-24 21:31:33', 0),
(56, 1, 4, 'Xd', 1, 'text', NULL, '2025-11-24 21:31:35', 0),
(57, 1, 4, 'Xd', 1, 'text', NULL, '2025-11-24 22:14:54', 0),
(58, 1, 4, 'Xdd', 1, 'text', NULL, '2025-11-24 22:22:46', 0),
(59, 4, 1, 'xd', 1, 'text', NULL, '2025-11-24 22:22:51', 0),
(60, 6, 1, '==wbkFGdwlmcj5WZgUmahNnbl1GIlRGIhJWZ1JHU', 1, 'text', NULL, '2025-11-24 23:25:02', 1),
(61, 6, 1, '=8GZhRHcpJ3YuVGIhFWYs9GS', 1, 'text', NULL, '2025-11-24 23:28:01', 1),
(62, 1, 6, 'Xdd', 1, 'text', NULL, '2025-11-24 23:28:23', 0),
(63, 6, 1, '==wbkFGdwlmcj5WR', 1, 'text', NULL, '2025-11-24 23:32:30', 1),
(64, 1, 6, 'Xdxd', 1, 'text', NULL, '2025-11-24 23:32:38', 0),
(65, 6, 1, 'xdxdxd', 1, 'text', NULL, '2025-11-24 23:32:45', 0),
(66, 1, 6, 'Holaa', 1, 'text', NULL, '2025-11-24 23:36:11', 0),
(67, 6, 1, 'vRWY0BXayNmblBCZ4RGe', 1, 'text', NULL, '2025-11-24 23:36:23', 1),
(68, 6, 1, '==wbkFGdwlmcj5WZ', 1, 'text', NULL, '2025-11-24 23:36:48', 1),
(69, 6, 1, 'vRWY0BXayNmblBCZ4RGe', 1, 'text', NULL, '2025-11-24 23:37:44', 1),
(70, 6, 1, 'xd', 1, 'text', NULL, '2025-11-24 23:37:50', 0),
(71, 1, 6, 'Xdxdxd', 1, 'text', NULL, '2025-11-24 23:38:23', 0),
(72, 6, 1, '???? 85ebb4fe-e9cf-4eed-be52-2f8cc4c48d18.png', 1, 'file', '692540f37c925_1764049139.png', '2025-11-24 23:38:59', 0),
(73, 6, 1, '???? Mi ubicación: https://www.google.com/maps?q=25.76218894176867,-100.26636572195473', 1, 'text', NULL, '2025-11-24 23:42:53', 0),
(74, 6, 1, '???? Mi ubicación: https://www.google.com/maps?q=25.762203982338583,-100.26639727757528', 1, 'text', NULL, '2025-11-24 23:46:10', 0),
(75, 6, 1, 'hola', 1, 'text', NULL, '2025-11-25 01:33:08', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_grupo`
--

CREATE TABLE `mensajes_grupo` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `message_type` enum('text','image','file') DEFAULT 'text',
  `file_url` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_encrypted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_grupo`
--

INSERT INTO `mensajes_grupo` (`id`, `grupo_id`, `sender_id`, `message_text`, `is_read`, `message_type`, `file_url`, `timestamp`, `is_encrypted`) VALUES
(1, 3, 4, 'xd', 0, 'text', NULL, '2025-11-24 06:27:48', 0),
(11, 2, 6, 'holaa', 0, 'text', NULL, '2025-11-25 07:08:30', 0),
(12, 2, 1, 'Holaa', 0, 'text', NULL, '2025-11-25 07:08:39', 0),
(13, 2, 6, '???? Mi ubicación: https://www.google.com/maps?q=25.762167068713875,-100.26634502997852', 0, 'text', NULL, '2025-11-25 07:09:06', 0),
(14, 2, 6, '???? 85ebb4fe-e9cf-4eed-be52-2f8cc4c48d18.png', 0, 'file', '692556239373c_1764054563.png', '2025-11-25 07:09:23', 0),
(15, 2, 6, '???? El Descanso y el Sueño.docx', 0, 'file', '6925564c1e8b3_1764054604.docx', '2025-11-25 07:10:04', 0),
(16, 2, 6, '???? Mi ubicación: https://www.google.com/maps?q=25.762209839306447,-100.2664057512446', 0, 'text', NULL, '2025-11-25 07:10:10', 0),
(17, 2, 1, '???? 20251114_191104.jpg', 0, 'file', '692559be6660f_1764055486.jpg', '2025-11-25 07:24:46', 0),
(18, 2, 1, '==AZ4RGW', 0, 'text', NULL, '2025-11-25 07:25:11', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `equipo_local_id` int(11) NOT NULL,
  `equipo_visitante_id` int(11) NOT NULL,
  `fase` enum('grupo','octavos','cuartos','semifinal','tercer_lugar','final') NOT NULL,
  `jornada` int(11) DEFAULT NULL,
  `grupo` char(1) DEFAULT NULL,
  `fecha_partido` datetime NOT NULL,
  `estadio` varchar(100) DEFAULT NULL,
  `goles_local` int(11) DEFAULT NULL,
  `goles_visitante` int(11) DEFAULT NULL,
  `penales_local` int(11) DEFAULT NULL,
  `penales_visitante` int(11) DEFAULT NULL,
  `finalizado` tinyint(1) DEFAULT 0,
  `ganador_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `equipo_local_id`, `equipo_visitante_id`, `fase`, `jornada`, `grupo`, `fecha_partido`, `estadio`, `goles_local`, `goles_visitante`, `penales_local`, `penales_visitante`, `finalizado`, `ganador_id`, `created_at`) VALUES
(1, 1, 2, 'grupo', 1, 'A', '2026-06-11 12:00:00', 'Al Bayt', 1, 1, NULL, NULL, 1, NULL, '2025-11-24 17:09:47'),
(2, 3, 4, 'grupo', 1, 'A', '2026-06-11 15:00:00', 'Al Thumama', 2, 1, NULL, NULL, 1, NULL, '2025-11-24 17:09:47'),
(3, 1, 3, 'grupo', 2, 'A', '2026-06-15 12:00:00', 'Al Thumama', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(4, 4, 2, 'grupo', 2, 'A', '2026-06-15 15:00:00', 'Khalifa', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(5, 4, 1, 'grupo', 3, 'A', '2026-06-19 18:00:00', 'Al Bayt', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(6, 2, 3, 'grupo', 3, 'A', '2026-06-19 18:00:00', 'Khalifa', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(7, 5, 6, 'grupo', 1, 'B', '2026-06-12 12:00:00', 'Khalifa', 1, 1, NULL, NULL, 1, NULL, '2025-11-24 17:09:47'),
(8, 7, 8, 'grupo', 1, 'B', '2026-06-12 15:00:00', 'Ahmad Bin Ali', 4, 0, NULL, NULL, 1, NULL, '2025-11-24 17:09:47'),
(9, 8, 6, 'grupo', 2, 'B', '2026-06-16 12:00:00', 'Ahmad Bin Ali', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(10, 5, 7, 'grupo', 2, 'B', '2026-06-16 15:00:00', 'Al Bayt', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(11, 8, 5, 'grupo', 3, 'B', '2026-06-20 18:00:00', 'Ahmad Bin Ali', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(12, 6, 7, 'grupo', 3, 'B', '2026-06-20 18:00:00', 'Al Thumama', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(13, 9, 10, 'grupo', 1, 'C', '2026-06-13 12:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(14, 11, 12, 'grupo', 1, 'C', '2026-06-13 15:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(15, 12, 10, 'grupo', 2, 'C', '2026-06-17 12:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(16, 9, 11, 'grupo', 2, 'C', '2026-06-17 15:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(17, 12, 9, 'grupo', 3, 'C', '2026-06-21 18:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(18, 10, 11, 'grupo', 3, 'C', '2026-06-21 18:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(19, 13, 14, 'grupo', 1, 'D', '2026-06-14 12:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(20, 15, 16, 'grupo', 1, 'D', '2026-06-14 15:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(21, 16, 14, 'grupo', 2, 'D', '2026-06-18 12:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(22, 13, 15, 'grupo', 2, 'D', '2026-06-18 15:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(23, 16, 13, 'grupo', 3, 'D', '2026-06-22 18:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(24, 14, 15, 'grupo', 3, 'D', '2026-06-22 18:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(25, 17, 18, 'grupo', 1, 'E', '2026-06-15 12:00:00', 'Al Thumama', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(26, 19, 20, 'grupo', 1, 'E', '2026-06-15 15:00:00', 'Khalifa', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(27, 20, 18, 'grupo', 2, 'E', '2026-06-19 12:00:00', 'Ahmad Bin Ali', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(28, 17, 19, 'grupo', 2, 'E', '2026-06-19 15:00:00', 'Al Bayt', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(29, 20, 17, 'grupo', 3, 'E', '2026-06-23 18:00:00', 'Khalifa', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(30, 18, 19, 'grupo', 3, 'E', '2026-06-23 18:00:00', 'Al Bayt', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(31, 21, 22, 'grupo', 1, 'F', '2026-06-16 12:00:00', 'Ahmad Bin Ali', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(32, 23, 24, 'grupo', 1, 'F', '2026-06-16 15:00:00', 'Al Bayt', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(33, 24, 22, 'grupo', 2, 'F', '2026-06-20 12:00:00', 'Khalifa', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(34, 21, 23, 'grupo', 2, 'F', '2026-06-20 15:00:00', 'Al Thumama', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(35, 24, 21, 'grupo', 3, 'F', '2026-06-24 18:00:00', 'Ahmad Bin Ali', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(36, 22, 23, 'grupo', 3, 'F', '2026-06-24 18:00:00', 'Al Thumama', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(37, 25, 26, 'grupo', 1, 'G', '2026-06-17 12:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(38, 27, 28, 'grupo', 1, 'G', '2026-06-17 15:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(39, 28, 26, 'grupo', 2, 'G', '2026-06-21 12:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(40, 25, 27, 'grupo', 2, 'G', '2026-06-21 15:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(41, 28, 25, 'grupo', 3, 'G', '2026-06-25 18:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(42, 26, 27, 'grupo', 3, 'G', '2026-06-25 18:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(43, 29, 30, 'grupo', 1, 'H', '2026-06-18 12:00:00', 'Stadium 974', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(44, 31, 32, 'grupo', 1, 'H', '2026-06-18 15:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(45, 32, 30, 'grupo', 2, 'H', '2026-06-22 12:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(46, 29, 31, 'grupo', 2, 'H', '2026-06-22 15:00:00', 'Lusail', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(47, 32, 29, 'grupo', 3, 'H', '2026-06-26 18:00:00', 'Education City', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47'),
(48, 30, 31, 'grupo', 3, 'H', '2026-06-26 18:00:00', 'Al Janoub', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-24 17:09:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `predicciones`
--

CREATE TABLE `predicciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `goles_local_prediccion` int(11) NOT NULL,
  `goles_visitante_prediccion` int(11) NOT NULL,
  `penales_local_prediccion` int(11) DEFAULT NULL,
  `penales_visitante_prediccion` int(11) DEFAULT NULL,
  `puntos_ganados` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `predicciones`
--

INSERT INTO `predicciones` (`id`, `usuario_id`, `partido_id`, `goles_local_prediccion`, `goles_visitante_prediccion`, `penales_local_prediccion`, `penales_visitante_prediccion`, `puntos_ganados`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(2, 1, 2, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(3, 1, 7, 1, 1, NULL, NULL, 10, '2025-11-24 19:32:30', '2025-11-25 05:18:09'),
(4, 1, 13, 5, 0, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(5, 1, 14, 2, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(6, 1, 8, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(7, 1, 19, 3, 0, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(8, 1, 20, 2, 0, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(9, 1, 25, 1, 0, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(10, 1, 26, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(11, 1, 31, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(12, 1, 32, 0, 3, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(13, 1, 37, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(14, 1, 38, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(15, 1, 43, 1, 1, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(16, 1, 44, 2, 0, NULL, NULL, 0, '2025-11-24 19:32:30', '2025-11-24 19:32:30'),
(17, 4, 2, 5, 0, NULL, NULL, 5, '2025-11-25 03:32:37', '2025-11-25 05:18:03'),
(18, 4, 1, 10, 0, NULL, NULL, 0, '2025-11-25 03:32:37', '2025-11-25 03:32:37'),
(19, 4, 14, 3, 0, NULL, NULL, 0, '2025-11-25 03:32:37', '2025-11-25 03:32:37'),
(20, 4, 13, 0, 0, NULL, NULL, 0, '2025-11-25 03:32:37', '2025-11-25 03:32:37'),
(21, 4, 7, 0, 0, NULL, NULL, 5, '2025-11-25 03:32:37', '2025-11-25 05:18:09'),
(22, 4, 19, 1, 0, NULL, NULL, 0, '2025-11-25 03:32:37', '2025-11-25 03:32:37'),
(23, 4, 8, 0, 0, NULL, NULL, 0, '2025-11-25 03:32:37', '2025-11-25 03:32:37'),
(24, 4, 20, 0, 1, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(25, 4, 25, 0, 1, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(26, 4, 26, 5, 0, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(27, 4, 31, 0, 7, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(28, 4, 32, 2, 0, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(29, 4, 37, 0, 5, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(30, 4, 38, 0, 1, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(31, 4, 43, 0, 0, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(32, 4, 44, 7, 0, NULL, NULL, 0, '2025-11-25 03:32:38', '2025-11-25 03:32:38'),
(33, 6, 2, 2, 0, NULL, NULL, 5, '2025-11-25 05:13:08', '2025-11-25 05:18:03'),
(34, 6, 7, 1, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:13:08'),
(35, 6, 8, 4, 0, NULL, NULL, 10, '2025-11-25 05:13:08', '2025-11-25 05:20:53'),
(36, 6, 13, 0, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(37, 6, 14, 1, 3, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(38, 6, 19, 1, 1, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(39, 6, 20, 2, 3, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(40, 6, 25, 0, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(41, 6, 26, 5, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(42, 6, 31, 1, 1, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(43, 6, 32, 3, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(44, 6, 37, 2, 2, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(45, 6, 38, 0, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(46, 6, 43, 1, 1, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42'),
(47, 6, 44, 0, 0, NULL, NULL, 0, '2025-11-25 05:13:08', '2025-11-25 05:20:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntuaciones_torneo`
--

CREATE TABLE `puntuaciones_torneo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `puntos_totales` int(11) DEFAULT 0,
  `predicciones_exactas` int(11) DEFAULT 0,
  `predicciones_tendencia` int(11) DEFAULT 0,
  `predicciones_incorrectas` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puntuaciones_torneo`
--

INSERT INTO `puntuaciones_torneo` (`id`, `usuario_id`, `puntos_totales`, `predicciones_exactas`, `predicciones_tendencia`, `predicciones_incorrectas`, `updated_at`) VALUES
(9, 1, 10, 1, 0, 3, '2025-11-25 05:20:53'),
(10, 4, 10, 0, 2, 2, '2025-11-25 05:20:53'),
(11, 6, 15, 1, 1, 1, '2025-11-25 05:20:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `typing_status`
--

CREATE TABLE `typing_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `chat_type` enum('private','group') DEFAULT 'private',
  `is_typing` tinyint(1) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `typing_status`
--

INSERT INTO `typing_status` (`id`, `user_id`, `chat_id`, `chat_type`, `is_typing`, `last_updated`) VALUES
(1, 4, 1, 'private', 0, '2025-11-25 04:22:51'),
(7, 4, 2, 'private', 0, '2025-11-24 06:18:01'),
(13, 4, 3, 'group', 0, '2025-11-24 06:27:48'),
(26, 1, 5, 'private', 0, '2025-11-24 06:28:08'),
(33, 1, 4, 'private', 0, '2025-11-25 04:22:46'),
(126, 6, 6, 'group', 0, '2025-11-25 07:03:34'),
(135, 1, 6, 'group', 0, '2025-11-25 05:14:48'),
(144, 4, 6, 'group', 0, '2025-11-25 05:14:50'),
(172, 6, 1, 'private', 0, '2025-11-25 07:33:09'),
(224, 1, 6, 'private', 0, '2025-11-25 05:38:23'),
(351, 6, 2, 'group', 0, '2025-11-25 07:08:30'),
(369, 1, 2, 'group', 0, '2025-11-25 07:25:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido1` varchar(100) NOT NULL,
  `apellido2` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `cumpleanos` date DEFAULT NULL,
  `pais` varchar(5) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `equipo` varchar(5) DEFAULT NULL,
  `foto_perfil` varchar(100) DEFAULT 'default.jpg',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido1`, `apellido2`, `email`, `cumpleanos`, `pais`, `usuario`, `contrasena`, `equipo`, `foto_perfil`, `last_activity`, `is_online`) VALUES
(1, 'Jaziel Alan', 'Balderas', 'Escobedo', 'jaziel_balderas@hotmail.com', '2002-05-07', 'mx', 'eljazmen', '$2y$10$nCQz/PUFE4cNYRr.CQGByegQN1RGSpkqmpeJMSSnX9WFm8Tqe8SQi', 'mx', 'pfp_68f5781636e431.82650690.jpg', '2025-11-25 08:40:36', 1),
(2, 'Sofia Tanahiri', 'Guzman', 'De Leon', 'latan@hotmail.com', '2005-09-02', 'mx', 'LaNoviaDeSugawara', '$2y$10$g9FFZpq9PuIugMjB0.t3zeHbvR.6IH0/6r3yimJIgFDZ5kBAzuMHS', 'mx', 'pfp_68f578b73c77f4.27311343.png', '2025-11-25 04:11:23', 0),
(4, 'Alfonso', 'Rodriguez', 'Perez', 'asa@gmail.com', '2000-02-05', 'mx', 'Alfo123', '$2y$10$npxKBptRr7WAT84KTHyW0.bSfoc.Yc651bKICNm7zL.dbsiR.fcOm', 'mx', 'pfp_68f71a9cd82418.76112088.jpg', '2025-11-25 05:14:24', 1),
(5, 'Chris', 'Drip', 'King', 'dp@gmail.com', '2000-02-02', 'mx', 'doncris', '$2y$10$iaI6Wvo5skj2WmPp5HgYHu0Rel0muA0ZI68SK4TM05.GT8uVeqFkG', 'mx', 'pfp_68f82a8f934b81.27286482.jpeg', '2025-11-25 04:11:23', 0),
(6, 'Tanahiri', 'Guzman', 'De Leon', 'tan@gmail.com', '2005-09-02', 'mx', 'LaaaTaaan', '$2y$10$czgqS5iZwTCL12T5Lps3JOMHEbGIBq3PAzaVyiHeTuqMp737Jh4QK', 'mx', 'pfp_6925347adb9c85.04851353.png', '2025-11-25 08:04:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_medallas`
--

CREATE TABLE `usuario_medallas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `medalla_id` int(11) NOT NULL,
  `fecha_obtencion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_medallas`
--

INSERT INTO `usuario_medallas` (`id`, `usuario_id`, `medalla_id`, `fecha_obtencion`) VALUES
(1, 4, 1, '2025-11-25 04:03:05'),
(2, 4, 3, '2025-11-25 04:03:05'),
(4, 1, 1, '2025-11-25 04:03:05'),
(5, 1, 3, '2025-11-25 04:03:05'),
(7, 6, 1, '2025-11-25 04:59:00'),
(9, 4, 2, '2025-11-25 05:18:03'),
(14, 6, 2, '2025-11-25 05:18:03'),
(19, 1, 2, '2025-11-25 05:18:09'),
(23, 6, 3, '2025-11-25 05:18:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `friend_id` (`friend_id`),
  ADD KEY `idx_friends_user` (`user_id`);

--
-- Indices de la tabla `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_request` (`sender_id`,`receiver_id`),
  ADD KEY `idx_requests_receiver` (`receiver_id`,`status`),
  ADD KEY `idx_requests_sender` (`sender_id`,`status`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creador_id` (`creador_id`);

--
-- Indices de la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_member` (`grupo_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `medallas`
--
ALTER TABLE `medallas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender` (`sender_id`),
  ADD KEY `fk_receiver` (`receiver_id`);

--
-- Indices de la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_grupo_timestamp` (`grupo_id`,`timestamp`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_local_id` (`equipo_local_id`),
  ADD KEY `equipo_visitante_id` (`equipo_visitante_id`),
  ADD KEY `ganador_id` (`ganador_id`),
  ADD KEY `idx_fase` (`fase`),
  ADD KEY `idx_grupo` (`grupo`),
  ADD KEY `idx_fecha` (`fecha_partido`);

--
-- Indices de la tabla `predicciones`
--
ALTER TABLE `predicciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_prediccion` (`usuario_id`,`partido_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_partido` (`partido_id`);

--
-- Indices de la tabla `puntuaciones_torneo`
--
ALTER TABLE `puntuaciones_torneo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `typing_status`
--
ALTER TABLE `typing_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_typing` (`user_id`,`chat_id`,`chat_type`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `usuario_medallas`
--
ALTER TABLE `usuario_medallas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_medalla` (`usuario_id`,`medalla_id`),
  ADD KEY `medalla_id` (`medalla_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `medallas`
--
ALTER TABLE `medallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `predicciones`
--
ALTER TABLE `predicciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `puntuaciones_torneo`
--
ALTER TABLE `puntuaciones_torneo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `typing_status`
--
ALTER TABLE `typing_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario_medallas`
--
ALTER TABLE `usuario_medallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  ADD CONSTRAINT `grupo_miembros_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grupo_miembros_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  ADD CONSTRAINT `mensajes_grupo_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_grupo_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `partidos_ibfk_1` FOREIGN KEY (`equipo_local_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `partidos_ibfk_2` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`ganador_id`) REFERENCES `equipos` (`id`);

--
-- Filtros para la tabla `predicciones`
--
ALTER TABLE `predicciones`
  ADD CONSTRAINT `predicciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `predicciones_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntuaciones_torneo`
--
ALTER TABLE `puntuaciones_torneo`
  ADD CONSTRAINT `puntuaciones_torneo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `typing_status`
--
ALTER TABLE `typing_status`
  ADD CONSTRAINT `typing_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_medallas`
--
ALTER TABLE `usuario_medallas`
  ADD CONSTRAINT `usuario_medallas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_medallas_ibfk_2` FOREIGN KEY (`medalla_id`) REFERENCES `medallas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
