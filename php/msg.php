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
    
    case 'mark_as_read':
        markAsRead($conexion, $my_user_id);
        break;
    
    case 'get_groups':
        getGroups($conexion, $my_user_id);
        break;
    
    case 'get_group_messages':
        getGroupMessages($conexion, $my_user_id);
        break;
    
    case 'send_group_message':
        sendGroupMessage($conexion, $my_user_id);
        break;
    
    case 'upload_file':
        uploadFile($conexion, $my_user_id);
        break;
    
    case 'search_messages':
        searchMessages($conexion, $my_user_id);
        break;
    
    case 'set_typing':
        setTypingStatus($conexion, $my_user_id);
        break;
    
    case 'get_typing':
        getTypingStatus($conexion, $my_user_id);
        break;
    
    case 'get_unread_count':
        getUnreadCount($conexion, $my_user_id);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no especificada"]);
        break;
}

$conexion->close();

function getContacts($conexion, $current_user_id) {
    // Obtener datos del usuario actual
    $current_user = [];
    $sql_current = "SELECT id, usuario, foto_perfil FROM usuarios WHERE id = ?";
    $stmt_current = $conexion->prepare($sql_current);
    $stmt_current->bind_param("i", $current_user_id);
    $stmt_current->execute();
    $result_current = $stmt_current->get_result();
    
    if ($row_current = $result_current->fetch_assoc()) {
        $current_user = [
            'id' => $row_current['id'],
            'username' => htmlspecialchars($row_current['usuario']),
            'avatar_url' => "../pictures/" . htmlspecialchars($row_current['foto_perfil'])
        ];
    }
    $stmt_current->close();
    
    // Obtener lista de amigos (solo amigos, no todos los usuarios)
    $contacts = [];
    $sql = "
        SELECT u.id, u.usuario, u.foto_perfil, u.is_online,
               CASE 
                   WHEN u.is_online = 1 THEN 'online'
                   ELSE 'offline'
               END as status
        FROM friends f
        INNER JOIN usuarios u ON f.friend_id = u.id
        WHERE f.user_id = ?
        ORDER BY status DESC, u.usuario ASC
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $contacts[] = [
            'id' => $row['id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar_url' => "../pictures/" . htmlspecialchars($row['foto_perfil']),
            'status' => $row['status']
        ];
    }
    $stmt->close();
    
    // Devolver tanto el usuario actual como los contactos
    echo json_encode([
        'current_user' => $current_user,
        'contacts' => $contacts
    ]);
}

function getMessages($conexion, $my_user_id) {
    $contact_id = $_GET['contact_id'] ?? 0;
    if (!is_numeric($contact_id) || $contact_id <= 0) {
        echo json_encode([]);
        return;
    }

    $messages = [];
    $sql = "
        SELECT sender_id, message_text, timestamp, is_read, message_type, file_url, is_encrypted
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
            'text' => $row['is_encrypted'] ? $row['message_text'] : htmlspecialchars($row['message_text']),
            'type' => ($row['sender_id'] == $my_user_id) ? 'sent' : 'received',
            'time' => $row['timestamp'],
            'is_read' => $row['is_read'],
            'message_type' => $row['message_type'],
            'file_url' => $row['file_url'] ? "../uploads/" . htmlspecialchars($row['file_url']) : null,
            'is_encrypted' => $row['is_encrypted']
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
    $file_url = $_POST['file_url'] ?? null;
    $message_type = $_POST['message_type'] ?? 'text';
    $is_encrypted = $_POST['is_encrypted'] ?? '0';

    if (!$receiver_id || empty($message_text)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        return;
    }

    $sql = "INSERT INTO mensajes (sender_id, receiver_id, message_text, file_url, message_type, is_encrypted) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error de preparación: " . $conexion->error]);
        return;
    }

    $stmt->bind_param("iisssi", $sender_id, $receiver_id, $message_text, $file_url, $message_type, $is_encrypted);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Mensaje enviado"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error al ejecutar: " . $stmt->error]);
    }

    $stmt->close();
}

