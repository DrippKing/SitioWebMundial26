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
    
    $carpeta_destino = "../pictures/"; 
    $foto_nombre_db = 'default.jpg';

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        
        $archivo_temporal = $_FILES['profile_picture']['tmp_name'];
        $nombre_original = basename($_FILES['profile_picture']['name']);
        
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_unico = uniqid('pfp_', true) . "." . $extension;
        $ruta_final = $carpeta_destino . $nombre_unico;

        if (move_uploaded_file($archivo_temporal, $ruta_final)) {
            $foto_nombre_db = $nombre_unico; 
        } else {
            echo "Advertencia: Error al guardar la foto en el servidor";
        }
    }
    
    $nombre = $_POST['name'] ?? '';
    $apellido1 = $_POST['lastname1'] ?? '';
    $apellido2 = $_POST['lastname2'] ?? '';
    $email = $_POST['email'] ?? '';
    $cumpleanos = $_POST['birthday'] ?? '';
    $pais = $_POST['country'] ?? '';
    $usuario = $_POST['username'] ?? '';
    $equipo = $_POST['team'] ?? '';
    
  
    $password_texto = $_POST['password'] ?? '';
    $contrasena_hash = password_hash($password_texto, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, apellido1, apellido2, email, cumpleanos, pais, usuario, contrasena, equipo, foto_perfil) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    
    $stmt->bind_param("ssssssssss",
        $nombre, 
        $apellido1, 
        $apellido2, 
        $email, 
        $cumpleanos, 
        $pais, 
        $usuario, 
        $contrasena_hash, 
        $equipo,
        $foto_nombre_db    
    );

    if ($stmt->execute()) {
        // Obtener el ID del usuario recién creado
        $new_user_id = $conexion->insert_id;
        
        // Iniciar sesión automáticamente
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $usuario;
        
        // Marcar como online
        $update_stmt = $conexion->prepare("UPDATE usuarios SET is_online = 1, last_activity = NOW() WHERE id = ?");
        $update_stmt->bind_param("i", $new_user_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        $stmt->close();
        $conexion->close();
        
        echo "<h1>¡Registro Exitoso!</h1>";
        header("Location: ../html/games.html");
        exit();
    } else {
        echo "<h1>🚨 Error de Registro:</h1><p>" . htmlspecialchars($error_msg) . "</p>";
    }
    
    $stmt->close();
}

$conexion->close();
?>