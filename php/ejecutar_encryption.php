<?php
require_once 'db_connection.php';

// El archivo db_connection.php ya se encarga de la conexión y de la gestión de errores.
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