// ==========================================
// NUEVAS FUNCIONALIDADES
// ==========================================

// Marcar mensajes como leídos
function markAsRead($conexion, $my_user_id) {
    $contact_id = $_POST['contact_id'] ?? 0;
    
    if (!is_numeric($contact_id) || $contact_id <= 0) {
        echo json_encode(["success" => false, "error" => "ID inválido"]);
        return;
    }
    
    $sql = "UPDATE mensajes SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $contact_id, $my_user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
}

// Obtener grupos del usuario
function getGroups($conexion, $my_user_id) {
    $groups = [];
    
    $sql = "
        SELECT g.id, g.nombre, g.descripcion, g.foto_grupo, 
               COUNT(DISTINCT gm.usuario_id) as miembros_count,
               (SELECT COUNT(*) FROM mensajes_grupo mg 
                WHERE mg.grupo_id = g.id AND mg.sender_id != ? 
                AND mg.is_read = 0) as unread_count
        FROM grupos g
        INNER JOIN grupo_miembros gm ON g.id = gm.grupo_id
        WHERE gm.usuario_id = ?
        GROUP BY g.id
        ORDER BY g.nombre ASC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $my_user_id, $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $groups[] = [
            'id' => $row['id'],
            'name' => htmlspecialchars($row['nombre']),
            'description' => htmlspecialchars($row['descripcion'] ?? ''),
            'avatar_url' => "../pictures/" . htmlspecialchars($row['foto_grupo']),
            'members_count' => $row['miembros_count'],
            'unread_count' => $row['unread_count'],
            'type' => 'group'
        ];
    }
    
    $stmt->close();
    echo json_encode($groups);
}

// Obtener mensajes de grupo
function getGroupMessages($conexion, $my_user_id) {
    $group_id = $_GET['group_id'] ?? 0;
    
    if (!is_numeric($group_id) || $group_id <= 0) {
        echo json_encode([]);
        return;
    }
    
    $messages = [];
    $sql = "
        SELECT mg.sender_id, mg.message_text, mg.timestamp, mg.message_type, mg.file_url, mg.is_encrypted,
               u.usuario, u.foto_perfil
        FROM mensajes_grupo mg
        INNER JOIN usuarios u ON mg.sender_id = u.id
        WHERE mg.grupo_id = ?
        ORDER BY mg.timestamp ASC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'text' => $row['is_encrypted'] ? $row['message_text'] : htmlspecialchars($row['message_text']),
            'type' => ($row['sender_id'] == $my_user_id) ? 'sent' : 'received',
            'time' => $row['timestamp'],
            'sender_name' => htmlspecialchars($row['usuario']),
            'sender_avatar' => "../pictures/" . htmlspecialchars($row['foto_perfil']),
            'message_type' => $row['message_type'],
            'file_url' => $row['file_url'] ? "../uploads/" . htmlspecialchars($row['file_url']) : null,
            'is_encrypted' => $row['is_encrypted']
        ];
    }
    
    $stmt->close();
    echo json_encode($messages);
}

// Enviar mensaje a grupo
function sendGroupMessage($conexion, $sender_id) {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Método no permitido"]);
        return;
    }
    
    $group_id = $_POST['group_id'] ?? null;
    $message_text = $_POST['message_text'] ?? '';
    $file_url = $_POST['file_url'] ?? null;
    $message_type = $_POST['message_type'] ?? 'text';
    $is_encrypted = $_POST['is_encrypted'] ?? '0';
    
    if (!$group_id || empty($message_text)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        return;
    }
    
    $sql = "INSERT INTO mensajes_grupo (grupo_id, sender_id, message_text, file_url, message_type, is_encrypted) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iisssi", $group_id, $sender_id, $message_text, $file_url, $message_type, $is_encrypted);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Mensaje enviado"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    
    $stmt->close();
}

