<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Retornar datos del usuario en JSON
header('Content-Type: application/json');

require_once 'db_connection.php';
// El archivo db_connection.php ya se encarga de la conexión y de la gestión de errores.

$user_id = $_SESSION['user_id'];

// Actualizar última actividad y marcar como online
$update_sql = "UPDATE usuarios SET last_activity = NOW(), is_online = 1 WHERE id = ?";
$update_stmt = $conexion->prepare($update_sql);
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();

$sql = "SELECT id, usuario, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'loggedIn' => true,
        'id' => $row['id'],
        'username' => $row['usuario'],
        'foto_perfil' => $row['foto_perfil']
    ]);
} else {
    echo json_encode(["error" => "Usuario no encontrado"]);
}

$stmt->close();
$conexion->close();
?>
