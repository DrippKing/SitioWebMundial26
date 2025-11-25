-- ========================================
-- Script de Datos de Prueba para el Chat
-- ========================================

-- 1. Insertar usuarios de prueba (si no existen)
-- Nota: Las contraseñas están hasheadas con password_hash() de PHP
-- Contraseña para todos: "test123"

INSERT INTO usuarios (nombre, apellido1, usuario, email, contrasena, foto_perfil) VALUES
('Juan', 'Pérez', 'juanp', 'juan@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.jpg'),
('María', 'García', 'mariag', 'maria@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.jpg'),
('Carlos', 'López', 'carlosl', 'carlos@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.jpg'),
('Ana', 'Martínez', 'anam', 'ana@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.jpg'),
('Pedro', 'Sánchez', 'pedros', 'pedro@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.jpg')
ON DUPLICATE KEY UPDATE usuario=usuario; -- No hace nada si ya existe

-- 2. Insertar mensajes de prueba entre usuarios
-- Conversación entre usuario 1 y usuario 2
INSERT INTO mensajes (sender_id, receiver_id, message_text, timestamp) VALUES
(1, 2, 'Hola, ¿cómo estás?', '2025-11-23 10:00:00'),
(2, 1, '¡Muy bien! ¿Y tú?', '2025-11-23 10:01:00'),
(1, 2, 'Todo bien, trabajando en el proyecto', '2025-11-23 10:02:00'),
(2, 1, 'Genial, yo también estoy con el POI', '2025-11-23 10:03:00'),
(1, 2, '¿Ya terminaste la parte del chat?', '2025-11-23 10:05:00'),
(2, 1, 'Casi, me falta probarlo', '2025-11-23 10:06:00');

-- Conversación entre usuario 1 y usuario 3
INSERT INTO mensajes (sender_id, receiver_id, message_text, timestamp) VALUES
(1, 3, '¿Nos vemos hoy?', '2025-11-23 11:00:00'),
(3, 1, 'Sí, a las 3pm', '2025-11-23 11:05:00'),
(1, 3, 'Perfecto, ahí nos vemos', '2025-11-23 11:10:00');

-- Conversación entre usuario 1 y usuario 4
INSERT INTO mensajes (sender_id, receiver_id, message_text, timestamp) VALUES
(4, 1, '¿Tienes los apuntes de la clase?', '2025-11-23 09:00:00'),
(1, 4, 'Sí, te los envío por email', '2025-11-23 09:15:00'),
(4, 1, 'Gracias!', '2025-11-23 09:20:00');

-- 3. Mostrar resumen
SELECT 'Usuarios insertados:' as info, COUNT(*) as total FROM usuarios;
SELECT 'Mensajes insertados:' as info, COUNT(*) as total FROM mensajes;

-- 4. Mostrar conversaciones
SELECT 
    u1.usuario as 'De',
    u2.usuario as 'Para',
    m.message_text as 'Mensaje',
    m.timestamp as 'Hora'
FROM mensajes m
INNER JOIN usuarios u1 ON m.sender_id = u1.id
INNER JOIN usuarios u2 ON m.receiver_id = u2.id
ORDER BY m.timestamp DESC
LIMIT 10;
