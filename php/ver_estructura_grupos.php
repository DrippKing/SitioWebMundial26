<?php
$c = new mysqli('localhost', 'root', '', 'poi_database', 3307); // Asumiendo conexión local para un script de debug
$r = $c->query('DESCRIBE grupos');
echo "Estructura de tabla grupos:\n";
while($row = $r->fetch_assoc()) { 
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
