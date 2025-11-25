<?php
$conexion = new mysqli('drippking.com', 'drippkin_Host', 'Drippking5545', 'Drippkin_poi_database', 3306);

// Obtener un mensaje encriptado de ejemplo
$result = $conexion->query("SELECT id, message_text, is_encrypted FROM mensajes WHERE is_encrypted = 1 ORDER BY id DESC LIMIT 1");

if ($row = $result->fetch_assoc()) {
    echo "=== MENSAJE ENCRIPTADO EN BD ===\n\n";
    echo "ID: {$row['id']}\n";
    echo "is_encrypted: {$row['is_encrypted']}\n";
    echo "Texto encriptado: {$row['message_text']}\n\n";
    
    $encrypted = $row['message_text'];
    
    echo "=== INTENTANDO DESENCRIPTAR ===\n\n";
    
    // Simular lo que hace JavaScript
    $unrotated = strrev($encrypted);
    echo "Después de reverse: $unrotated\n";
    
    $decoded = base64_decode($unrotated);
    echo "Después de base64_decode: $decoded\n\n";
    
    echo "=== VERIFICACIÓN ===\n";
    echo "¿Es Base64 válido? " . (base64_encode(base64_decode($unrotated)) === $unrotated ? 'SÍ' : 'NO') . "\n";
    
} else {
    echo "No hay mensajes encriptados en la BD\n";
}

$conexion->close();
?>
