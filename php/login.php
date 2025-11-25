<?php
session_start();

$host = "drippking.com";
$usuario_db = "drippkin_Host";
$contrasena_db = "Drippking5545";
$nombre_db = "Drippkin_poi_database";
$port = 3306;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    die("Error de conexión a la BD: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username_ingresado = $_POST['username'] ?? '';
    $password_ingresada = $_POST['password'] ?? '';

    $sql = "SELECT id, contrasena FROM usuarios WHERE usuario = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $username_ingresado);
    $stmt->execute();
    
    $stmt->bind_result($user_id, $contrasena_hash_almacenado);
    

    if ($stmt->fetch()) {

        if (password_verify($password_ingresada, $contrasena_hash_almacenado)) {

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username_ingresado;

            $stmt->close();
            
            // Marcar usuario como online
            $update_stmt = $conexion->prepare("UPDATE usuarios SET is_online = 1, last_activity = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            $conexion->close();
            
            header("Location: ../html/chats.html"); 
            exit();

        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Nombre de usuario no encontrado.";
    }

    $stmt->close();

    $conexion->close();
    
    echo "<h1>❌ Error de Inicio de Sesión</h1><p>{$error}</p><p><a href='../html/login.html'>Volver a intentar</a></p>";
}
?>