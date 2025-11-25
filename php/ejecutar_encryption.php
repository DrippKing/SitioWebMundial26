<?php
$conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = file_get_contents('ADD_ENCRYPTION_COLUMN.sql');

if ($conexion->multi_query($sql)) {
    do {
        if ($result = $conexion->store_result()) {
            $result->free();
        }
    } while ($conexion->next_result());
    
    echo "✅ Columnas is_encrypted agregadas exitosamente a messages y mensajes_grupo\n";
} else {
    echo "❌ Error: " . $conexion->error . "\n";
}

$conexion->close();
?>
