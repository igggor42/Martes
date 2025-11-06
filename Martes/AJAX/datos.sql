-- Script de inicialización para MySQL del Sistema de Gestión de Stock
-- Versión sin claves foráneas (FOREIGN KEY)

-- Asegurarse de que el motor por defecto sea InnoDB
SET default_storage_engine=InnoDB;

-- -----------------------------------------------------
-- Tabla: TipoDeMov
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS TipoDeMov (
  IdMov INT NOT NULL,
  Codigo VARCHAR(15) NOT NULL,
  Descripcion VARCHAR(60) NOT NULL,
  PRIMARY KEY (IdMov)
) ENGINE=InnoDB;

-- Insertar datos de ejemplo en TipoDeMov
INSERT INTO TipoDeMov (IdMov, Codigo, Descripcion) VALUES 
(1, 'ENT', 'Entrada de stock'),
(2, 'SAL', 'Salida de stock'),
(3, 'REUBICACION', 'Reubicación'),
(4, 'AJUSTE_INV', 'Ajuste de inventario')
ON DUPLICATE KEY UPDATE 
  Codigo = VALUES(Codigo),
  Descripcion = VALUES(Descripcion);

-- -----------------------------------------------------
-- Tabla: Login (NUEVA)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Login (
  iduser INT NOT NULL AUTO_INCREMENT,
  usuario VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  contador_sesiones INT DEFAULT 0,
  PRIMARY KEY (iduser),
  UNIQUE INDEX `usuario_UNIQUE` (`usuario` ASC)
) ENGINE=InnoDB;

-- Insertar un usuario de ejemplo
INSERT INTO Login (usuario, password) VALUES ('admin', 'admin123')
ON DUPLICATE KEY UPDATE usuario = usuario; -- No hacer nada si ya existe

-- -----------------------------------------------------
-- Tabla: MovimientosDeStock
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS MovimientosDeStock (
  IdMov INT NOT NULL,
  CodArticulo VARCHAR(10) NOT NULL,
  Descripcion VARCHAR(60) NOT NULL,
  NroDeLote VARCHAR(10) NOT NULL,
  FechaMovimiento DATE NOT NULL,
  UnidadMedida VARCHAR(5) NOT NULL,
  Cantidad INT NOT NULL,
  FotoArticulo VARCHAR(60) NULL,
  PRIMARY KEY (CodArticulo, NroDeLote, IdMov)
) ENGINE=InnoDB;

-- Insertar datos de ejemplo en MovimientosDeStock
INSERT INTO MovimientosDeStock (IdMov, CodArticulo, Descripcion, NroDeLote, FechaMovimiento, UnidadMedida, Cantidad, FotoArticulo) VALUES 
(1, '001', 'Caja tuercas 10mm', 'L-01', '2025-09-12', 'CAJ', 30, 'caja_tuercas.jpg'),
(2, '002', 'Cinta adhesiva', 'L-02', '2025-02-18', 'U', 25, 'cinta_adhesiva.jpg'),
(3, '003', 'Baterias', 'L-03', '2025-03-13', 'CAJ', 60, 'baterias.jpg'),
(4, '004', 'Alambres', 'L-04', '2025-06-05', 'M', 15, 'alambre.jpg')
ON DUPLICATE KEY UPDATE 
  Descripcion = VALUES(Descripcion),
  FechaMovimiento = VALUES(FechaMovimiento),
  UnidadMedida = VALUES(UnidadMedida),
  Cantidad = VALUES(Cantidad),
  FotoArticulo = VALUES(FotoArticulo);