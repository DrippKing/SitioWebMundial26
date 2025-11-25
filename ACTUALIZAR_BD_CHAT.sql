-- ================================================
-- Actualización de Base de Datos - Sistema de Chat Completo
-- ================================================

-- 1. Modificar tabla mensajes para agregar nuevas funcionalidades
ALTER TABLE mensajes 
ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER message_text,
ADD COLUMN message_type ENUM('text', 'image', 'file') DEFAULT 'text' AFTER is_read,
ADD COLUMN file_url VARCHAR(255) NULL AFTER message_type,
ADD COLUMN edited_at TIMESTAMP NULL AFTER timestamp,
ADD COLUMN deleted TINYINT(1) DEFAULT 0 AFTER edited_at;

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

-- 6. Crear tabla para notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    tipo ENUM('message', 'friend_request', 'group_invite') DEFAULT 'message',
    mensaje TEXT NOT NULL,
    leida TINYINT(1) DEFAULT 0,
    referencia_id INT(11) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida)
);

-- 7. Insertar algunos grupos de ejemplo
INSERT INTO grupos (nombre, descripcion, creador_id) VALUES
('TILINES', 'Grupo de amigos', 1),
('LMEADOS', 'Equipo de trabajo', 1)
ON DUPLICATE KEY UPDATE nombre=nombre;

-- 8. Mostrar resumen
SELECT 'Tablas actualizadas correctamente' as Status;
