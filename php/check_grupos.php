<?php
require_once 'db_connection.php';
$conexion->set_charset('utf8mb4');

echo "=== GRUPOS EN LA BASE DE DATOS ===\n\n";

$res = $conexion->query('SELECT id, nombre, creador_id FROM grupos ORDER BY id');
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Nombre: {$row['nombre']} | Creador: {$row['creador_id']}\n";
}

$conexion->close();
?>
