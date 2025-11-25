-- ================================================
-- Script SQL Simple para Sistema de Chat
-- Ejecuta esto en phpMyAdmin
-- ================================================

-- 1. Crear tabla de mensajes (si no existe)
CREATE TABLE IF NOT EXISTS mensajes (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    sender_id INT(11) NOT NULL,
    receiver_id INT(11) NOT NULL,
    message_text TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_conversation (sender_id, receiver_id, timestamp),
    INDEX idx_timestamp (timestamp)
);

-- 2. Crear tabla para chats grupales
CREATE TABLE IF NOT EXISTS grupos (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    foto_grupo VARCHAR(255) DEFAULT 'default.jpg',
    creador_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Tabla de miembros de grupos
CREATE TABLE IF NOT EXISTS grupo_miembros (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    grupo_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    es_admin TINYINT(1) DEFAULT 0,
    unido_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (grupo_id, usuario_id)
);

-- 4. Tabla para mensajes de grupo
CREATE TABLE IF NOT EXISTS mensajes_grupo (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    grupo_id INT(11) NOT NULL,
    sender_id INT(11) NOT NULL,
    message_text TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_grupo_timestamp (grupo_id, timestamp)
);

-- 5. Insertar grupos de ejemplo (basados en tu HTML)
INSERT INTO grupos (nombre, descripcion, creador_id) 
SELECT 'TILINES', 'Canal de amigos', MIN(id) FROM usuarios
WHERE NOT EXISTS (SELECT 1 FROM grupos WHERE nombre = 'TILINES');

INSERT INTO grupos (nombre, descripcion, creador_id) 
SELECT 'LMEADOS', 'Canal de trabajo', MIN(id) FROM usuarios
WHERE NOT EXISTS (SELECT 1 FROM grupos WHERE nombre = 'LMEADOS');

-- 6. Agregar todos los usuarios a los grupos (ejemplo)
INSERT IGNORE INTO grupo_miembros (grupo_id, usuario_id)
SELECT g.id, u.id
FROM grupos g
CROSS JOIN usuarios u
WHERE g.nombre IN ('TILINES', 'LMEADOS');

-- Listo!
SELECT 'Base de datos actualizada correctamente' as Mensaje;
