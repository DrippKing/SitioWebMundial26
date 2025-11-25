<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

$info = [
    'sesion_activa' => isset($_SESSION['user_id']),
    'user_id' => $_SESSION['user_id'] ?? null,
    'user_name' => $_SESSION['username'] ?? null,
    'es_miembro_LMEADOS' => false
];

if (isset($_SESSION['user_id'])) {
    try {
        require_once 'db_connection.php';
        
        // Verificar si es miembro de LMEADOS (grupo ID 2)
        $stmt = $conexion->prepare("
            SELECT COUNT(*) as es_miembro 
            FROM grupo_miembros 
            WHERE grupo_id = 2 AND usuario_id = ?
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $info['es_miembro_LMEADOS'] = $row['es_miembro'] > 0;
        
        // Obtener nombre de usuario
        $stmt2 = $conexion->prepare("SELECT usuario FROM usuarios WHERE id = ?");
        $stmt2->bind_param("i", $_SESSION['user_id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($row2 = $result2->fetch_assoc()) {
            $info['user_name'] = $row2['usuario'];
        }
        
        $conexion->close();
    } catch (Exception $e) {
        $info['error'] = $e->getMessage();
    }
}

echo json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
