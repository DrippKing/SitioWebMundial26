-- ================================================
-- ACTUALIZACIÓN RÁPIDA - Solo agregar columnas faltantes
-- ================================================

-- Agregar columnas a la tabla mensajes
ALTER TABLE mensajes 
ADD COLUMN IF NOT EXISTS is_read TINYINT(1) DEFAULT 0 AFTER message_text,
ADD COLUMN IF NOT EXISTS message_type ENUM('text', 'image', 'file') DEFAULT 'text' AFTER is_read,
ADD COLUMN IF NOT EXISTS file_url VARCHAR(255) NULL AFTER message_type;

-- Agregar columnas a la tabla mensajes_grupo
ALTER TABLE mensajes_grupo
ADD COLUMN IF NOT EXISTS is_read TINYINT(1) DEFAULT 0 AFTER message_text,
ADD COLUMN IF NOT EXISTS message_type ENUM('text', 'image', 'file') DEFAULT 'text' AFTER is_read,
ADD COLUMN IF NOT EXISTS file_url VARCHAR(255) NULL AFTER message_type;

-- Verificar cambios
SHOW COLUMNS FROM mensajes LIKE '%read%';
SHOW COLUMNS FROM mensajes LIKE 'file_url';
SHOW COLUMNS FROM mensajes_grupo LIKE 'file_url';

SELECT 'Columnas agregadas correctamente' as Resultado;
