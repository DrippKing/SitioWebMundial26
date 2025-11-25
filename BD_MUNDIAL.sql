-- ========================================
-- TABLAS PARA SIMULADOR DE MUNDIAL
-- ========================================

-- Tabla de equipos
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(3) NOT NULL UNIQUE, -- ARG, BRA, ESP, etc.
    grupo CHAR(1) NOT NULL, -- A, B, C, D, E, F, G, H
    bandera VARCHAR(255), -- Ruta a imagen de bandera
    puntos INT DEFAULT 0,
    partidos_jugados INT DEFAULT 0,
    partidos_ganados INT DEFAULT 0,
    partidos_empatados INT DEFAULT 0,
    partidos_perdidos INT DEFAULT 0,
    goles_favor INT DEFAULT 0,
    goles_contra INT DEFAULT 0,
    diferencia_goles INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de partidos
CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_local_id INT NOT NULL,
    equipo_visitante_id INT NOT NULL,
    fase ENUM('grupo', 'octavos', 'cuartos', 'semifinal', 'tercer_lugar', 'final') NOT NULL,
    jornada INT NULL, -- 1, 2, 3 para fase de grupos
    grupo CHAR(1) NULL, -- A-H para fase de grupos
    fecha_partido DATETIME NOT NULL,
    estadio VARCHAR(100),
    goles_local INT NULL,
    goles_visitante INT NULL,
    penales_local INT NULL, -- Para fase eliminatoria
    penales_visitante INT NULL,
    finalizado BOOLEAN DEFAULT FALSE,
    ganador_id INT NULL, -- ID del equipo ganador (importante para eliminatorias)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_local_id) REFERENCES equipos(id),
    FOREIGN KEY (equipo_visitante_id) REFERENCES equipos(id),
    FOREIGN KEY (ganador_id) REFERENCES equipos(id),
    INDEX idx_fase (fase),
    INDEX idx_grupo (grupo),
    INDEX idx_fecha (fecha_partido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de predicciones de usuarios
CREATE TABLE IF NOT EXISTS predicciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    partido_id INT NOT NULL,
    goles_local_prediccion INT NOT NULL,
    goles_visitante_prediccion INT NOT NULL,
    penales_local_prediccion INT NULL,
    penales_visitante_prediccion INT NULL,
    puntos_ganados INT DEFAULT 0, -- Se calculan cuando finaliza el partido
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (partido_id) REFERENCES partidos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_prediccion (usuario_id, partido_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_partido (partido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de puntuaciones del torneo (ranking general)
CREATE TABLE IF NOT EXISTS puntuaciones_torneo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    puntos_totales INT DEFAULT 0,
    predicciones_exactas INT DEFAULT 0, -- Resultado exacto
    predicciones_tendencia INT DEFAULT 0, -- Ganador correcto pero marcador diferente
    predicciones_incorrectas INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- DATOS INICIALES - EQUIPOS DEL MUNDIAL 2026
-- ========================================



-- ========================================
-- PARTIDOS FASE DE GRUPOS
-- ========================================

-- GRUPO A - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='QAT'), (SELECT id FROM equipos WHERE codigo='ECU'), 'grupo', 1, 'A', '2026-06-11 12:00:00', 'Al Bayt'),
((SELECT id FROM equipos WHERE codigo='SEN'), (SELECT id FROM equipos WHERE codigo='NED'), 'grupo', 1, 'A', '2026-06-11 15:00:00', 'Al Thumama');

-- GRUPO A - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='QAT'), (SELECT id FROM equipos WHERE codigo='SEN'), 'grupo', 2, 'A', '2026-06-15 12:00:00', 'Al Thumama'),
((SELECT id FROM equipos WHERE codigo='NED'), (SELECT id FROM equipos WHERE codigo='ECU'), 'grupo', 2, 'A', '2026-06-15 15:00:00', 'Khalifa');

-- GRUPO A - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='NED'), (SELECT id FROM equipos WHERE codigo='QAT'), 'grupo', 3, 'A', '2026-06-19 18:00:00', 'Al Bayt'),
((SELECT id FROM equipos WHERE codigo='ECU'), (SELECT id FROM equipos WHERE codigo='SEN'), 'grupo', 3, 'A', '2026-06-19 18:00:00', 'Khalifa');

