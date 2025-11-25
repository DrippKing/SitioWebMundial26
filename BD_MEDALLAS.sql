-- ========================================
-- SISTEMA DE MEDALLAS Y LOGROS
-- ========================================

-- Crear tabla de medallas
CREATE TABLE IF NOT EXISTS medallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(255) NOT NULL,
    condicion_tipo ENUM('amigos', 'victorias', 'derrotas', 'ranking') NOT NULL,
    condicion_valor INT NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de medallas de usuarios
CREATE TABLE IF NOT EXISTS usuario_medallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    medalla_id INT NOT NULL,
    fecha_obtencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (medalla_id) REFERENCES medallas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_medalla (usuario_id, medalla_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar las medallas disponibles
INSERT INTO medallas (codigo, nombre, descripcion, icono, condicion_tipo, condicion_valor) VALUES
('primer_amigo', 'Primer Amigo', 'Agregaste a tu primer amigo', '../MEDALLAS/logro_primeramigo.png', 'amigos', 1),
('primera_victoria', 'Primera Victoria', 'Acertaste tu primera predicción', '../MEDALLAS/logro_primeravictoria.png', 'victorias', 1),
('primera_derrota', 'Primera Derrota', 'Fallaste tu primera predicción', '../MEDALLAS/logro_primeraderrota.png', 'derrotas', 1),
('salado', 'Salado', 'Perdiste 10 predicciones', '../MEDALLAS/logro_salado.png', 'derrotas', 10),
('top_global', 'Top Global', 'Llegaste al top 3 del ranking final', '../MEDALLAS/logro_topglobal.png', 'ranking', 3);
