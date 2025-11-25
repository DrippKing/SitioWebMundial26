<?php
require_once 'db_connection.php';

echo "=== ÚLTIMOS 5 MENSAJES ===\n\n";
$result = $conexion->query('SELECT id, sender_id, receiver_id, message_text, is_encrypted FROM mensajes ORDER BY id DESC LIMIT 5');

while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}\n";
    echo "De: {$row['sender_id']} → Para: {$row['receiver_id']}\n";
    echo "Texto: " . substr($row['message_text'], 0, 80) . "...\n";
    echo "is_encrypted: {$row['is_encrypted']}\n";
    echo "---\n";
}

$conexion->close();
?>
