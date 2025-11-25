-- ================================================
-- SISTEMA DE AMIGOS - Tablas
-- ================================================

-- Tabla de solicitudes de amistad
CREATE TABLE IF NOT EXISTS friend_requests (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    sender_id INT(11) NOT NULL,
    receiver_id INT(11) NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_request (sender_id, receiver_id)
);

-- Tabla de amigos (solo cuando se acepta la solicitud)
CREATE TABLE IF NOT EXISTS friends (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    friend_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_friendship (user_id, friend_id)
);

-- Índices para optimizar búsquedas
CREATE INDEX idx_requests_receiver ON friend_requests(receiver_id, status);
CREATE INDEX idx_requests_sender ON friend_requests(sender_id, status);
CREATE INDEX idx_friends_user ON friends(user_id);

SELECT 'Tablas de amigos creadas correctamente' as Resultado;
