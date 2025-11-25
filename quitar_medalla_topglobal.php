<?php
// Script para eliminar medallas de Top Global otorgadas prematuramente

$conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "DELETE um FROM usuario_medallas um INNER JOIN medallas m ON um.medalla_id = m.id WHERE m.codigo = 'top_global'";

if ($conexion->query($sql)) {
    echo "✅ Medalla Top Global eliminada de todos los usuarios\n";
    echo "Afectadas: " . $conexion->affected_rows . " medallas\n";
} else {
    echo "❌ Error: " . $conexion->error . "\n";
}

$conexion->close();
