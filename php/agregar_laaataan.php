<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'db_connection.php';
    $conexion->set_charset('utf8mb4');
    
    // Agregar LaaaTaaan (ID 6) al grupo LMEADOS (ID 2)
    $stmt = $conexion->prepare("
        INSERT INTO grupo_miembros (grupo_id, usuario_id, es_admin)
        VALUES (2, 6, 0)
        ON DUPLICATE KEY UPDATE usuario_id = usuario_id
    ");
    
    if ($stmt->execute()) {
        // Verificar que se agregó
        $res = $conexion->query("
            SELECT gm.usuario_id, u.usuario
            FROM grupo_miembros gm
            JOIN usuarios u ON gm.usuario_id = u.id
            WHERE gm.grupo_id = 2
            ORDER BY gm.usuario_id
        ");
        
        $miembros = [];
        while ($row = $res->fetch_assoc()) {
            $miembros[] = [
                'id' => $row['usuario_id'],
                'usuario' => $row['usuario']
            ];
        }
        
        echo json_encode([
            'exitoso' => true,
            'mensaje' => 'LaaaTaaan agregado al grupo LMEADOS',
            'miembros_actuales' => $miembros
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'exitoso' => false,
            'error' => $stmt->error
        ]);
    }
    
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
