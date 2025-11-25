<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
    $conexion->set_charset('utf8mb4');
    
    // Los 3 miembros que deben estar: eljazmen (1), Alfo123 (4), LaaaTaaan (6)
    $miembros_correctos = [1, 4, 6];
    $ids_str = implode(',', $miembros_correctos);
    
    // Eliminar a los que no deben estar
    $stmt = $conexion->prepare("
        DELETE FROM grupo_miembros 
        WHERE grupo_id = 2 AND usuario_id NOT IN ($ids_str)
    ");
    
    if ($stmt->execute()) {
        $resultado = [
            'exitoso' => true,
            'mensaje' => 'Grupo LMEADOS limpiado - Solo 3 miembros',
            'miembros_eliminados' => $stmt->affected_rows,
            'miembros_actuales' => []
        ];
        
        // Obtener miembros actuales
        $res = $conexion->query("
            SELECT gm.usuario_id, u.usuario, gm.es_admin
            FROM grupo_miembros gm
            JOIN usuarios u ON gm.usuario_id = u.id
            WHERE gm.grupo_id = 2
            ORDER BY gm.usuario_id
        ");
        
        while ($row = $res->fetch_assoc()) {
            $resultado['miembros_actuales'][] = [
                'id' => $row['usuario_id'],
                'usuario' => $row['usuario'],
                'es_admin' => $row['es_admin']
            ];
        }
        
        echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
