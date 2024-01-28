-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-06-2023 a las 02:15:32
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sis_venta`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (IN `n_cantidad` INT, IN `n_precio` DECIMAL(10,2), IN `codigo` INT)   BEGIN
DECLARE nueva_existencia int;
DECLARE nuevo_total decimal(10,2);
DECLARE nuevo_precio decimal(10,2);

DECLARE cant_actual int;
DECLARE pre_actual decimal(10,2);

DECLARE actual_existencia int;
DECLARE actual_precio decimal(10,2);

SELECT precio, existencia INTO actual_precio, actual_existencia FROM producto WHERE codproducto = codigo;

SET nueva_existencia = actual_existencia + n_cantidad;
SET nuevo_total = n_precio;
SET nuevo_precio = nuevo_total;

UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE codproducto = codigo;

SELECT nueva_existencia, nuevo_precio;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))   BEGIN
DECLARE precio_actual decimal(10,2);
SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;
INSERT INTO detalle_temp(token_user, codproducto, cantidad, precio_venta) VALUES (token_user, codigo, cantidad, precio_actual);
SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER JOIN producto p ON tmp.codproducto = p.codproducto WHERE tmp.token_user = token_user;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `data` ()   BEGIN
DECLARE usuarios int;
DECLARE clientes int;
DECLARE proveedores int;
DECLARE productos int;
DECLARE ventas int;
SELECT COUNT(*) INTO usuarios FROM usuario;
SELECT COUNT(*) INTO clientes FROM cliente;
SELECT COUNT(*) INTO proveedores FROM proveedor;
SELECT COUNT(*) INTO productos FROM producto;
SELECT COUNT(*) INTO ventas FROM factura WHERE fecha > CURDATE();

SELECT usuarios, clientes, proveedores, productos, ventas;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))   BEGIN
DELETE FROM detalle_temp WHERE correlativo = id_detalle;
SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER JOIN producto p ON tmp.codproducto = p.codproducto WHERE tmp.token_user = token;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50))   BEGIN
DECLARE factura INT;
DECLARE registros INT;
DECLARE total DECIMAL(10,2);
DECLARE nueva_existencia int;
DECLARE existencia_actual int;

DECLARE tmp_cod_producto int;
DECLARE tmp_cant_producto int;
DECLARE a int;
SET a = 1;

CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cod_prod BIGINT,
    cant_prod int);
SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
IF registros > 0 THEN
INSERT INTO tbl_tmp_tokenuser(cod_prod, cant_prod) SELECT codproducto, cantidad FROM detalle_temp WHERE token_user = token;
INSERT INTO factura (usuario,codcliente) VALUES (cod_usuario, cod_cliente);
SET factura = LAST_INSERT_ID();

INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) AS nofactura, codproducto, cantidad,precio_venta FROM detalle_temp WHERE token_user = token;
WHILE a <= registros DO
	SELECT cod_prod, cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
    SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
    SET nueva_existencia = existencia_actual - tmp_cant_producto;
    UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
    SET a=a+1;
END WHILE;
SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
UPDATE factura SET totalfactura = total WHERE nofactura = factura;
DELETE FROM detalle_temp WHERE token_user = token;
TRUNCATE TABLE tbl_tmp_tokenuser;
SELECT * FROM factura WHERE nofactura = factura;
ELSE
SELECT 0;
END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL,
  `categoria_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`categoria_id`, `categoria_nombre`) VALUES
