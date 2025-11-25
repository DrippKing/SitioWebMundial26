<?php
header('Content-Type: application/json');

try {
    $conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    $conexion->set_charset('utf8');
    
    $grupo_id = 1; // ID del grupo LMEADOS
    $miembros = [];
    
    // Obtener miembros del grupo
    $res = $conexion->query("SELECT usuario_id FROM grupo_miembros WHERE grupo_id = $grupo_id");
    if (!$res) {
        throw new Exception("Error en consulta: " . $conexion->error);
    }
    
    while ($row = $res->fetch_assoc()) {
        $miembros[] = $row['usuario_id'];
    }
    
    // Si no hay miembros, retornar array vacío
    if (empty($miembros)) {
        echo json_encode([]);
        $conexion->close();
        exit;
    }
$tareas = [
    'mensaje' => [],
    'foto' => [],
    'documento' => [],
    'ubicacion' => []
];
foreach ($miembros as $uid) {
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid");
    $tareas['mensaje'][$uid] = $r->fetch_assoc()['c'] > 0;
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.gif' OR file_url LIKE '%.webp')");
    $tareas['foto'][$uid] = $r->fetch_assoc()['c'] > 0;
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.pdf' OR file_url LIKE '%.doc%' OR file_url LIKE '%.xls%' OR file_url LIKE '%.ppt%' OR file_url LIKE '%.txt')");
    $tareas['documento'][$uid] = $r->fetch_assoc()['c'] > 0;
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND message_text LIKE '%maps.google.com%'");
    $tareas['ubicacion'][$uid] = $r->fetch_assoc()['c'] > 0;
}
$nombres = [];
$ids_str = implode(',', $miembros);
$res = $conexion->query("SELECT id, usuario FROM usuarios WHERE id IN ($ids_str)");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $nombres[$row['id']] = $row['usuario'];
    }
}
$resultado = [];
foreach ($miembros as $uid) {
    $resultado[] = [
        'usuario' => $nombres[$uid] ?? 'Usuario ' . $uid,
        'mensaje' => $tareas['mensaje'][$uid] ?? false,
        'foto' => $tareas['foto'][$uid] ?? false,
        'documento' => $tareas['documento'][$uid] ?? false,
        'ubicacion' => $tareas['ubicacion'][$uid] ?? false
    ];
}

echo json_encode($resultado);
$conexion->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>