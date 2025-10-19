<?php

$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    die("Error de conexión a la BD: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $carpeta_destino = "../pictures/"; 
    $foto_nombre_db = 'default.jpg';

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        
        //ruta
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
        echo "<h1>¡Registro Exitoso!</h1>";
        header("Location: ../html/login.html");
    } else {
        echo "<h1>🚨 Error de Registro:</h1><p>" . htmlspecialchars($error_msg) . "</p>";
    }
    
    $stmt->close();
}

$conexion->close();
?>