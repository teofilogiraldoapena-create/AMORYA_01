CREATE DATABASE IF NOT EXISTS amorya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE amorya;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT UNSIGNED NULL,
  nombre VARCHAR(180) NOT NULL,
  descripcion TEXT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  imagen VARCHAR(255) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
  INDEX idx_producto_nombre (nombre),
  INDEX idx_producto_activo (activo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(32) NOT NULL UNIQUE,
  cliente_nombre VARCHAR(150) NOT NULL,
  cliente_email VARCHAR(190) NULL,
  cliente_telefono VARCHAR(40) NOT NULL,
  tipo_entrega ENUM('delivery','recojo') NOT NULL,
  direccion TEXT NULL,
  metodo_pago ENUM('yape','plin','transferencia','efectivo','tarjeta') NOT NULL,
  notas TEXT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  costo_delivery DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente','confirmado','preparando','en_camino','listo_recojo','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  pago_confirmado TINYINT(1) NOT NULL DEFAULT 0,
  stock_descontado TINYINT(1) NOT NULL DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pedido_estado (estado),
  INDEX idx_pedido_fecha (creado_en)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedido_detalles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id BIGINT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NULL,
  nombre_producto VARCHAR(180) NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  cantidad INT UNSIGNED NOT NULL,
  total_linea DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ventas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id BIGINT UNSIGNED NOT NULL UNIQUE,
  subtotal DECIMAL(10,2) NOT NULL,
  costo_delivery DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  metodo_pago VARCHAR(30) NOT NULL,
  registrado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_venta_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
) ENGINE=InnoDB;

INSERT IGNORE INTO categorias (nombre) VALUES ('General'), ('Novedades'), ('Ofertas');
