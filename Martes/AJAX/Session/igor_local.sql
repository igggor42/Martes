-- Script de inicialización para MySQL del Sistema de Gestión de Stock
-- Versión combinada con sistema de login y soporte BLOB

-- Crear la base de datos y configurar el charset
CREATE DATABASE IF NOT EXISTS `igor_stock` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `igor_stock`;

-- Asegurarse de que el motor por defecto sea InnoDB
SET default_storage_engine=InnoDB;

-- -----------------------------------------------------
-- Tabla: usuarios (sistema de login mejorado)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(50) NOT NULL UNIQUE,
  `apellido` VARCHAR(100) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `contador_sesiones` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert de ejemplo con password hasheado con SHA256
-- Password: admin123 -> hash SHA256: 240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9
INSERT INTO `usuarios` (`login`,`apellido`,`nombres`,`password`,`contador_sesiones`) VALUES
('admin','Admin','Administrador','240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9',0);

-- -----------------------------------------------------
-- Tabla: TipoDeMov
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `TipoDeMov` (
  `IdMov` INT NOT NULL,
  `Codigo` VARCHAR(15) NOT NULL,
  `Descripcion` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`IdMov`),
  UNIQUE KEY `uq_tipomov_codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de ejemplo en TipoDeMov
INSERT INTO `TipoDeMov` (`IdMov`, `Codigo`, `Descripcion`) VALUES 
(1, 'ENT', 'Entrada de stock'),
(2, 'SAL', 'Salida de stock'),
(3, 'REUBICACION', 'Reubicación'),
(4, 'AJUSTE_INV', 'Ajuste de inventario')
ON DUPLICATE KEY UPDATE 
  `Codigo` = VALUES(`Codigo`),
  `Descripcion` = VALUES(`Descripcion`);

-- -----------------------------------------------------
-- Tabla: MovimientosDeStock (con soporte BLOB)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `MovimientosDeStock` (
  `CodArticulo` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NOT NULL,
  `NroDeLote` VARCHAR(50) NULL,
  `fecha_Movimiento` DATE NOT NULL,
  `TipodeMov` VARCHAR(15) NOT NULL,
  `Unidad_medida` VARCHAR(10) NULL,
  `Cantidad` INT NOT NULL,
  `FotoArticulo` LONGBLOB NULL,
  `FotoMime` VARCHAR(100) NULL,
  PRIMARY KEY (`CodArticulo`),
  INDEX (`TipodeMov`),
  CONSTRAINT `fk_movimiento_tipo_codigo` FOREIGN KEY (`TipodeMov`) REFERENCES `TipoDeMov`(`Codigo`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de ejemplo en MovimientosDeStock (sin fotos iniciales, CodArticulo autogenerado)
INSERT INTO `MovimientosDeStock` (`Descripcion`, `NroDeLote`, `fecha_Movimiento`, `TipodeMov`, `Unidad_medida`, `Cantidad`, `FotoArticulo`, `FotoMime`) VALUES 
('Caja tuercas 10mm', 'L-01', '2025-09-12', 'ENT', 'CAJ', 30, NULL, NULL),
('Cinta adhesiva', 'L-02', '2025-02-18', 'SAL', 'U', 25, NULL, NULL),
('Baterias', 'L-03', '2025-03-13', 'REUBICACION', 'CAJ', 60, NULL, NULL),
('Alambres', 'L-04', '2025-06-05', 'AJUSTE_INV', 'M', 15, NULL, NULL);