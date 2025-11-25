-- Crear grupo LMEADOS con usuarios específicos

-- Insertar el grupo LMEADOS
INSERT INTO grupos (nombre, descripcion, foto_grupo, creador_id) 
VALUES ('LMEADOS', 'Grupo exclusivo', 'default_group.jpg', 4);

-- Obtener el ID del grupo recién creado
SET @grupo_id = LAST_INSERT_ID();

-- Agregar los 3 miembros al grupo
-- ID 4 = Alfo123
-- ID 1 = eljazmen  
-- ID 6 = LaaaTaaan

INSERT INTO grupo_miembros (grupo_id, usuario_id, es_admin) VALUES
(@grupo_id, 4, 1),
(@grupo_id, 1, 0),
(@grupo_id, 6, 0);

-- Verificar el grupo creado
SELECT g.id, g.nombre, g.descripcion, 
       GROUP_CONCAT(u.usuario SEPARATOR ', ') as miembros
FROM grupos g
LEFT JOIN grupo_miembros gm ON g.id = gm.grupo_id
LEFT JOIN usuarios u ON gm.usuario_id = u.id
WHERE g.nombre = 'LMEADOS'
GROUP BY g.id;
