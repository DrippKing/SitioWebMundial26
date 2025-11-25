<?php
$conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);

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
