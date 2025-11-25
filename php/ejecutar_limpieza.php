<?php
require_once 'db_connection.php';


echo "🧹 Limpiando usuarios de prueba...\n\n";

// Leer el archivo SQL
$sql = file_get_contents('LIMPIAR_USUARIOS_PRUEBA.sql');

// Separar por comandos (punto y coma)
$comandos = explode(';', $sql);

$exitosos = 0;
$errores = 0;

foreach ($comandos as $comando) {
    $comando = trim($comando);
    
    // Ignorar comentarios y líneas vacías
    if (empty($comando) || substr($comando, 0, 2) === '--') {
        continue;
    }
    
    if ($conexion->query($comando)) {
        $exitosos++;
        if ($conexion->affected_rows > 0) {
            echo "✅ Ejecutado: " . substr($comando, 0, 50) . "... ({$conexion->affected_rows} filas afectadas)\n";
        }
    } else {
        $errores++;
        echo "❌ Error: " . $conexion->error . "\n";
        echo "   Comando: " . substr($comando, 0, 100) . "...\n";
    }
}

echo "\n📊 Resumen:\n";
echo "   Comandos exitosos: $exitosos\n";
echo "   Errores: $errores\n";

// Mostrar usuarios restantes
echo "\n👥 Usuarios restantes en la base de datos:\n";
$result = $conexion->query("SELECT id, usuario, email, nombre FROM usuarios");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: {$row['id']} | Usuario: {$row['usuario']} | Nombre: {$row['nombre']} | Email: {$row['email']}\n";
    }
} else {
    echo "   ⚠️ No hay usuarios en la base de datos\n";
}

$conexion->close();
echo "\n✨ Limpieza completada!\n";
?>
