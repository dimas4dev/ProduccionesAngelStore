-- Script SQL para crear la base de datos y tablas de "Producciones Angel"
-- Ejecutar este script en phpMyAdmin o en la línea de comandos de MySQL

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS produccionesAngel;
USE produccionesAngel;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    direccion VARCHAR(200),
    telefono VARCHAR(20),
    role ENUM('cliente', 'administrador') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT(11) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de detalles de venta
CREATE TABLE IF NOT EXISTS detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT(11) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES venta(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES producto(id) ON DELETE CASCADE
);

-- Tabla de mensajes de contacto
CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100),
    direccion VARCHAR(200),
    telefono VARCHAR(20),
    mensaje VARCHAR(500),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo para productos
INSERT INTO producto (nombre, descripcion, imagen, precio, stock) VALUES
('Laptop Gaming HP Pavilion', 'Laptop de alto rendimiento para gaming y trabajo profesional. Procesador Intel Core i7, 16GB RAM, 512GB SSD, tarjeta gráfica dedicada.', NULL, 899.99, 15),
('Smartphone Samsung Galaxy S24', 'Smartphone premium con cámara de 108MP, pantalla AMOLED de 6.2 pulgadas, 256GB de almacenamiento y batería de larga duración.', NULL, 749.99, 25),
('Auriculares Sony WH-1000XM4', 'Auriculares inalámbricos con cancelación de ruido líder en la industria. Sonido de alta calidad y hasta 30 horas de batería.', NULL, 349.99, 30),
('Tablet iPad Air 5ta Gen', 'Tablet versátil con chip M1, pantalla Liquid Retina de 10.9 pulgadas, ideal para trabajo, estudio y entretenimiento.', NULL, 599.99, 20),
('Monitor Gaming ASUS 27"', 'Monitor gaming de 27 pulgadas con resolución 4K, 144Hz de frecuencia de actualización y tecnología HDR.', NULL, 449.99, 12),
('Teclado Mecánico Razer', 'Teclado gaming mecánico con switches Razer Green, retroiluminación RGB y construcción duradera para gaming profesional.', NULL, 149.99, 40),
('Mouse Logitech MX Master 3', 'Mouse inalámbrico premium con sensor de alta precisión, batería de 70 días y diseño ergonómico para productividad.', NULL, 99.99, 35),
('Cámara Canon EOS R6', 'Cámara mirrorless profesional con sensor full frame de 20MP, grabación 4K y estabilización de imagen integrada.', NULL, 2499.99, 8),
('Impresora 3D Creality Ender 3', 'Impresora 3D FDM de escritorio, ideal para principiantes y makers. Área de impresión 220x220x250mm.', NULL, 199.99, 18),
('Disco Duro Externo Seagate 2TB', 'Disco duro externo portátil de 2TB, USB 3.0, perfecto para respaldos y almacenamiento adicional.', NULL, 79.99, 50);

-- Insertar usuario administrador de ejemplo
-- Contraseña: admin123
INSERT INTO usuarios (nombre, email, clave, direccion, telefono, role) VALUES
('Administrador', 'admin@produccionesangel.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Dirección Principal 123', '+1-555-0123', 'administrador'),
('admin', 'add@example.com', 'add123', 'Dirección Principal 123', '+1-545-0123', 'administrador');

-- Insertar algunos usuarios de ejemplo
-- Contraseña para todos: password123
INSERT INTO usuarios (nombre, email, clave, direccion, telefono, role) VALUES
('Juan Pérez', 'juan.perez@email.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Calle Principal 456', '+1-555-0456', 'cliente'),
('María García', 'maria.garcia@email.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Avenida Central 789', '+1-555-0789', 'cliente'),
('Carlos López', 'carlos.lopez@email.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Plaza Mayor 321', '+1-555-0321', 'cliente');

-- Insertar algunos mensajes de contacto de ejemplo
INSERT INTO mensajes_contacto (nombre, email, telefono, mensaje) VALUES
('Ana Martínez', 'ana.martinez@email.com', '+1-555-0101', 'Hola, me interesa saber más sobre los productos de gaming que tienen disponibles. ¿Podrían contactarme?'),
('Roberto Silva', 'roberto.silva@email.com', '+1-555-0202', 'Buenos días, quisiera información sobre el servicio técnico para laptops. Gracias.'),
('Laura Rodríguez', 'laura.rodriguez@email.com', '+1-555-0303', '¿Tienen garantía extendida para las cámaras Canon? Necesito esta información para una compra corporativa.');

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_producto_nombre ON producto(nombre);
CREATE INDEX idx_producto_precio ON producto(precio);
CREATE INDEX idx_producto_stock ON producto(stock);
CREATE INDEX idx_usuario_email ON usuarios(email);
CREATE INDEX idx_venta_fecha ON venta(fecha);
CREATE INDEX idx_mensajes_fecha ON mensajes_contacto(fecha);

-- Mostrar mensaje de confirmación
SELECT 'Base de datos "Producciones Angel" creada exitosamente con datos de ejemplo.' AS mensaje;
