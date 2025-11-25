<?php
require_once 'db_connection.php';


echo "📝 Creando grupo LMEADOS...\n\n";

// Leer el archivo SQL
$sql = file_get_contents('CREAR_GRUPO_LMEADOS.sql');

// Ejecutar comando por comando
if ($conexion->multi_query($sql)) {
    do {
        // Obtener resultados
        if ($result = $conexion->store_result()) {
            echo "✅ Grupo creado exitosamente!\n\n";
            echo "👥 Miembros del grupo:\n";
            while ($row = $result->fetch_assoc()) {
                echo "   ID: {$row['id']} | Nombre: {$row['nombre']} | Descripción: {$row['descripcion']}\n";
                echo "   Miembros: {$row['miembros']}\n";
            }
            $result->free();
        }
        
        // Siguiente resultado
        if ($conexion->more_results()) {
            $conexion->next_result();
        }
    } while ($conexion->more_results());
} else {
    echo "❌ Error: " . $conexion->error . "\n";
}

$conexion->close();
echo "\n✨ Proceso completado!\n";
?>
