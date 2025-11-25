<?php
session_start();

// Marcar usuario como offline antes de cerrar sesión
if (isset($_SESSION['user_id'])) {
    $host = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $nombre_db = "poi_database";
    $port = 3307;

    $conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);
    
    if (!$conexion->connect_error) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conexion->prepare("UPDATE usuarios SET is_online = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    }
}

session_destroy();
header("Location: ../html/login.html");
exit();
?>
