<?php
session_start();

header('Content-Type: application/json');

$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Error de conexión a la BD: " . $conexion->connect_error]));
}
$conexion->set_charset("utf8");


$my_user_id = $_SESSION['user_id'] ?? 0; 

if ($my_user_id === 0) {
     http_response_code(401);
     die(json_encode(["error" => "Usuario no autenticado. Inicie sesión."]));
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_contacts':
        getContacts($conexion, $my_user_id);
        break;

    case 'get_messages':
        getMessages($conexion, $my_user_id);
        break;

    case 'send_message':
        sendMessage($conexion, $my_user_id);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no especificada"]);
        break;
}

$conexion->close();

function getContacts($conexion, $current_user_id) {
    $contacts = [];
    $sql = "SELECT id, usuario, foto_perfil FROM usuarios WHERE id != ? ORDER BY usuario ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $contacts[] = [
            'id' => $row['id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar_url' => "../pictures/" . htmlspecialchars($row['foto_perfil'])
        ];
    }
    $stmt->close();
    echo json_encode($contacts);
}

function getMessages($conexion, $my_user_id) {
    $contact_id = $_GET['contact_id'] ?? 0;
    if (!is_numeric($contact_id) || $contact_id <= 0) {
        echo json_encode([]);
        return;
    }

    $messages = [];
    $sql = "
        SELECT sender_id, message_text, timestamp 
        FROM mensajes 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiii", $my_user_id, $contact_id, $contact_id, $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'text' => htmlspecialchars($row['message_text']),
            'type' => ($row['sender_id'] == $my_user_id) ? 'sent' : 'received',
            'time' => $row['timestamp']
        ];
    }
    $stmt->close();
    echo json_encode($messages);
}

function sendMessage($conexion, $sender_id) {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Método no permitido"]);
        return;
    }
    
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message_text = $_POST['message_text'] ?? '';

    if (!$receiver_id || empty($message_text)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        return;
    }

    $sql = "INSERT INTO mensajes (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error de preparación: " . $conexion->error]);
        return;
    }

    $stmt->bind_param("iis", $sender_id, $receiver_id, $message_text);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Mensaje enviado"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error al ejecutar: " . $stmt->error]);
    }

    $stmt->close();
}
?>