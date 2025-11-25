<?php
// Ejecutar SQL para agregar columna is_online
require_once 'db_connection.php';

// El archivo db_connection.php ya se encarga de la conexión y de la gestión de errores.
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