(1, 'Papeleria'),
(2, 'Electronica'),
(3, 'Arte y Diseño'),
(4, 'Artes Graficas'),
(5, 'Papel'),
(6, 'Material Didactico'),
(7, 'Decoracion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `dni` int(8) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(25) COLLATE utf8_spanish_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `calle` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `municipio` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `CP` int(12) NOT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `apellido_paterno` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `dni`, `nombre`, `telefono`, `usuario_id`, `calle`, `municipio`, `estado`, `CP`, `fecha_modificacion`, `apellido_paterno`, `apellido_materno`) VALUES
(1, 1, 'PUBLICO', '1', 1, 'Avenida 2', 'San Nicolas ', 'Nuevo Leon', 4555, '2023-06-01 00:59:11', 'GENERAL', 'GENERAL'),
(2, 3, 'VALERIA ', '8110149397', 10, 'Palma Verde', 'Guadalupe', 'Nuevo Leon', 67193, '2023-06-01 01:03:20', 'PALOMARES ', 'VILLALON'),
(3, 2, 'ANTONIO ', '81101456723', 10, 'VARIEL AVE', 'SAN PEDRO', 'NUEVO LEON', 8330, '2023-06-01 01:13:43', 'MUÑOZ ', 'FRAIRE '),
(4, 4, 'JUAN DAVID ', '8110532411', 10, 'Olivos', 'San Nicolas', 'Nuevo Leon', 4567, '2023-06-01 01:14:08', 'RESENDIZ', 'GARCIA'),
(5, 5, 'ALBERTO YARACG ', '8112786442', 23, 'Girasoles', 'Guadalupe', 'Nuevo Leon', 67192, '2023-06-01 01:14:40', 'RANGEL ', 'OCHOA'),
(25, 6, 'DORLAN JAIR ', '8134562312', 23, 'Hortensia', 'San Nicolas', 'Nuevo Leon', 6234, '2023-06-01 01:15:00', 'VILLALOBOS', 'GARCIA'),
(26, 7, 'GILBERTO', '8110678945', 28, 'Morelos', 'Monterrey', 'Nuevo Leon', 6719, '2023-06-01 01:15:34', 'VALADEZ', 'VALADEZ');

--
-- Disparadores `cliente`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_cliente` AFTER UPDATE ON `cliente` FOR EACH ROW BEGIN
  IF NEW.dni <> OLD.dni THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'dni', OLD.dni, NEW.dni);
  END IF;

  IF NEW.nombre <> OLD.nombre THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'nombre', OLD.nombre, NEW.nombre);
  END IF;

  IF NEW.telefono <> OLD.telefono THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'telefono', OLD.telefono, NEW.telefono);
  END IF;

  IF NEW.calle <> OLD.calle THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'calle', OLD.calle, NEW.calle);
  END IF;

  IF NEW.municipio <> OLD.municipio THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'municipio', OLD.municipio, NEW.municipio);
  END IF;

  IF NEW.estado <> OLD.estado THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'estado', OLD.estado, NEW.estado);
  END IF;

  IF NEW.cp <> OLD.cp THEN
    INSERT INTO registro_cambios (cliente_id, columna_modificada, valor_anterior, valor_nuevo)
    VALUES (NEW.idcliente, 'cp', OLD.cp, NEW.cp);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `dni` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `razon_social` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(11) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` text COLLATE utf8_spanish_ci NOT NULL,
  `igv` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `dni`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `igv`) VALUES