// Subir archivos
function uploadFile($conexion, $user_id) {
    if (!isset($_FILES['file'])) {
        echo json_encode(["success" => false, "error" => "No se envió archivo"]);
        return;
    }
    
    $upload_dir = "../uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['file'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
    
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        echo json_encode(["success" => false, "error" => "Tipo de archivo no permitido"]);
        return;
    }
    
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo json_encode([
            "success" => true, 
            "filename" => $new_filename,
            "url" => "../uploads/" . $new_filename
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al subir archivo"]);
    }
}

// Buscar mensajes
function searchMessages($conexion, $my_user_id) {
    $query = $_GET['query'] ?? '';
    $contact_id = $_GET['contact_id'] ?? 0;
    
    if (empty($query)) {
        echo json_encode([]);
        return;
    }
    
    $messages = [];
    $search_term = "%{$query}%";
    
    $sql = "
        SELECT sender_id, message_text, timestamp 
        FROM mensajes 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND message_text LIKE ?
        ORDER BY timestamp DESC
        LIMIT 50
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiiis", $my_user_id, $contact_id, $contact_id, $my_user_id, $search_term);
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

// Establecer estado de "escribiendo"
function setTypingStatus($conexion, $user_id) {
    $chat_id = $_POST['chat_id'] ?? 0;
    $chat_type = $_POST['chat_type'] ?? 'private';
    $is_typing = $_POST['is_typing'] ?? 0;
    
    // Verificar si la tabla existe
    $table_check = $conexion->query("SHOW TABLES LIKE 'typing_status'");
    if ($table_check->num_rows == 0) {
        // Crear tabla si no existe
        $create_table = "
            CREATE TABLE typing_status (
                id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                chat_id INT(11) NOT NULL,
                chat_type ENUM('private', 'group') DEFAULT 'private',
                is_typing TINYINT(1) DEFAULT 0,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                UNIQUE KEY unique_typing (user_id, chat_id, chat_type)
            )
        ";
        $conexion->query($create_table);
    }
    
    $sql = "
        INSERT INTO typing_status (user_id, chat_id, chat_type, is_typing) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE is_typing = ?, last_updated = CURRENT_TIMESTAMP
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iisii", $user_id, $chat_id, $chat_type, $is_typing, $is_typing);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    
    $stmt->close();
}

// Obtener estado de "escribiendo"
function getTypingStatus($conexion, $my_user_id) {
    $chat_id = $_GET['chat_id'] ?? 0;
    $chat_type = $_GET['chat_type'] ?? 'private';
    
    // Verificar si la tabla existe
    $table_check = $conexion->query("SHOW TABLES LIKE 'typing_status'");
    if ($table_check->num_rows == 0) {
        echo json_encode(["is_typing" => false, "users" => []]);
        return;
    }
    
    $sql = "
        SELECT u.usuario, ts.is_typing 
        FROM typing_status ts
        INNER JOIN usuarios u ON ts.user_id = u.id
        WHERE ts.chat_id = ? AND ts.chat_type = ? AND ts.user_id != ?
        AND ts.is_typing = 1
        AND ts.last_updated >= DATE_SUB(NOW(), INTERVAL 5 SECOND)
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isi", $chat_id, $chat_type, $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $typing_users = [];
    while ($row = $result->fetch_assoc()) {
        $typing_users[] = htmlspecialchars($row['usuario']);
    }
    
    $stmt->close();
    echo json_encode([
        "is_typing" => count($typing_users) > 0,
        "users" => $typing_users
    ]);
}

// Obtener contador de mensajes no leídos
function getUnreadCount($conexion, $my_user_id) {
    $counts = [];
    
    // Mensajes privados no leídos
    $sql = "
        SELECT sender_id, COUNT(*) as unread
        FROM mensajes
        WHERE receiver_id = ? AND is_read = 0
        GROUP BY sender_id
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $counts['contact_' . $row['sender_id']] = $row['unread'];
    }
    
    $stmt->close();
    echo json_encode($counts);
}
?>
