<?php
session_start();
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit();
}

$my_user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'search_users':
        searchUsers($conexion, $my_user_id);
        break;
    case 'send_request':
        sendFriendRequest($conexion, $my_user_id);
        break;
    case 'get_requests':
        getFriendRequests($conexion, $my_user_id);
        break;
    case 'respond_request':
        respondToRequest($conexion, $my_user_id);
        break;
    case 'get_friends':
        getFriends($conexion, $my_user_id);
        break;
    case 'remove_friend':
        removeFriend($conexion, $my_user_id);
        break;
    case 'get_user_info':
        getUserInfo($conexion, $my_user_id);
        break;
    default:
        echo json_encode(["error" => "Acción no válida"]);
}

$conexion->close();

// ========================================
// FUNCIONES
// ========================================

// Buscar usuarios
function searchUsers($conexion, $my_user_id) {
    $query = $_GET['query'] ?? '';
    
    // Si query está vacío, buscar todos los usuarios
    if (empty($query)) {
        $search_term = "%"; // Traer todos
    } else {
        $search_term = "%{$query}%";
    }
    
    // Buscar usuarios excluyendo: yo mismo, mis amigos actuales, solicitudes pendientes
    $sql = "
        SELECT u.id, u.usuario, u.foto_perfil,
               CASE
                   WHEN EXISTS (
                       SELECT 1 FROM friends 
                       WHERE (user_id = ? AND friend_id = u.id) 
                       OR (user_id = u.id AND friend_id = ?)
                   ) THEN 'friend'
                   WHEN EXISTS (
                       SELECT 1 FROM friend_requests 
                       WHERE sender_id = ? AND receiver_id = u.id AND status = 'pending'
                   ) THEN 'request_sent'
                   WHEN EXISTS (
                       SELECT 1 FROM friend_requests 
                       WHERE sender_id = u.id AND receiver_id = ? AND status = 'pending'
                   ) THEN 'request_received'
                   ELSE 'none'
               END as friendship_status
        FROM usuarios u
        WHERE u.id != ? AND u.usuario LIKE ?
        ORDER BY u.usuario ASC
        LIMIT 50
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiiiis", $my_user_id, $my_user_id, $my_user_id, $my_user_id, $my_user_id, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar' => "../pictures/" . htmlspecialchars($row['foto_perfil']),
            'friendship_status' => $row['friendship_status']
        ];
    }
    
    $stmt->close();
    echo json_encode($users);
}

// Enviar solicitud de amistad
function sendFriendRequest($conexion, $sender_id) {
    $receiver_id = $_POST['receiver_id'] ?? 0;
    
    if (!is_numeric($receiver_id) || $receiver_id <= 0 || $receiver_id == $sender_id) {
        echo json_encode(["success" => false, "error" => "Usuario inválido"]);
        return;
    }
    
    // Verificar que no sean ya amigos
    $check_sql = "SELECT 1 FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Ya son amigos"]);
        $check_stmt->close();
        return;
    }
    $check_stmt->close();
    
    // Insertar solicitud
    $sql = "INSERT INTO friend_requests (sender_id, receiver_id, status) 
            VALUES (?, ?, 'pending')
            ON DUPLICATE KEY UPDATE status = 'pending', updated_at = CURRENT_TIMESTAMP";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Solicitud enviada"]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    
    $stmt->close();
}