-- GRUPO B - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='ENG'), (SELECT id FROM equipos WHERE codigo='IRN'), 'grupo', 1, 'B', '2026-06-12 12:00:00', 'Khalifa'),
((SELECT id FROM equipos WHERE codigo='USA'), (SELECT id FROM equipos WHERE codigo='WAL'), 'grupo', 1, 'B', '2026-06-12 15:00:00', 'Ahmad Bin Ali');

-- GRUPO B - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='WAL'), (SELECT id FROM equipos WHERE codigo='IRN'), 'grupo', 2, 'B', '2026-06-16 12:00:00', 'Ahmad Bin Ali'),
((SELECT id FROM equipos WHERE codigo='ENG'), (SELECT id FROM equipos WHERE codigo='USA'), 'grupo', 2, 'B', '2026-06-16 15:00:00', 'Al Bayt');

-- GRUPO B - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='WAL'), (SELECT id FROM equipos WHERE codigo='ENG'), 'grupo', 3, 'B', '2026-06-20 18:00:00', 'Ahmad Bin Ali'),
((SELECT id FROM equipos WHERE codigo='IRN'), (SELECT id FROM equipos WHERE codigo='USA'), 'grupo', 3, 'B', '2026-06-20 18:00:00', 'Al Thumama');

-- GRUPO C - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='ARG'), (SELECT id FROM equipos WHERE codigo='KSA'), 'grupo', 1, 'C', '2026-06-13 12:00:00', 'Lusail'),
((SELECT id FROM equipos WHERE codigo='MEX'), (SELECT id FROM equipos WHERE codigo='POL'), 'grupo', 1, 'C', '2026-06-13 15:00:00', 'Stadium 974');

-- GRUPO C - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='POL'), (SELECT id FROM equipos WHERE codigo='KSA'), 'grupo', 2, 'C', '2026-06-17 12:00:00', 'Education City'),
((SELECT id FROM equipos WHERE codigo='ARG'), (SELECT id FROM equipos WHERE codigo='MEX'), 'grupo', 2, 'C', '2026-06-17 15:00:00', 'Lusail');

-- GRUPO C - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='POL'), (SELECT id FROM equipos WHERE codigo='ARG'), 'grupo', 3, 'C', '2026-06-21 18:00:00', 'Stadium 974'),
((SELECT id FROM equipos WHERE codigo='KSA'), (SELECT id FROM equipos WHERE codigo='MEX'), 'grupo', 3, 'C', '2026-06-21 18:00:00', 'Lusail');

-- GRUPO D - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='FRA'), (SELECT id FROM equipos WHERE codigo='AUS'), 'grupo', 1, 'D', '2026-06-14 12:00:00', 'Al Janoub'),
((SELECT id FROM equipos WHERE codigo='DEN'), (SELECT id FROM equipos WHERE codigo='TUN'), 'grupo', 1, 'D', '2026-06-14 15:00:00', 'Education City');

-- GRUPO D - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='TUN'), (SELECT id FROM equipos WHERE codigo='AUS'), 'grupo', 2, 'D', '2026-06-18 12:00:00', 'Al Janoub'),
((SELECT id FROM equipos WHERE codigo='FRA'), (SELECT id FROM equipos WHERE codigo='DEN'), 'grupo', 2, 'D', '2026-06-18 15:00:00', 'Stadium 974');

-- GRUPO D - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='TUN'), (SELECT id FROM equipos WHERE codigo='FRA'), 'grupo', 3, 'D', '2026-06-22 18:00:00', 'Education City'),
((SELECT id FROM equipos WHERE codigo='AUS'), (SELECT id FROM equipos WHERE codigo='DEN'), 'grupo', 3, 'D', '2026-06-22 18:00:00', 'Al Janoub');

-- Continúa para GRUPOS E, F, G, H siguiendo el mismo patrón...
-- GRUPO E - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='ESP'), (SELECT id FROM equipos WHERE codigo='CRC'), 'grupo', 1, 'E', '2026-06-15 12:00:00', 'Al Thumama'),
((SELECT id FROM equipos WHERE codigo='GER'), (SELECT id FROM equipos WHERE codigo='JPN'), 'grupo', 1, 'E', '2026-06-15 15:00:00', 'Khalifa');

-- GRUPO E - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='JPN'), (SELECT id FROM equipos WHERE codigo='CRC'), 'grupo', 2, 'E', '2026-06-19 12:00:00', 'Ahmad Bin Ali'),
((SELECT id FROM equipos WHERE codigo='ESP'), (SELECT id FROM equipos WHERE codigo='GER'), 'grupo', 2, 'E', '2026-06-19 15:00:00', 'Al Bayt');

