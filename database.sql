CREATE DATABASE IF NOT EXISTS clubes_deportivos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clubes_deportivos;

CREATE TABLE IF NOT EXISTS clubes (
  club_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  deporte VARCHAR(60) NOT NULL,
  ubicacion VARCHAR(150) NOT NULL,
  fecha_fundacion DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS miembros (
  miembro_id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  apellido VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  telefono VARCHAR(20)
);

-- Relación N:M: un miembro puede inscribirse en varios clubes
CREATE TABLE IF NOT EXISTS inscripciones (
  inscripcion_id INT AUTO_INCREMENT PRIMARY KEY,
  club_id INT NOT NULL,
  miembro_id INT NOT NULL,
  fecha_inscripcion DATE NOT NULL,
  UNIQUE KEY uq_club_miembro (club_id, miembro_id),
  FOREIGN KEY (club_id) REFERENCES clubes(club_id) ON DELETE CASCADE,
  FOREIGN KEY (miembro_id) REFERENCES miembros(miembro_id) ON DELETE CASCADE
);

INSERT INTO clubes (nombre, deporte, ubicacion, fecha_fundacion) VALUES
('Club Imbabura', 'Fútbol', 'Ibarra, Ecuador', '1985-03-12'),
('Los Andes Basket', 'Baloncesto', 'Otavalo, Ecuador', '1999-07-01');
INSERT INTO miembros (nombre, apellido, email, telefono) VALUES
('Juan', 'Pérez', 'juan@correo.com', '0991234567'),
('María', 'Gómez', 'maria@correo.com', '0987654321');