// Obtener solicitudes recibidas
function getFriendRequests($conexion, $my_user_id) {
    $sql = "
        SELECT fr.id, fr.sender_id, u.usuario, u.foto_perfil, fr.created_at
        FROM friend_requests fr
        INNER JOIN usuarios u ON fr.sender_id = u.id
        WHERE fr.receiver_id = ? AND fr.status = 'pending'
        ORDER BY fr.created_at DESC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = [
            'id' => $row['id'],
            'sender_id' => $row['sender_id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar' => "../pictures/" . htmlspecialchars($row['foto_perfil']),
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    echo json_encode($requests);
}

// Responder a solicitud (aceptar/rechazar)
function respondToRequest($conexion, $my_user_id) {
    $request_id = $_POST['request_id'] ?? 0;
    $action = $_POST['response'] ?? ''; // 'accept' o 'reject'
    
    if (!in_array($action, ['accept', 'reject'])) {
        echo json_encode(["success" => false, "error" => "Acción inválida"]);
        return;
    }
    
    // Obtener información de la solicitud
    $get_sql = "SELECT sender_id, receiver_id FROM friend_requests WHERE id = ? AND receiver_id = ? AND status = 'pending'";
    $get_stmt = $conexion->prepare($get_sql);
    $get_stmt->bind_param("ii", $request_id, $my_user_id);
    $get_stmt->execute();
    $result = $get_stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Solicitud no encontrada"]);
        $get_stmt->close();
        return;
    }
    
    $request_data = $result->fetch_assoc();
    $sender_id = $request_data['sender_id'];
    $receiver_id = $request_data['receiver_id'];
    $get_stmt->close();
    
    $conexion->begin_transaction();
    
    try {
        // Actualizar estado de la solicitud
        $new_status = ($action === 'accept') ? 'accepted' : 'rejected';
        $update_sql = "UPDATE friend_requests SET status = ? WHERE id = ?";
        $update_stmt = $conexion->prepare($update_sql);
        $update_stmt->bind_param("si", $new_status, $request_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Si se acepta, crear la relación de amistad (bidireccional)
        if ($action === 'accept') {
            $friend_sql = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)";
            $friend_stmt = $conexion->prepare($friend_sql);
            $friend_stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
            $friend_stmt->execute();
            $friend_stmt->close();
            
            // Otorgar medalla de "Primer Amigo" si es su primer amigo
            otorgarMedallaPrimerAmigo($conexion, $sender_id);
            otorgarMedallaPrimerAmigo($conexion, $receiver_id);
        }
        
        $conexion->commit();
        echo json_encode(["success" => true, "message" => "Solicitud " . ($action === 'accept' ? 'aceptada' : 'rechazada')]);
        
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}

// Obtener lista de amigos
function getFriends($conexion, $my_user_id) {
    $sql = "
        SELECT u.id, u.usuario, u.foto_perfil,
               (SELECT COUNT(*) FROM mensajes 
                WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
        FROM friends f
        INNER JOIN usuarios u ON f.friend_id = u.id
        WHERE f.user_id = ?
        ORDER BY u.usuario ASC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $my_user_id, $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $friends = [];
    while ($row = $result->fetch_assoc()) {
        $friends[] = [
            'id' => $row['id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar' => "../pictures/" . htmlspecialchars($row['foto_perfil']),
            'unread_count' => $row['unread_count']
        ];
    }
    
    $stmt->close();
    echo json_encode($friends);
}

// Eliminar amigo
function removeFriend($conexion, $my_user_id) {
    $friend_id = $_POST['friend_id'] ?? 0;
    
    if (!is_numeric($friend_id) || $friend_id <= 0) {
        echo json_encode(["success" => false, "error" => "ID inválido"]);
        return;
    }
    
    // Eliminar relación bidireccional
    $sql = "DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiii", $my_user_id, $friend_id, $friend_id, $my_user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Amigo eliminado"]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    
    $stmt->close();
}

// Obtener información del usuario actual
function getUserInfo($conexion, $my_user_id) {
    $sql = "SELECT id, usuario, foto_perfil FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $my_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'id' => $row['id'],
            'username' => htmlspecialchars($row['usuario']),
            'avatar' => "../pictures/" . htmlspecialchars($row['foto_perfil'])
        ]);
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
    
    $stmt->close();
}

// Otorgar medalla de primer amigo
function otorgarMedallaPrimerAmigo($conexion, $usuario_id) {
    // Verificar si ya tiene la medalla
    $check_sql = "
        SELECT um.id 
        FROM usuario_medallas um
        INNER JOIN medallas m ON um.medalla_id = m.id
        WHERE um.usuario_id = ? AND m.codigo = 'primer_amigo'
    ";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("i", $usuario_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    // Si ya tiene la medalla, no hacer nada
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        return;
    }
    $check_stmt->close();
    
    // Obtener ID de la medalla
    $medalla_sql = "SELECT id FROM medallas WHERE codigo = 'primer_amigo' AND activa = TRUE";
    $medalla_result = $conexion->query($medalla_sql);
    
    if ($medalla_row = $medalla_result->fetch_assoc()) {
        $medalla_id = $medalla_row['id'];
        
        // Otorgar la medalla
        $insert_sql = "INSERT IGNORE INTO usuario_medallas (usuario_id, medalla_id) VALUES (?, ?)";
        $insert_stmt = $conexion->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $usuario_id, $medalla_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
}
?>
