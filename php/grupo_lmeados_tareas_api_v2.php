<?php
header('Content-Type: application/json; charset=utf-8');

try {
    session_start();
    
    $conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    $conexion->set_charset('utf8mb4');
    
    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("No autorizado: sesión no iniciada");
    }
    
    $user_id = intval($_SESSION['user_id']);
    $grupo_id = 2; // ID del grupo LMEADOS (CORRECTO)
    
    // VALIDACIÓN: Verificar que el usuario sea miembro de LMEADOS
    $validar = $conexion->prepare("
        SELECT COUNT(*) as es_miembro 
        FROM grupo_miembros 
        WHERE grupo_id = ? AND usuario_id = ?
    ");
    $validar->bind_param("ii", $grupo_id, $user_id);
    $validar->execute();
    $resultado_validar = $validar->get_result();
    $row = $resultado_validar->fetch_assoc();
    
    if ($row['es_miembro'] == 0) {
        // El usuario NO es miembro, no puede ver las tareas
        echo json_encode([
            'error' => 'No autorizado',
            'mensaje' => 'No eres miembro del grupo LMEADOS',
            'miembro' => false
        ]);
        $validar->close();
        $conexion->close();
        exit;
    }
    
    $validar->close();
    
    // Si llegó aquí, el usuario ES miembro
    $miembros = [];
    
    // Obtener miembros del grupo
    $res = $conexion->query("SELECT usuario_id FROM grupo_miembros WHERE grupo_id = $grupo_id");
    if (!$res) {
        throw new Exception("Error en consulta de miembros: " . $conexion->error);
    }
    
    while ($row = $res->fetch_assoc()) {
        $miembros[] = $row['usuario_id'];
    }
    
    // Si no hay miembros, retornar array vacío
    if (empty($miembros)) {
        echo json_encode([
            'miembro' => true,
            'tareas' => []
        ]);
        $conexion->close();
        exit;
    }
    
    $tareas = [
        'mensaje' => [],
        'foto' => [],
        'documento' => [],
        'ubicacion' => []
    ];
    
    // Verificar tareas de cada miembro
    foreach ($miembros as $uid) {
        // 1. ¿Ha enviado al menos 1 mensaje?
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid");
        if ($r) {
            $tareas['mensaje'][$uid] = $r->fetch_assoc()['c'] > 0;
        }
        
        // 2. ¿Ha enviado al menos 1 foto?
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.gif' OR file_url LIKE '%.webp')");
        if ($r) {
            $tareas['foto'][$uid] = $r->fetch_assoc()['c'] > 0;
        }
        
        // 3. ¿Ha enviado al menos 1 documento?
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.pdf' OR file_url LIKE '%.doc%' OR file_url LIKE '%.xls%' OR file_url LIKE '%.ppt%' OR file_url LIKE '%.txt')");
        if ($r) {
            $tareas['documento'][$uid] = $r->fetch_assoc()['c'] > 0;
        }
        
        // 4. ¿Ha enviado al menos 1 ubicación?
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND message_text LIKE '%maps.google.com%'");
        if ($r) {
            $tareas['ubicacion'][$uid] = $r->fetch_assoc()['c'] > 0;
        }
    }
    
    // Obtener nombres de usuarios
    $nombres = [];
    $ids_str = implode(',', $miembros);
    $res = $conexion->query("SELECT id, usuario FROM usuarios WHERE id IN ($ids_str)");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $nombres[$row['id']] = $row['usuario'];
        }
    }
    
    // Construir resultado
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
    
    echo json_encode([
        'miembro' => true,
        'tareas' => $resultado
    ]);
    $conexion->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage(),
        'miembro' => false
    ]);
}
?>
