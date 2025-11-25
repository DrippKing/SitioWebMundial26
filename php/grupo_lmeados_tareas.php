<?php
// Script para verificar tareas del grupo LMEADOS
$conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);
$conexion->set_charset('utf8');

// ID del grupo LMEADOS (ajustar si es necesario)
$grupo_id = 1; // Cambia este valor si el ID real es diferente

// Obtener miembros del grupo
$miembros = [];
$res = $conexion->query("SELECT usuario_id FROM grupo_miembros WHERE grupo_id = $grupo_id");
while ($row = $res->fetch_assoc()) {
    $miembros[] = $row['usuario_id'];
}

$tareas = [
    'mensaje' => [],
    'foto' => [],
    'documento' => [],
    'ubicacion' => []
];

foreach ($miembros as $uid) {
    // 1. ¿Ha enviado al menos 1 mensaje?
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid");
    $tareas['mensaje'][$uid] = $r->fetch_assoc()['c'] > 0;
    // 2. ¿Ha enviado al menos 1 foto?
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.gif' OR file_url LIKE '%.webp')");
    $tareas['foto'][$uid] = $r->fetch_assoc()['c'] > 0;
    // 3. ¿Ha enviado al menos 1 documento?
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.pdf' OR file_url LIKE '%.doc%' OR file_url LIKE '%.xls%' OR file_url LIKE '%.ppt%' OR file_url LIKE '%.txt')");
    $tareas['documento'][$uid] = $r->fetch_assoc()['c'] > 0;
    // 4. ¿Ha enviado al menos 1 ubicación?
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND message_text LIKE '%maps.google.com%'");
    $tareas['ubicacion'][$uid] = $r->fetch_assoc()['c'] > 0;
}

// Obtener nombres de usuario
$nombres = [];
$res = $conexion->query("SELECT id, usuario FROM usuarios WHERE id IN (" . implode(',', $miembros) . ")");
while ($row = $res->fetch_assoc()) {
    $nombres[$row['id']] = $row['usuario'];
}

// Mostrar resultados
foreach ($miembros as $uid) {
    echo "Usuario: " . ($nombres[$uid] ?? $uid) . "\n";
    echo "- Mensaje:      " . ($tareas['mensaje'][$uid] ? '✅' : '❌') . "\n";
    echo "- Foto:         " . ($tareas['foto'][$uid] ? '✅' : '❌') . "\n";
    echo "- Documento:    " . ($tareas['documento'][$uid] ? '✅' : '❌') . "\n";
    echo "- Ubicación:    " . ($tareas['ubicacion'][$uid] ? '✅' : '❌') . "\n";
    echo "--------------------------\n";
}
$conexion->close();
