-- Agregar columna is_online para controlar estado de sesión
ALTER TABLE usuarios 
ADD COLUMN is_online TINYINT(1) DEFAULT 0 AFTER last_activity;

-- Actualizar todos los usuarios a offline por defecto
UPDATE usuarios SET is_online = 0;
