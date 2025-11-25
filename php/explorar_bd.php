<?php
require_once 'db_connection.php';
$conexion->set_charset('utf8mb4');

echo "=== ESTRUCTURA DE PREDICCIONES (APUESTAS) ===\n";
$res = $conexion->query("DESCRIBE predicciones");
while ($row = $res->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\n=== ESTRUCTURA DE PARTIDOS ===\n";
$res = $conexion->query("DESCRIBE partidos");
while ($row = $res->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\n=== ESTRUCTURA DE PUNTUACIONES_TORNEO ===\n";
$res = $conexion->query("DESCRIBE puntuaciones_torneo");
while ($row = $res->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\n=== MUESTRA DE PREDICCIONES ===\n";
$res = $conexion->query("SELECT * FROM predicciones LIMIT 5");
if ($res->num_rows > 0) {
    echo "Encontradas " . $res->num_rows . " predicciones de ejemplo\n";
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No hay predicciones en la tabla\n";
}

echo "\n=== ESTADÍSTICAS DE USUARIOS ===\n";
$res = $conexion->query("SELECT COUNT(*) as total_usuarios FROM usuarios");
$total = $res->fetch_assoc()['total_usuarios'];
echo "Total de usuarios: $total\n";

echo "\n=== MUESTRA DE PUNTUACIONES ===\n";
$res = $conexion->query("SELECT * FROM puntuaciones_torneo LIMIT 5");
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No hay puntuaciones\n";
}

$conexion->close();
?>
