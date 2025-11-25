<?php
require_once 'db_connection.php';
// El archivo db_connection.php ya se encarga de la conexión y de la gestión de errores.

$result = $conexion->query('SHOW TABLES');
echo "Tablas en poi_database:\n";
while ($row = $result->fetch_row()) {
    echo "- " . $row[0] . "\n";
}

$conexion->close();
?>
