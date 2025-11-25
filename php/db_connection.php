<?php
$host = "localhost";
$usuario_db = "root";
$contrasena_db = "";
$nombre_db = "poi_database";
$port = 3307;

$host = "drippking.com";
$usuario_db = "drippkin_Host";
$contrasena_db = "DrippKing5545";
$nombre_db = "drippkin_poi_database";
$port = 3306;

global $conexion;
$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db, $port);

if ($conexion->connect_error) {
    die("Error de conexión a la BD: " . $conexion->connect_error);
}
?>