<?php
$conexion = new mysqli('localhost', 'root', '', 'poi_database', 3307);
$conexion->set_charset('utf8mb4');

echo "=== LIMPIANDO GRUPOS DUPLICADOS ===\n\n";

// Primero, verificar miembros de cada grupo LMEADOS
echo "1. Miembros antes de limpiar:\n";
$lmeados_ids = [2, 4, 5, 6];
foreach ($lmeados_ids as $id) {
    $res = $conexion->query("SELECT COUNT(*) as c FROM grupo_miembros WHERE grupo_id = $id");
    $count = $res->fetch_assoc()['c'];
    echo "   LMEADOS (ID: $id): $count miembros\n";
}

// Eliminar grupos duplicados manteniendo solo el ID 2
echo "\n2. Eliminando grupos duplicados...\n";
$ids_a_eliminar = [4, 5, 6];
foreach ($ids_a_eliminar as $id) {
    // Primero eliminar miembros
    $conexion->query("DELETE FROM grupo_miembros WHERE grupo_id = $id");
    echo "   - Eliminados miembros del grupo ID: $id\n";
    
    // Luego eliminar el grupo
    $conexion->query("DELETE FROM grupos WHERE id = $id");
    echo "   - Eliminado grupo ID: $id\n";
}

echo "\n3. Grupos finales:\n";
$res = $conexion->query('SELECT id, nombre, creador_id FROM grupos ORDER BY id');
while ($row = $res->fetch_assoc()) {
    echo "   ID: {$row['id']} | Nombre: {$row['nombre']} | Creador: {$row['creador_id']}\n";
}

echo "\n4. Miembros finales de LMEADOS (ID 2):\n";
$res = $conexion->query("SELECT gm.usuario_id, u.usuario FROM grupo_miembros gm 
                          JOIN usuarios u ON gm.usuario_id = u.id 
                          WHERE gm.grupo_id = 2");
while ($row = $res->fetch_assoc()) {
    echo "   - {$row['usuario']} (ID: {$row['usuario_id']})\n";
}

$conexion->close();
echo "\n✅ Limpieza completada\n";
?>