-- GRUPO E - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='JPN'), (SELECT id FROM equipos WHERE codigo='ESP'), 'grupo', 3, 'E', '2026-06-23 18:00:00', 'Khalifa'),
((SELECT id FROM equipos WHERE codigo='CRC'), (SELECT id FROM equipos WHERE codigo='GER'), 'grupo', 3, 'E', '2026-06-23 18:00:00', 'Al Bayt');

-- GRUPO F - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='BEL'), (SELECT id FROM equipos WHERE codigo='CAN'), 'grupo', 1, 'F', '2026-06-16 12:00:00', 'Ahmad Bin Ali'),
((SELECT id FROM equipos WHERE codigo='MAR'), (SELECT id FROM equipos WHERE codigo='CRO'), 'grupo', 1, 'F', '2026-06-16 15:00:00', 'Al Bayt');

-- GRUPO F - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='CRO'), (SELECT id FROM equipos WHERE codigo='CAN'), 'grupo', 2, 'F', '2026-06-20 12:00:00', 'Khalifa'),
((SELECT id FROM equipos WHERE codigo='BEL'), (SELECT id FROM equipos WHERE codigo='MAR'), 'grupo', 2, 'F', '2026-06-20 15:00:00', 'Al Thumama');

-- GRUPO F - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='CRO'), (SELECT id FROM equipos WHERE codigo='BEL'), 'grupo', 3, 'F', '2026-06-24 18:00:00', 'Ahmad Bin Ali'),
((SELECT id FROM equipos WHERE codigo='CAN'), (SELECT id FROM equipos WHERE codigo='MAR'), 'grupo', 3, 'F', '2026-06-24 18:00:00', 'Al Thumama');

-- GRUPO G - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='BRA'), (SELECT id FROM equipos WHERE codigo='SRB'), 'grupo', 1, 'G', '2026-06-17 12:00:00', 'Lusail'),
((SELECT id FROM equipos WHERE codigo='SUI'), (SELECT id FROM equipos WHERE codigo='CMR'), 'grupo', 1, 'G', '2026-06-17 15:00:00', 'Al Janoub');

-- GRUPO G - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='CMR'), (SELECT id FROM equipos WHERE codigo='SRB'), 'grupo', 2, 'G', '2026-06-21 12:00:00', 'Al Janoub'),
((SELECT id FROM equipos WHERE codigo='BRA'), (SELECT id FROM equipos WHERE codigo='SUI'), 'grupo', 2, 'G', '2026-06-21 15:00:00', 'Stadium 974');

-- GRUPO G - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='CMR'), (SELECT id FROM equipos WHERE codigo='BRA'), 'grupo', 3, 'G', '2026-06-25 18:00:00', 'Lusail'),
((SELECT id FROM equipos WHERE codigo='SRB'), (SELECT id FROM equipos WHERE codigo='SUI'), 'grupo', 3, 'G', '2026-06-25 18:00:00', 'Stadium 974');

-- GRUPO H - Jornada 1
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='POR'), (SELECT id FROM equipos WHERE codigo='GHA'), 'grupo', 1, 'H', '2026-06-18 12:00:00', 'Stadium 974'),
((SELECT id FROM equipos WHERE codigo='URU'), (SELECT id FROM equipos WHERE codigo='KOR'), 'grupo', 1, 'H', '2026-06-18 15:00:00', 'Education City');

-- GRUPO H - Jornada 2
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='KOR'), (SELECT id FROM equipos WHERE codigo='GHA'), 'grupo', 2, 'H', '2026-06-22 12:00:00', 'Education City'),
((SELECT id FROM equipos WHERE codigo='POR'), (SELECT id FROM equipos WHERE codigo='URU'), 'grupo', 2, 'H', '2026-06-22 15:00:00', 'Lusail');

-- GRUPO H - Jornada 3
INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fase, jornada, grupo, fecha_partido, estadio) VALUES
((SELECT id FROM equipos WHERE codigo='KOR'), (SELECT id FROM equipos WHERE codigo='POR'), 'grupo', 3, 'H', '2026-06-26 18:00:00', 'Education City'),
((SELECT id FROM equipos WHERE codigo='GHA'), (SELECT id FROM equipos WHERE codigo='URU'), 'grupo', 3, 'H', '2026-06-26 18:00:00', 'Al Janoub');

-- ========================================
-- NOTA: Los partidos de OCTAVOS, CUARTOS, SEMIFINALES y FINAL
-- se crearán dinámicamente según los resultados de la fase de grupos
-- ========================================
