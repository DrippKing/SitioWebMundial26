<?php
// Detectar si estamos en un entorno local (localhost) o en el servidor de producción
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($is_local) {
    // --- Configuración para el entorno LOCAL ---
    $host = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $nombre_db = "poi_database";
    $port = 3307;
} else {
    // --- Configuración para el servidor REMOTO (Producción) ---
    $host = "localhost"; // ¡Importante! Usar localhost también en el servidor.
    $usuario_db = "drippkin_Host";
    $contrasena_db = "DrippKing5545";
    $nombre_db = "drippkin_poi_database";
    $port = 3306;
}

global $conexion;
$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    die("Error de conexión a la BD: " . $conexion->connect_error);
}
?>