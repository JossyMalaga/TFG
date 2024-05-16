CREATE SCHEMA db_tiendaOnline;

USE db_tiendaOnline;

CREATE TABLE usuarios ( usuario VARCHAR(12) PRIMARY KEY, contrasena VARCHAR(255) NOT NULL, fechaNacimiento DATE NOT NULL );

ALTER TABLE usuarios ADD COLUMN rol VARCHAR(10) DEFAULT 'cliente';

CREATE TABLE productos( idProducto INTEGER(8) PRIMARY KEY AUTO_INCREMENT, nombreProducto VARCHAR(40) NOT NULL, precio NUMERIC(7, 2) NOT NULL, descripcion VARCHAR(255) NOT NULL, cantidad NUMERIC(5, 0) NOT NULL );

ALTER TABLE productos ADD COLUMN imagen VARCHAR(100);

CREATE TABLE cestas( idCesta INTEGER(8) PRIMARY KEY AUTO_INCREMENT, usuario VARCHAR(12) NOT NULL, precioTotal NUMERIC(7, 2) NOT NULL DEFAULT 0, FOREIGN KEY (usuario) REFERENCES usuarios(usuario) );

CREATE TABLE productosCestas( idProducto INTEGER(8), idCesta INTEGER(8), cantidad NUMERIC(2,0) NOT NULL, FOREIGN KEY (idProducto) REFERENCES productos(idProducto), FOREIGN KEY (idCesta) REFERENCES cestas(idCesta), CONSTRAINT PK_ProductoCesta PRIMARY KEY(idProducto, idCesta) );

CREATE TABLE pedidos( idPedido INTEGER(8) PRIMARY KEY AUTO_INCREMENT, usuario VARCHAR(12) NOT NULL, precioTotal NUMERIC(7, 2) NOT NULL, fechaPedido DATE NOT NULL, FOREIGN KEY (usuario) REFERENCES usuarios (usuario) );

CREATE TABLE lineasPedidos( lineaPedido NUMERIC(2) NOT NULL, idProducto INTEGER(8) NOT NULL, idPedido INTEGER(8) NOT NULL, precioUnitario NUMERIC(7,2) NOT NULL, cantidad NUMERIC(2,0) NOT NULL, FOREIGN KEY (idPedido) REFERENCES pedidos(idPedido), FOREIGN KEY (idProducto) REFERENCES productos(idProducto));