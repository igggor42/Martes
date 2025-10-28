-- (TablaHijo.json)
CREATE TABLE `TipoDeMov` (
  `IdMov` INT NOT NULL,
  `Codigo` VARCHAR(15) NOT NULL,
  `Descripcion` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`IdMov`)
);

-- Registros en TipoDeMov
INSERT INTO `TipoDeMov` (`IdMov`, `Codigo`, `Descripcion`) VALUES 
(1, 'ENT', 'Entrada de stock'),
(2, 'SAL', 'Salida de stock'),
(3, 'REUBICACION', 'Reubicación'),
(4, 'AJUSTE_INV', 'Ajuste de inventario');

-- (TablaPadre.json)
CREATE TABLE `MovimientosDeStock` (
  `IdMov` INT NOT NULL,
  `CodArticulo` VARCHAR(10) NOT NULL,
  `Descripcion` VARCHAR(60) NOT NULL,
  `NroDeLote` VARCHAR(10) NOT NULL,
  `FechaMovimiento` DATE NOT NULL,
  `UnidadMedida` VARCHAR(5) NOT NULL,
  `Cantidad` INT NOT NULL,
  `FotoArticulo` VARCHAR(60),
  PRIMARY KEY (`CodArticulo`, `NroDeLote`, `IdMov`), 
  
  -- (FOREIGN KEY)
  FOREIGN KEY (`IdMov`) REFERENCES `TipoDeMov`(`IdMov`)
);

-- MovimientosDeStock
INSERT INTO `MovimientosDeStock` (`IdMov`, `CodArticulo`, `Descripcion`, `NroDeLote`, `FechaMovimiento`, `UnidadMedida`, `Cantidad`, `FotoArticulo`) VALUES 
(1, '001', 'Caja tuercas 10mm', 'L-01', '2025-09-12', 'CAJ', 30, 'caja_tuercas.jpg'),
(2, '002', 'Cinta adhesiva', 'L-02', '2025-02-18', 'U', 25, 'cinta_adhesiva.jpg'),
(3, '003', 'Baterias', 'L-03', '2025-03-13', 'CAJ', 60, 'baterias.jpg'),
(4, '004', 'Alambres', 'L-04', '2025-06-05', 'M', 15, 'alambre.jpg');