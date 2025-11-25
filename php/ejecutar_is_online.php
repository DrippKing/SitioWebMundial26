<?php
// Ejecutar SQL para agregar columna is_online
$host = "drippking.com";
$usuario_db = "drippkin_Host";
$contrasena_db = "Drippking5545";
$nombre_db = "Drippkin_poi_database";
$port = 3306;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error . "\n");
}

$sql = file_get_contents('ADD_IS_ONLINE_COLUMN.sql');

if ($conexion->multi_query($sql)) {
    echo "✅ Columna is_online agregada exitosamente\n";
    
    // Limpiar resultados
    while ($conexion->more_results()) {
        $conexion->next_result();
    }
} else {
    echo "❌ Error: " . $conexion->error . "\n";
}

$conexion->close();
?>
