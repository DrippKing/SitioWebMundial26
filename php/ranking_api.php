<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'db_connection.php';
    $conexion->set_charset('utf8mb4');

    // Obtener ranking simple
    $query = "
        SELECT 
            u.id,
            u.usuario,
            u.pais,
            u.foto_perfil,
            COALESCE(pt.puntos_totales, 0) as puntos,
            COALESCE(pt.predicciones_exactas, 0) as predicciones_exactas
        FROM usuarios u
        LEFT JOIN puntuaciones_torneo pt ON u.id = pt.usuario_id
        ORDER BY COALESCE(pt.puntos_totales, 0) DESC
    ";

    $resultado = $conexion->query($query);

    if (!$resultado) {
        throw new Exception('Error en consulta: ' . $conexion->error);
    }

    $usuarios = [];
    while ($row = $resultado->fetch_assoc()) {
        // Contar predicciones
        $pred_res = $conexion->query("SELECT COUNT(*) as total FROM predicciones WHERE usuario_id = " . $row['id']);
        $pred_row = $pred_res->fetch_assoc();
        $total_predicciones = $pred_row['total'];
        
        // Calcular precision
        $precision = 0;
        if ($total_predicciones > 0) {
            $precision = round(($row['predicciones_exactas'] / $total_predicciones) * 100, 2);
        }
        
        $usuarios[] = [
            'id' => intval($row['id']),
            'usuario' => $row['usuario'],
            'pais' => $row['pais'],
            'foto_perfil' => $row['foto_perfil'],
            'puntos' => intval($row['puntos']),
            'total_predicciones' => intval($total_predicciones),
            'precision' => floatval($precision)
        ];
    }

    echo json_encode([
        'exito' => true,
        'total_usuarios' => count($usuarios),
        'ranking' => $usuarios
    ]);

    $conexion->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
