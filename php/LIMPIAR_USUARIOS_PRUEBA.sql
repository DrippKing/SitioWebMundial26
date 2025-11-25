-- Eliminar usuarios de prueba y sus relaciones

-- Primero, eliminar todas las solicitudes de amistad
DELETE FROM friend_requests;

-- Eliminar todas las amistades
DELETE FROM friends;

-- Eliminar predicciones de usuarios de prueba
DELETE FROM predicciones WHERE user_id IN (
    SELECT id FROM usuarios WHERE usuario LIKE 'Username%' OR usuario IN ('Jazmen2002', 'gaelkreis')
);

-- Eliminar medallas de usuarios de prueba
DELETE FROM usuario_medallas WHERE usuario_id IN (
    SELECT id FROM usuarios WHERE usuario LIKE 'Username%' OR usuario IN ('Jazmen2002', 'gaelkreis')
);

-- Eliminar mensajes de usuarios de prueba
DELETE FROM messages WHERE sender_id IN (
    SELECT id FROM usuarios WHERE usuario LIKE 'Username%' OR usuario IN ('Jazmen2002', 'gaelkreis')
) OR receiver_id IN (
    SELECT id FROM usuarios WHERE usuario LIKE 'Username%' OR usuario IN ('Jazmen2002', 'gaelkreis')
);

-- Finalmente, eliminar los usuarios de prueba
-- CUIDADO: Esto NO eliminará al usuario con ID=1 (admin) ni a ningún otro usuario real
DELETE FROM usuarios 
WHERE usuario LIKE 'Username%' 
   OR usuario IN ('Jazmen2002', 'gaelkreis');

-- Verificar usuarios restantes
SELECT id, usuario, email, nombre FROM usuarios;
