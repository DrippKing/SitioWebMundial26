<?php
/**
 * Script de Prueba del Sistema de Chat
 * Ejecuta este archivo para verificar la configuración
 */

session_start();

$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

echo "<h1>🧪 Test del Sistema de Chat</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .box { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
</style>";

// Test 1: Conexión a la base de datos
echo "<div class='box'>";
echo "<h2>1️⃣ Prueba de Conexión a la Base de Datos</h2>";
$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    echo "<p class='error'>❌ Error: " . $conexion->connect_error . "</p>";
    die();
} else {
    echo "<p class='success'>✅ Conexión exitosa a la base de datos '{$nombre_db}' en puerto {$port}</p>";
}
$conexion->set_charset("utf8");
echo "</div>";

// Test 2: Verificar tabla usuarios
echo "<div class='box'>";
echo "<h2>2️⃣ Verificar Tabla de Usuarios</h2>";
$result = $conexion->query("SHOW TABLES LIKE 'usuarios'");
if ($result->num_rows > 0) {
    echo "<p class='success'>✅ Tabla 'usuarios' existe</p>";
    
    // Contar usuarios
    $count_result = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
    $count = $count_result->fetch_assoc()['total'];
    echo "<p class='info'>📊 Total de usuarios registrados: {$count}</p>";
    
    // Mostrar algunos usuarios
    $users_result = $conexion->query("SELECT id, usuario, foto_perfil FROM usuarios LIMIT 5");
    echo "<p><strong>Usuarios de ejemplo:</strong></p><ul>";
    while ($user = $users_result->fetch_assoc()) {
        echo "<li>ID: {$user['id']} - Usuario: {$user['usuario']} - Foto: {$user['foto_perfil']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ La tabla 'usuarios' no existe</p>";
}
echo "</div>";

// Test 3: Verificar tabla mensajes
echo "<div class='box'>";
echo "<h2>3️⃣ Verificar Tabla de Mensajes</h2>";
$result = $conexion->query("SHOW TABLES LIKE 'mensajes'");
if ($result->num_rows > 0) {
    echo "<p class='success'>✅ Tabla 'mensajes' existe</p>";
    
    // Contar mensajes
    $count_result = $conexion->query("SELECT COUNT(*) as total FROM mensajes");
    $count = $count_result->fetch_assoc()['total'];
    echo "<p class='info'>📊 Total de mensajes: {$count}</p>";
    
    // Verificar estructura
    $structure = $conexion->query("DESCRIBE mensajes");
    echo "<p><strong>Estructura de la tabla:</strong></p><ul>";
    while ($col = $structure->fetch_assoc()) {
        echo "<li>{$col['Field']} - {$col['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ La tabla 'mensajes' NO existe</p>";
    echo "<p class='info'>💡 Debes crear la tabla ejecutando el archivo <strong>TABLE MENSAJES.sql</strong> en phpMyAdmin</p>";
}
echo "</div>";

// Test 4: Verificar sesión
echo "<div class='box'>";
echo "<h2>4️⃣ Verificar Sesión Activa</h2>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "<p class='success'>✅ Sesión activa</p>";
    echo "<p class='info'>👤 User ID: {$user_id}</p>";
    
    // Obtener info del usuario
    $stmt = $conexion->prepare("SELECT usuario, foto_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        echo "<p class='info'>👤 Usuario: {$user['usuario']}</p>";
        echo "<p class='info'>📷 Avatar: {$user['foto_perfil']}</p>";
    }
    $stmt->close();
} else {
    echo "<p class='error'>❌ No hay sesión activa</p>";
    echo "<p class='info'>💡 Debes iniciar sesión primero en <a href='../html/login.html'>login.html</a></p>";
}
echo "</div>";

// Test 5: Verificar archivos
echo "<div class='box'>";
echo "<h2>5️⃣ Verificar Archivos del Chat</h2>";
$files_to_check = [
    '../html/chats.html' => 'HTML del Chat',
    '../css/chats.css' => 'Estilos del Chat',
    '../js/chat.js' => 'Lógica JavaScript',
    'msg.php' => 'API del Chat',
    '../pictures/default.jpg' => 'Avatar por defecto'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ {$description} ({$file})</p>";
    } else {
        echo "<p class='error'>❌ {$description} ({$file}) - No encontrado</p>";
    }
}
echo "</div>";

// Test 6: Simular petición de contactos (si hay sesión)
if (isset($_SESSION['user_id'])) {
    echo "<div class='box'>";
    echo "<h2>6️⃣ Simular Carga de Contactos</h2>";
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, usuario, foto_perfil FROM usuarios WHERE id != ? ORDER BY usuario ASC LIMIT 5";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<p class='info'>📋 Tus contactos disponibles:</p><ul>";
    while ($contact = $result->fetch_assoc()) {
        echo "<li>ID: {$contact['id']} - {$contact['usuario']}</li>";
    }
    echo "</ul>";
    $stmt->close();
    echo "</div>";
}

// Resumen final
echo "<div class='box' style='background: #e8f5e9; border-left: 4px solid green;'>";
echo "<h2>📋 Resumen</h2>";

$all_ok = true;
$issues = [];

// Verificar cada requisito
if ($conexion->connect_error) {
    $all_ok = false;
    $issues[] = "Problemas de conexión a la base de datos";
}

$result = $conexion->query("SHOW TABLES LIKE 'mensajes'");
if ($result->num_rows == 0) {
    $all_ok = false;
    $issues[] = "Falta crear la tabla 'mensajes'";
}

if (!isset($_SESSION['user_id'])) {
    $all_ok = false;
    $issues[] = "No hay sesión activa - necesitas hacer login";
}

if ($all_ok) {
    echo "<p class='success' style='font-size: 1.2em;'>🎉 ¡Todo está listo! El sistema de chat está configurado correctamente.</p>";
    echo "<p><a href='../html/chats.html' style='background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🚀 Ir al Chat</a></p>";
} else {
    echo "<p class='error'>⚠️ Hay algunos problemas que resolver:</p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li class='error'>{$issue}</li>";
    }
    echo "</ul>";
}
echo "</div>";

$conexion->close();

echo "<br><p style='text-align: center; color: #999; font-size: 0.9em;'>
    💡 Para más información, consulta <strong>INSTRUCCIONES_CHAT.md</strong>
</p>";
?>
