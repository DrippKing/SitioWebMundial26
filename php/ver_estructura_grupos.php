<?php
$c = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);
$r = $c->query('DESCRIBE grupos');
echo "Estructura de tabla grupos:\n";
while($row = $r->fetch_assoc()) { 
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
