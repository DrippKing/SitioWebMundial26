<?php
session_start();

// Simular login como eljazmen (ID 1)
$_SESSION['user_id'] = 1;

require_once 'db_connection.php';
$conexion->set_charset('utf8mb4');

echo "=== TEST DE TAREAS LMEADOS ===\n\n";

// Verificar grupo
echo "1. Verificando grupo LMEADOS...\n";
$res = $conexion->query("SELECT * FROM grupos WHERE id = 2");
if ($row = $res->fetch_assoc()) {
    echo "   ✅ Grupo encontrado: {$row['nombre']} (ID: {$row['id']})\n";
} else {
    echo "   ❌ Grupo no encontrado\n";
}

// Verificar miembros
echo "\n2. Miembros del grupo...\n";
$res = $conexion->query("SELECT gm.usuario_id, u.usuario FROM grupo_miembros gm JOIN usuarios u ON gm.usuario_id = u.id WHERE gm.grupo_id = 2");
$miembros = [];
while ($row = $res->fetch_assoc()) {
    $miembros[] = $row['usuario_id'];
    echo "   - {$row['usuario']} (ID: {$row['usuario_id']})\n";
}

// Verificar sesión actual
echo "\n3. Usuario en sesión:\n";
echo "   - ID: {$_SESSION['user_id']}\n";
$res = $conexion->query("SELECT usuario FROM usuarios WHERE id = {$_SESSION['user_id']}");
if ($row = $res->fetch_assoc()) {
    echo "   - Username: {$row['usuario']}\n";
}

// Verificar si es miembro
echo "\n4. ¿Es miembro de LMEADOS?\n";
$es_miembro = in_array($_SESSION['user_id'], $miembros);
echo "   - " . ($es_miembro ? "✅ SÍ" : "❌ NO") . "\n";

// Simular llamada al API
echo "\n5. Simulando API grupo_lmeados_tareas_api_v2.php...\n";

$validar = $conexion->prepare("
    SELECT COUNT(*) as es_miembro 
    FROM grupo_miembros 
    WHERE grupo_id = 2 AND usuario_id = ?
");
$validar->bind_param("i", $_SESSION['user_id']);
$validar->execute();
$resultado_validar = $validar->get_result();
$row = $resultado_validar->fetch_assoc();

if ($row['es_miembro'] == 0) {
    echo "   ❌ No es miembro\n";
} else {
    echo "   ✅ Es miembro\n";
    
    // Obtener tareas
    echo "\n6. Estado de tareas:\n";
    
    foreach ($miembros as $uid) {
        $res = $conexion->query("SELECT usuario FROM usuarios WHERE id = $uid");
        $usuario = $res->fetch_assoc()['usuario'];
        
        // Mensaje
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = 2 AND sender_id = $uid");
        $msg = $r->fetch_assoc()['c'] > 0 ? "✅" : "⏳";
        
        // Foto
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = 2 AND sender_id = $uid AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.gif' OR file_url LIKE '%.webp')");
        $foto = $r->fetch_assoc()['c'] > 0 ? "✅" : "⏳";
        
        // Documento
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = 2 AND sender_id = $uid AND (file_url LIKE '%.pdf' OR file_url LIKE '%.doc%' OR file_url LIKE '%.xls%' OR file_url LIKE '%.ppt%' OR file_url LIKE '%.txt')");
        $doc = $r->fetch_assoc()['c'] > 0 ? "✅" : "⏳";
        
        // Ubicación
        $r = $conexion->query("SELECT COUNT(*) as c FROM mensajes_grupo WHERE grupo_id = 2 AND sender_id = $uid AND message_text LIKE '%maps.google.com%'");
        $ubi = $r->fetch_assoc()['c'] > 0 ? "✅" : "⏳";
        
        echo "   $usuario: Mensaje($msg) Foto($foto) Documento($doc) Ubicación($ubi)\n";
    }
}

$conexion->close();
?>
