<?php
session_start();
$_SESSION['user_id'] = 1; // Simular usuario logueado

$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);
$conexion->set_charset("utf8");

$contact_id = 6; // ID del contacto de prueba
$my_user_id = 1;

$messages = [];
$sql = "
    SELECT sender_id, message_text, timestamp, is_read, message_type, file_url, is_encrypted
    FROM mensajes 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY timestamp DESC
    LIMIT 3
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiii", $my_user_id, $contact_id, $contact_id, $my_user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "=== JSON QUE RECIBE EL FRONTEND ===\n\n";

while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'text' => $row['is_encrypted'] ? $row['message_text'] : htmlspecialchars($row['message_text']),
        'type' => ($row['sender_id'] == $my_user_id) ? 'sent' : 'received',
        'time' => $row['timestamp'],
        'is_read' => $row['is_read'],
        'message_type' => $row['message_type'],
        'file_url' => $row['file_url'] ? "../uploads/" . htmlspecialchars($row['file_url']) : null,
        'is_encrypted' => $row['is_encrypted']
    ];
}

echo json_encode($messages, JSON_PRETTY_PRINT);

$stmt->close();
$conexion->close();
?>
