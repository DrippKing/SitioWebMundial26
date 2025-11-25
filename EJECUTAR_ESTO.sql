-- ================================================
-- ARREGLO COMPLETO - Crear tablas y agregar columnas
-- ================================================

-- 1. Agregar columnas a la tabla mensajes
ALTER TABLE mensajes 
ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER message_text,
ADD COLUMN message_type ENUM('text', 'image', 'file') DEFAULT 'text' AFTER is_read,
ADD COLUMN file_url VARCHAR(255) NULL AFTER message_type;

-- 2. Crear tabla para chats grupales
CREATE TABLE IF NOT EXISTS grupos (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    foto_grupo VARCHAR(255) DEFAULT 'default_group.jpg',
    creador_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Crear tabla de miembros de grupos
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

-- 4. Crear tabla para mensajes de grupo
CREATE TABLE IF NOT EXISTS mensajes_grupo (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    grupo_id INT(11) NOT NULL,
    sender_id INT(11) NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',
    file_url VARCHAR(255) NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_grupo_timestamp (grupo_id, timestamp)
);

-- 5. Crear tabla para indicador de "escribiendo"
CREATE TABLE IF NOT EXISTS typing_status (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    chat_id INT(11) NOT NULL,
    chat_type ENUM('private', 'group') DEFAULT 'private',
    is_typing TINYINT(1) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_typing (user_id, chat_id, chat_type)
);

-- 6. Insertar grupos de ejemplo
INSERT INTO grupos (nombre, descripcion, creador_id) VALUES
('TILINES', 'Grupo de amigos', 1),
('LMEADOS', 'Equipo de trabajo', 1)
ON DUPLICATE KEY UPDATE nombre=nombre;

-- 7. Agregar todos los usuarios a todos los grupos
INSERT INTO grupo_miembros (grupo_id, usuario_id)
SELECT g.id, u.id
FROM grupos g
CROSS JOIN usuarios u
ON DUPLICATE KEY UPDATE grupo_id=grupo_id;

-- Verificar
SELECT 'Base de datos actualizada correctamente' as Resultado;
SELECT COUNT(*) as 'Total Grupos' FROM grupos;
SELECT COUNT(*) as 'Total Miembros' FROM grupo_miembros;
