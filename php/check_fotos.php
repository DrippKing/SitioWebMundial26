<?php
$conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
$conexion->set_charset('utf8mb4');

echo "=== FOTOS DE PERFIL EN BD ===\n\n";
$res = $conexion->query("SELECT id, usuario, foto_perfil FROM usuarios");
while ($row = $res->fetch_assoc()) {
    echo "Usuario: {$row['usuario']}\n";
    echo "  Foto: {$row['foto_perfil']}\n";
    echo "\n";
}

$conexion->close();
?>