(1, 1, 'Paper Ingeneria ', 'Paper Ingeneria ', '8114672419', 'Paper_ingenieria@gmail.com', 'Monterrey, Nuevo Leon, Mexico', '1.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(20) NOT NULL,
  `nofactura` bigint(20) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(1, 1, 1, 2, '1560.00'),
(2, 2, 1, 3, '1560.00'),
(3, 3, 2, 1, '2500.00'),
(4, 4, 1, 1, '1560.00'),
(5, 5, 2, 10, '2500.00'),
(6, 6, 2, 1, '2500.00'),
(7, 7, 1, 1, '1560.00'),
(8, 8, 1, 1, '1560.00'),
(9, 9, 33, 1, '123.00'),
(10, 10, 1, 1, '1560.00'),
(11, 11, 48, 5, '49.00'),
(12, 12, 49, 1, '25.00'),
(13, 13, 48, 1, '49.00'),
(14, 14, 48, 1, '49.00'),
(15, 15, 48, 1, '49.00'),
(16, 16, 49, 1, '25.00'),
(17, 17, 49, 3, '25.00'),
(18, 18, 49, 1, '25.00'),
(19, 19, 48, 1, '49.00'),
(20, 20, 49, 1, '25.00'),
(21, 21, 48, 1, '49.00'),
(22, 22, 48, 1, '49.00'),
(23, 23, 48, 1, '49.00'),
(24, 24, 50, 1, '15.00'),
(25, 25, 50, 1, '15.00'),
(26, 26, 50, 1, '15.00'),
(27, 27, 50, 1, '15.00'),
(28, 28, 50, 1, '15.00'),
(29, 29, 50, 1, '15.00'),
(30, 30, 50, 1, '15.00'),
(31, 31, 50, 1, '15.00'),
(32, 32, 50, 1, '15.00'),
(33, 33, 50, 1, '15.00'),
(34, 34, 50, 1, '15.00'),
(35, 35, 53, 1, '32.00'),
(36, 36, 53, 1, '32.00'),
(37, 37, 53, 1, '32.00'),
(38, 38, 50, 1, '15.00'),
(39, 39, 48, 1, '49.00'),
(40, 40, 50, 1, '15.00'),
(41, 41, 50, 1, '15.00'),
(42, 42, 53, 3, '32.00'),
(43, 43, 50, 1, '15.00'),
(44, 44, 53, 1, '32.00'),
(45, 45, 53, 1, '32.00'),
(46, 46, 53, 1, '32.00'),
(47, 47, 50, 1, '15.00'),
(48, 48, 53, 1, '32.00'),
(49, 49, 50, 1, '15.00'),
(50, 50, 53, 1, '32.00'),
(51, 51, 53, 1, '32.00'),
(52, 52, 53, 1, '32.00'),
(53, 53, 53, 1, '32.00'),
(54, 54, 53, 1, '32.00'),
(55, 55, 53, 1, '32.00'),
(56, 56, 53, 1, '32.00'),
(57, 57, 53, 1, '32.00'),
(58, 58, 48, 1, '49.00'),
(59, 59, 49, 1, '25.00'),
(60, 60, 49, 1, '25.00'),
(61, 61, 48, 1, '49.00'),
(62, 62, 48, 1, '49.00'),
(63, 63, 53, 1, '32.00'),
(64, 64, 53, 1, '32.00'),
(65, 65, 53, 1, '32.00'),
(66, 66, 53, 1, '32.00'),
(67, 67, 53, 1, '32.00'),
(68, 68, 53, 1, '32.00'),
(69, 69, 49, 1, '25.00'),
(70, 70, 49, 1, '25.00'),
(71, 71, 49, 1, '25.00'),
(72, 72, 53, 1, '32.00'),
(73, 73, 49, 1, '25.00'),
(74, 74, 49, 1, '25.00'),
(75, 75, 53, 1, '32.00'),
(76, 76, 49, 1, '25.00'),
(77, 77, 49, 1, '25.00'),
(78, 78, 50, 1, '15.00'),
(79, 79, 49, 1, '25.00'),
(80, 80, 49, 1, '25.00'),
(81, 81, 50, 1, '15.00'),
(82, 81, 48, 1, '49.00'),
(83, 81, 49, 3, '25.00'),
(84, 82, 50, 2, '15.00'),
(85, 83, 48, 5, '49.00'),
(86, 83, 50, 1, '15.00'),
(88, 84, 50, 1, '15.00'),
(89, 85, 50, 1, '15.00'),
(90, 86, 48, 4, '49.00'),
(91, 87, 48, 1, '49.00'),
(92, 88, 50, 1, '15.00'),
(93, 89, 49, 1, '25.00'),
(94, 90, 48, 1, '49.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id_dir` tinyint(3) NOT NULL,
  `calle` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `municipio` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '2023-05-15 19:08:59', 12, '1560.00', 10),
(2, 48, '2023-05-30 14:50:20', 12, '49.00', 10),
(3, 48, '2023-05-30 14:50:33', 12, '49.00', 10),
(4, 49, '2023-05-30 14:50:46', 12, '25.00', 10),
(5, 50, '2023-05-30 14:50:55', 12, '15.00', 10),
(6, 53, '2023-05-30 14:51:02', 12, '32.00', 10),
(7, 53, '2023-05-31 11:55:14', 27, '32.00', 23),
(8, 49, '2023-05-31 11:55:25', 12, '25.00', 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `codcliente` int(11) NOT NULL,
  `totalfactura` decimal(10,2) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `num_producto` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estado`, `num_producto`, `codproducto`) VALUES
(82, '2023-05-31 16:54:04', 10, 1, '30.00', 1, 0, 0),
(83, '2023-05-31 16:54:45', 10, 3, '260.00', 1, 0, 0),
(84, '2023-05-31 16:55:32', 10, 2, '15.00', 1, 0, 0),
(85, '2023-05-31 16:56:20', 10, 4, '15.00', 1, 0, 0),
(86, '2023-05-31 16:56:45', 10, 5, '196.00', 1, 0, 0),
(87, '2023-05-31 16:58:17', 10, 25, '49.00', 1, 0, 0),
(88, '2023-05-31 19:13:04', 10, 1, '15.00', 1, 0, 0),
(89, '2023-06-01 17:59:13', 10, 26, '25.00', 1, 0, 0),
(90, '2023-06-01 18:11:52', 10, 1, '49.00', 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `categoria_id` int(7) NOT NULL,
  `cod_inventario` tinyint(4) NOT NULL,
  `precio_compra` tinyint(4) NOT NULL,
  `existencia` tinyint(4) NOT NULL,
  `proveedor` varchar(60) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `proveedor` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `existencia` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `id_inventario` int(11) NOT NULL,
  `categoria` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `usuario_id`, `id_inventario`, `categoria`) VALUES
(48, 'BOLIGRAFO P/MED. 1.0MM DURA+', 10, '49.00', 6, 9, 1, 'Papeleria'),
(49, 'CORRECTOR PLUMA BIC SHAKE', 12, '25.00', 7, 9, 1, 'Papeleria'),
(50, 'BOLIGRAFO P/MED. 1.0MM ', 12, '15.00', -3, 9, 1, 'Papeleria'),
(53, 'MARCADOR BEROL AMARILLO 2 Pz', 12, '32.00', 18, 9, 1, 'Papeleria'),
(54, 'STABILO VERDE ++ 1PZ', 19, '50.00', 10, 28, 2, 'Arte y Diseño');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `contacto` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(11) COLLATE utf8_spanish_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `calle` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `municipio` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `CP` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `usuario_id`, `calle`, `municipio`, `estado`, `CP`) VALUES
(10, 'BIC', 'aaron.barrera@bicworld.com', '5545335809', 9, 'Mexico', 'Naucalpan', 'CDMX', 6790),
(11, 'Norma Mexico', 'contacto.mx@carvajal.com', '5641588779', 9, 'Avenida 3', 'Tultitlan de Mariano Escobedo', 'CDMX', 54900),
(12, 'Berol', 'berolventas@psi.net.mx', '5557293433', 9, 'Vía Dr. Gustavo Baz ', 'Tlalnepantla de Baz', 'CDMX', 54060),
(13, 'Pelikan', 'escolares@pelikan.com.mx', '1222309800', 9, 'Carretera a Tehuacán 1033 Col. Maravillas ', 'Puebla', 'Puebla', 72220),
(14, 'Sharpie', 'gerencia@grupoleomond.com', '5555160186', 9, 'Irapuato 35-2, Col. Hip. Condesa', 'Ciudad de México', 'Ciudad de México', 6170),
(15, 'Faber Castell', 'Info@fabercastell.com.mx   ', '4422517097', 9, 'Carretera Estatal 431, KM 1.3, Bod. 1 El Colorado ', 'EL MARQUÉS', 'QUERETARO ', 76246),
(16, 'PaperMate Mexico', 'Nueva dirección de Newell Brands de México', '5557293450', 9, 'Blvd. Manuel Ávila Camacho No. 32 Piso 14,', 'Naucalpan', 'CDMX', 1256),
(17, 'Post IT ', 'postit.mx@post.com', '1800120363', 9, 'Sin Calle', 'Sin Municipio', 'Sin Estado', 4534),
(18, 'CRAYOLA', 'Binney & Smith (México), S.A. de C.V.', '15004300', 9, 'Calz. de la Venta #26, Fracc.Industrial Cuamatla,', 'Cuautitlán Izcallia', 'Edo de México', 56789),
(19, 'STABILO OTHELO', 'stabilo.oth@boss.com', '5589456321', 10, 'PLAZA COMERCIAL SERENA', 'MONTERREY', 'NUEVO LEON', 64989),
(20, 'MAPITA', 'mapita@gmail.com', '8110134783', 28, 'Mexico', 'Guadalupe', 'Nuevo Leon', 4567);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_cambios`
--

CREATE TABLE `registro_cambios` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `columna_modificada` varchar(50) DEFAULT NULL,
  `valor_anterior` varchar(255) DEFAULT NULL,
  `valor_nuevo` varchar(255) DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `registro_cambios`
--

INSERT INTO `registro_cambios` (`id`, `cliente_id`, `columna_modificada`, `valor_anterior`, `valor_nuevo`, `fecha_modificacion`) VALUES
(1, 1, 'cp', '4556', '4555', '2023-05-31 20:03:27'),
(2, 2, 'cp', '67190', '67193', '2023-05-31 20:03:41'),
(3, 1, 'nombre', 'PUBLICO GENERAL', 'GENERAL', '2023-06-01 00:45:46'),
(4, 1, 'cp', '4555', '0', '2023-06-01 00:58:17'),
(5, 1, 'cp', '0', '4555', '2023-06-01 00:58:46'),
(6, 1, 'nombre', 'GENERAL', 'PUBLICO', '2023-06-01 00:59:11'),
(7, 2, 'nombre', 'VALERIA PALOMARES VILLALON', 'VALERIA PALOMARES ', '2023-06-01 01:02:44'),
(8, 2, 'nombre', 'VALERIA PALOMARES ', 'VALERIA ', '2023-06-01 01:03:20'),
(9, 3, 'nombre', 'ANTONIO MUÑOZ FRAIRE ', 'ANTONIO ', '2023-06-01 01:13:43'),
(10, 3, 'telefono', '8110567834', '81101456723', '2023-06-01 01:13:43'),
(11, 4, 'nombre', 'JUAN DAVID RESENDIZ', 'JUAN DAVID ', '2023-06-01 01:14:08'),
(12, 4, 'telefono', '811257890', '8110532411', '2023-06-01 01:14:08'),
(13, 5, 'nombre', 'ALBERTO YARACG RANGEL OCHOA', 'ALBERTO YARACG ', '2023-06-01 01:14:40'),
(14, 5, 'telefono', '8110453523', '8112786442', '2023-06-01 01:14:40'),
(15, 25, 'telefono', '8134567832', '8134562312', '2023-06-01 01:15:00'),
(16, 26, 'telefono', '8112567834', '8110678945', '2023-06-01 01:15:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `rol` int(11) NOT NULL,
  `apellido_m_usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `apellido_p_usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `id_dir` tinyint(3) NOT NULL,
  `telefono` int(11) NOT NULL,
  `apellido_paterno` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `apellido_m_usuario`, `apellido_p_usuario`, `id_dir`, `telefono`, `apellido_paterno`, `apellido_materno`) VALUES
(1, 'ANTONIO MUÑOZ ', 'tony@gmail.com', 'A_001', '21232f297a57a5a743894a0e4a801fc3', 1, '', '', 0, 0, 'MUÑOZ ', 'FRAIRE'),
(10, 'VALERIA ', 'palomasv78@gmail.com', 'A_002', '81dc9bdb52d04dc20036dbd8313ed055', 1, '', '', 0, 0, 'PALOMARES ', 'VILLALON'),
(24, 'ALEJANDRO ', 'olivas@gmail.com', 'A_003', '81dc9bdb52d04dc20036dbd8313ed055', 1, '', '', 0, 0, 'OLIVA ', 'SANCHEZ'),
(26, 'CECILIA ', 'cecilia@gmail.com', 'V_001', '81dc9bdb52d04dc20036dbd8313ed055', 2, '', '', 0, 0, 'HUERTA ', 'MINOR'),
(27, 'OLIVIA ', 'olivia@gmail.com', 'V_002', '81dc9bdb52d04dc20036dbd8313ed055', 2, '', '', 0, 0, 'VILLALON ', 'ORDOÑEZ'),
(28, 'DORLAN JAIR ', 'dorlan@gmail.com', 'V_003', '81dc9bdb52d04dc20036dbd8313ed055', 2, '', '', 0, 0, 'VILLALOBOS ', 'GARCIA'),
(29, 'ENRIQUE', 'enrique@gmail.com', 'A_007', '81dc9bdb52d04dc20036dbd8313ed055', 1, '', '', 0, 0, 'LOPEZ', 'LOPEZ');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes` (
`dni` int(8)
,`nombre` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_total`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_total` (
`idcliente` int(11)
,`nombre` varchar(100)
,`apellido_paterno` varchar(50)
,`apellido_materno` varchar(50)
,`veces_compradas` bigint(21)
,`ultima_compra` datetime
,`primera_compra` datetime
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos` (
`descripcion` varchar(200)
,`precio` decimal(10,2)
,`existencia` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_por_categoria`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_por_categoria` (
`producto` varchar(200)
,`categoria` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes`
--
DROP TABLE IF EXISTS `vista_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes`  AS SELECT `cliente`.`dni` AS `dni`, `cliente`.`nombre` AS `nombre` FROM `cliente` WHERE `cliente`.`idcliente` in (select distinct `factura`.`codcliente` from `factura`)  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_total`
--
DROP TABLE IF EXISTS `vista_clientes_total`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_total`  AS SELECT `c`.`idcliente` AS `idcliente`, `c`.`nombre` AS `nombre`, `c`.`apellido_paterno` AS `apellido_paterno`, `c`.`apellido_materno` AS `apellido_materno`, count(`f`.`nofactura`) AS `veces_compradas`, max(`f`.`fecha`) AS `ultima_compra`, min(`f`.`fecha`) AS `primera_compra` FROM (`cliente` `c` join `factura` `f` on(`c`.`idcliente` = `f`.`codcliente`)) GROUP BY `c`.`idcliente`, `c`.`nombre`, `c`.`apellido_paterno`, `c`.`apellido_materno``apellido_materno`  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos`
--
DROP TABLE IF EXISTS `vista_productos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos`  AS SELECT `producto`.`descripcion` AS `descripcion`, `producto`.`precio` AS `precio`, `producto`.`existencia` AS `existencia` FROM `producto` WHERE `producto`.`precio` > 1010  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_por_categoria`
--
DROP TABLE IF EXISTS `vista_productos_por_categoria`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_por_categoria`  AS SELECT `p`.`descripcion` AS `producto`, `c`.`categoria_nombre` AS `categoria` FROM (`producto` `p` join `categoria` `c` on(`p`.`categoria` = `c`.`categoria_nombre`))  ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`categoria_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id_dir`),
  ADD UNIQUE KEY `id_dir` (`id_dir`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `cod_producto` (`codproducto`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `id_inventario` (`id_inventario`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`);

--
-- Indices de la tabla `registro_cambios`
--
ALTER TABLE `registro_cambios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `id_dir` (`id_dir`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `categoria_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id_dir` tinyint(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `registro_cambios`
--
ALTER TABLE `registro_cambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `id_inventario` FOREIGN KEY (`id_inventario`) REFERENCES `producto` (`id_inventario`),
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
