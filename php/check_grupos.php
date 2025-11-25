<?php
$conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
$conexion->set_charset('utf8mb4');

echo "=== GRUPOS EN LA BASE DE DATOS ===\n\n";

$res = $conexion->query('SELECT id, nombre, creador_id FROM grupos ORDER BY id');
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Nombre: {$row['nombre']} | Creador: {$row['creador_id']}\n";
}

$conexion->close();
?>
