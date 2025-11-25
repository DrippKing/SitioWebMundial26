-- Agregar columna de última actividad a usuarios
ALTER TABLE usuarios ADD COLUMN last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Actualizar todos los usuarios con la hora actual
UPDATE usuarios SET last_activity = NOW();
