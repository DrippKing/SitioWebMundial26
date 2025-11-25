<?php
// Script para verificar y validar la estructura de base de datos para LMEADOS

header('Content-Type: application/json; charset=utf-8');

try {
    $conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    $conexion->set_charset('utf8mb4');
    
    $diagnostico = [];
    
    // 1. Verificar tabla grupos
    $res = $conexion->query("DESCRIBE grupos");
    $diagnostico['tabla_grupos'] = $res ? 'OK' : 'FALTA';
    
    // 2. Verificar tabla grupo_miembros
    $res = $conexion->query("DESCRIBE grupo_miembros");
    $diagnostico['tabla_grupo_miembros'] = $res ? 'OK' : 'FALTA';
    
    // 3. Verificar tabla mensajes_grupo
    $res = $conexion->query("DESCRIBE mensajes_grupo");
    if ($res) {
        $columns = [];
        while ($col = $res->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        $diagnostico['tabla_mensajes_grupo'] = 'OK';
        $diagnostico['campos_mensajes_grupo'] = $columns;
        
        // Verificar que tenga is_encrypted
        $diagnostico['tiene_is_encrypted'] = in_array('is_encrypted', $columns) ? 'SÍ' : 'NO - FALTA AGREGAR';
    } else {
        $diagnostico['tabla_mensajes_grupo'] = 'FALTA';
    }
    
    // 4. Verificar grupo LMEADOS
    $res = $conexion->query("SELECT id, nombre FROM grupos WHERE nombre = 'LMEADOS'");
    if ($res && $res->num_rows > 0) {
        $grupo = $res->fetch_assoc();
        $diagnostico['grupo_LMEADOS'] = [
            'existe' => true,
            'id' => $grupo['id'],
            'nombre' => $grupo['nombre']
        ];
        
        // Contar miembros
        $res_miembros = $conexion->query("SELECT COUNT(*) as total FROM grupo_miembros WHERE grupo_id = " . $grupo['id']);
        $miembros = $res_miembros->fetch_assoc();
        $diagnostico['grupo_LMEADOS']['total_miembros'] = $miembros['total'];
        
        // Listar miembros
        $res_list = $conexion->query("
            SELECT u.id, u.usuario, gm.es_admin 
            FROM grupo_miembros gm
            JOIN usuarios u ON gm.usuario_id = u.id
            WHERE gm.grupo_id = " . $grupo['id']
        );
        $diagnostico['grupo_LMEADOS']['miembros'] = [];
        while ($member = $res_list->fetch_assoc()) {
            $diagnostico['grupo_LMEADOS']['miembros'][] = $member;
        }
    } else {
        $diagnostico['grupo_LMEADOS'] = ['existe' => false, 'id' => null];
    }
    
    // 5. Verificar si existen usuarios
    $res_usuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
    $usuarios_total = $res_usuarios->fetch_assoc();
    $diagnostico['total_usuarios'] = $usuarios_total['total'];
    
    // 6. Verificar mensajes en grupo LMEADOS
    if ($diagnostico['grupo_LMEADOS']['existe']) {
        $lmeados_id = $diagnostico['grupo_LMEADOS']['id'];
        $res_msgs = $conexion->query("SELECT COUNT(*) as total FROM mensajes_grupo WHERE grupo_id = $lmeados_id");
        $msgs = $res_msgs->fetch_assoc();
        $diagnostico['mensajes_en_LMEADOS'] = $msgs['total'];
    }
    
    echo json_encode($diagnostico, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $conexion->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}
?>
