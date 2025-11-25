<?php
require_once 'db_connection.php';
$conexion->set_charset('utf8mb4');

$query = "SELECT u.id, u.usuario, u.pais, COALESCE(pt.puntos_totales, 0) as puntos FROM usuarios u LEFT JOIN puntuaciones_torneo pt ON u.id = pt.usuario_id ORDER BY COALESCE(pt.puntos_totales, 0) DESC";

$resultado = $conexion->query($query);
if ($resultado) {
    echo "Usuarios encontrados:\n";
    while ($row = $resultado->fetch_assoc()) {
        echo $row['usuario'] . ': ' . $row['puntos'] . " puntos\n";
    }
} else {
    echo "Error: " . $conexion->error . "\n";
}

$conexion->close();
?>
