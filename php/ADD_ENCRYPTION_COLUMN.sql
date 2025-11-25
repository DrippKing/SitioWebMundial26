-- Agregar columna is_encrypted a las tablas de mensajes

ALTER TABLE mensajes 
ADD COLUMN is_encrypted TINYINT(1) DEFAULT 0;

ALTER TABLE mensajes_grupo 
ADD COLUMN is_encrypted TINYINT(1) DEFAULT 0;

-- Verificar estructura
DESCRIBE mensajes;
DESCRIBE mensajes_grupo;
