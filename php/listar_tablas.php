<?php
$conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$result = $conexion->query('SHOW TABLES');
echo "Tablas en poi_database:\n";
while ($row = $result->fetch_row()) {
    echo "- " . $row[0] . "\n";
}

$conexion->close();
?>
