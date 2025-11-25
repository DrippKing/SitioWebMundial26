<?php
header('Content-Type: application/json; charset=utf-8');

// Test simple sin requerir sesión - simula que el usuario es eljazmen (ID 1)

$test_user_id = 1; // eljazmen

$conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

$grupo_id = 2; // LMEADOS

// PASO 1: Verificar que el usuario es miembro
$validar = $conexion->prepare("
    SELECT COUNT(*) as es_miembro 
    FROM grupo_miembros 
    WHERE grupo_id = ? AND usuario_id = ?
");
$validar->bind_param("ii", $grupo_id, $test_user_id);
$validar->execute();
$resultado_validar = $validar->get_result();
$row = $resultado_validar->fetch_assoc();

$es_miembro = $row['es_miembro'] > 0;

echo json_encode([
    'paso_1_validacion' => [
        'es_miembro' => $es_miembro,
        'grupo_id' => $grupo_id,
        'usuario_id' => $test_user_id
    ]
], JSON_PRETTY_PRINT);

if (!$es_miembro) {
    $conexion->close();
    exit;
}

// PASO 2: Obtener miembros
$miembros = [];
$res = $conexion->query("SELECT usuario_id FROM grupo_miembros WHERE grupo_id = $grupo_id");

while ($row = $res->fetch_assoc()) {
    $miembros[] = $row['usuario_id'];
}

echo json_encode([
    'paso_2_miembros' => $miembros
], JSON_PRETTY_PRINT);

if (empty($miembros)) {
    $conexion->close();
    exit;
}

// PASO 3: Verificar tareas
$tareas = [];
foreach ($miembros as $uid) {
    $tareas[$uid] = [
        'mensaje' => 0,
        'foto' => 0,
        'documento' => 0,
        'ubicacion' => 0
    ];
    
    // Mensaje
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid");
    $tareas[$uid]['mensaje'] = $r->fetch_assoc()['c'] > 0 ? true : false;
    
    // Foto
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.gif' OR file_url LIKE '%.webp')");
    $tareas[$uid]['foto'] = $r->fetch_assoc()['c'] > 0 ? true : false;
    
    // Documento
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND (file_url LIKE '%.pdf' OR file_url LIKE '%.doc%' OR file_url LIKE '%.xls%' OR file_url LIKE '%.ppt%' OR file_url LIKE '%.txt')");
    $tareas[$uid]['documento'] = $r->fetch_assoc()['c'] > 0 ? true : false;
    
    // Ubicación
    $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = $grupo_id AND sender_id = $uid AND message_text LIKE '%maps.google.com%'");
    $tareas[$uid]['ubicacion'] = $r->fetch_assoc()['c'] > 0 ? true : false;
}

echo json_encode([
    'paso_3_tareas' => $tareas
], JSON_PRETTY_PRINT);

// PASO 4: Obtener nombres
$nombres = [];
$ids_str = implode(',', $miembros);
$res = $conexion->query("SELECT id, usuario FROM usuarios WHERE id IN ($ids_str)");
while ($row = $res->fetch_assoc()) {
    $nombres[$row['id']] = $row['usuario'];
}

echo json_encode([
    'paso_4_nombres' => $nombres
], JSON_PRETTY_PRINT);

// PASO 5: Resultado final
$resultado = [];
foreach ($miembros as $uid) {
    $resultado[] = [
        'usuario' => $nombres[$uid] ?? 'Usuario ' . $uid,
        'mensaje' => $tareas[$uid]['mensaje'],
        'foto' => $tareas[$uid]['foto'],
        'documento' => $tareas[$uid]['documento'],
        'ubicacion' => $tareas[$uid]['ubicacion']
    ];
}

echo json_encode([
    'paso_5_resultado_final' => $resultado
], JSON_PRETTY_PRINT);

$conexion->close();
?>
