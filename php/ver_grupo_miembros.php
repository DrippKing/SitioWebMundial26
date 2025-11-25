<?php
$c = new mysqli('localhost', 'root', '', 'poi_database', 3307);
$r = $c->query('DESCRIBE grupo_miembros');
echo "Estructura de tabla grupo_miembros:\n";
while($row = $r->fetch_assoc()) { 
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
