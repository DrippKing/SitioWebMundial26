<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'db_connection.php';
    $conexion->set_charset('utf8mb4');
    
    $reporte = [
        'usuarios' => [],
        'grupo_LMEADOS' => [],
        'miembros_LMEADOS' => []
    ];
    
    // 1. Listar todos los usuarios
    $res = $conexion->query("SELECT id, usuario FROM usuarios ORDER BY id");
    while ($row = $res->fetch_assoc()) {
        $reporte['usuarios'][] = [
            'id' => $row['id'],
            'usuario' => $row['usuario']
        ];
    }
    
    // 2. Información del grupo LMEADOS
    $res = $conexion->query("SELECT * FROM grupos WHERE id = 2");
    if ($row = $res->fetch_assoc()) {
        $reporte['grupo_LMEADOS'] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'creador_id' => $row['creador_id']
        ];
    }
    
    // 3. Miembros de LMEADOS
    $res = $conexion->query("
        SELECT gm.usuario_id, u.usuario, gm.es_admin, gm.unido_at
        FROM grupo_miembros gm
        JOIN usuarios u ON gm.usuario_id = u.id
        WHERE gm.grupo_id = 2
        ORDER BY gm.usuario_id
    ");
    while ($row = $res->fetch_assoc()) {
        $reporte['miembros_LMEADOS'][] = [
            'usuario_id' => $row['usuario_id'],
            'usuario' => $row['usuario'],
            'es_admin' => $row['es_admin'],
            'unido_at' => $row['unido_at']
        ];
    }
    
    // 4. Mensajes en LMEADOS
    $res = $conexion->query("SELECT COUNT(*) as total FROM mensajes_grupo WHERE grupo_id = 2");
    $msg_count = $res->fetch_assoc();
    $reporte['mensajes_en_LMEADOS'] = $msg_count['total'];
    
    echo json_encode($reporte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
