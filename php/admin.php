<?php
session_start();
header('Content-Type: application/json');

// Verificar que sea administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) { // Solo el user_id 1 es admin
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Conexión a base de datos
$host = "localhost";
$port = "3307";
$dbname = "poi_database";
$username = "root";
$password = "";

try {
    $conexion = new mysqli($host, $username, $password, $dbname, $port);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    $conexion->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a base de datos']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_partidos_pendientes':
        getPartidosPendientes($conexion);
        break;
    
    case 'set_resultado':
        setResultado($conexion);
        break;
    
    case 'get_all_partidos':
        getAllPartidos($conexion);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
        break;
}

$conexion->close();

// ========================================
// FUNCIONES
// ========================================

// Obtener partidos pendientes de resultado
function getPartidosPendientes($conexion) {
    $sql = "
        SELECT 
            p.id,
            p.fase,
            p.jornada,
            p.grupo,
            p.fecha_partido,
            p.estadio,
            p.finalizado,
            p.goles_local,
            p.goles_visitante,
            p.penales_local,
            p.penales_visitante,
            el.nombre as equipo_local,
            el.bandera as bandera_local,
            ev.nombre as equipo_visitante,
            ev.bandera as bandera_visitante
        FROM partidos p
        INNER JOIN equipos el ON p.equipo_local_id = el.id
        INNER JOIN equipos ev ON p.equipo_visitante_id = ev.id
        ORDER BY p.finalizado ASC, p.fecha_partido ASC, p.id ASC
    ";
    
    $result = $conexion->query($sql);
    
    $partidos = [];
    while ($row = $result->fetch_assoc()) {
        $partidos[] = [
            'id' => $row['id'],
            'fase' => $row['fase'],
            'jornada' => $row['jornada'],
            'grupo' => $row['grupo'],
            'fecha' => $row['fecha_partido'],
            'estadio' => $row['estadio'],
            'finalizado' => (bool)$row['finalizado'],
            'equipo_local' => [
                'nombre' => $row['equipo_local'],
                'bandera' => $row['bandera_local']
            ],
            'equipo_visitante' => [
                'nombre' => $row['equipo_visitante'],
                'bandera' => $row['bandera_visitante']
            ],
            'resultado' => [
                'goles_local' => $row['goles_local'],
                'goles_visitante' => $row['goles_visitante'],
                'penales_local' => $row['penales_local'],
                'penales_visitante' => $row['penales_visitante']
            ]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'partidos' => $partidos
    ]);
}

// Obtener todos los partidos
function getAllPartidos($conexion) {
    getPartidosPendientes($conexion);
}

// Establecer resultado de un partido
function setResultado($conexion) {
    $partido_id = $_POST['partido_id'] ?? 0;
    $goles_local = $_POST['goles_local'] ?? null;
    $goles_visitante = $_POST['goles_visitante'] ?? null;
    $penales_local = $_POST['penales_local'] ?? null;
    $penales_visitante = $_POST['penales_visitante'] ?? null;
    
    if (!$partido_id || $goles_local === null || $goles_visitante === null) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // Actualizar resultado del partido
        $sql = "
            UPDATE partidos 
            SET goles_local = ?, 
                goles_visitante = ?, 
                penales_local = ?, 
                penales_visitante = ?, 
                finalizado = TRUE 
            WHERE id = ?
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiiii", $goles_local, $goles_visitante, $penales_local, $penales_visitante, $partido_id);
        $stmt->execute();
        $stmt->close();
        
        // Calcular puntos para todas las predicciones de este partido
        calcularPuntos($conexion, $partido_id, $goles_local, $goles_visitante, $penales_local, $penales_visitante);
        
        // Verificar y otorgar medallas
        verificarMedallas($conexion);
        
        $conexion->commit();
        
        echo json_encode(['success' => true, 'message' => 'Resultado guardado y puntos calculados']);
        
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $e->getMessage()]);
    }
}

// Calcular puntos de predicciones
function calcularPuntos($conexion, $partido_id, $goles_local, $goles_visitante, $penales_local, $penales_visitante) {
    // Obtener todas las predicciones de este partido
    $sql = "SELECT * FROM predicciones WHERE partido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $partido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($pred = $result->fetch_assoc()) {
        $puntos = 0;
        
        // Determinar ganador real
        $ganador_real = null;
        if ($penales_local !== null && $penales_visitante !== null) {
            $ganador_real = $penales_local > $penales_visitante ? 'local' : 'visitante';
        } else {
            if ($goles_local > $goles_visitante) $ganador_real = 'local';
            elseif ($goles_visitante > $goles_local) $ganador_real = 'visitante';
            else $ganador_real = 'empate';
        }
        
        // Determinar ganador predicho
        $ganador_predicho = null;
        if ($pred['penales_local_prediccion'] !== null && $pred['penales_visitante_prediccion'] !== null) {
            $ganador_predicho = $pred['penales_local_prediccion'] > $pred['penales_visitante_prediccion'] ? 'local' : 'visitante';
        } else {
            if ($pred['goles_local_prediccion'] > $pred['goles_visitante_prediccion']) $ganador_predicho = 'local';
            elseif ($pred['goles_visitante_prediccion'] > $pred['goles_local_prediccion']) $ganador_predicho = 'visitante';
            else $ganador_predicho = 'empate';
        }
        
        // Calcular puntos
        // Predicción exacta = 10 puntos
        if ($pred['goles_local_prediccion'] == $goles_local && 
            $pred['goles_visitante_prediccion'] == $goles_visitante) {
            
            // Si hubo penales y también los predijo correctamente
            if ($penales_local !== null && 
                $pred['penales_local_prediccion'] == $penales_local && 
                $pred['penales_visitante_prediccion'] == $penales_visitante) {
                $puntos = 15; // Bonus por predecir penales exactos
            } else {
                $puntos = 10;
            }
        }
        // Acertó solo el ganador = 5 puntos
        elseif ($ganador_predicho === $ganador_real) {
            $puntos = 5;
        }
        // Falló = 0 puntos
        else {
            $puntos = 0;
        }
        
        // Actualizar puntos de la predicción
        $update_sql = "UPDATE predicciones SET puntos_ganados = ? WHERE usuario_id = ? AND partido_id = ?";
        $update_stmt = $conexion->prepare($update_sql);
        $update_stmt->bind_param("iii", $puntos, $pred['usuario_id'], $partido_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Actualizar tabla de puntuaciones del torneo
        actualizarPuntuacionesTorneo($conexion, $pred['usuario_id'], $puntos);
    }
    
    $stmt->close();
}

// Actualizar puntuaciones del torneo
function actualizarPuntuacionesTorneo($conexion, $usuario_id, $puntos) {
    // Verificar si existe registro
    $check_sql = "SELECT * FROM puntuaciones_torneo WHERE usuario_id = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("i", $usuario_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        // Crear registro nuevo
        $insert_sql = "INSERT INTO puntuaciones_torneo (usuario_id, puntos_totales, predicciones_exactas, predicciones_tendencia, predicciones_incorrectas) VALUES (?, 0, 0, 0, 0)";
        $insert_stmt = $conexion->prepare($insert_sql);
        $insert_stmt->bind_param("i", $usuario_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $check_stmt->close();
    
    // Actualizar puntos
    if ($puntos >= 10) {
        $sql = "UPDATE puntuaciones_torneo SET puntos_totales = puntos_totales + ?, predicciones_exactas = predicciones_exactas + 1 WHERE usuario_id = ?";
    } elseif ($puntos > 0) {
        $sql = "UPDATE puntuaciones_torneo SET puntos_totales = puntos_totales + ?, predicciones_tendencia = predicciones_tendencia + 1 WHERE usuario_id = ?";
    } else {
        $sql = "UPDATE puntuaciones_torneo SET predicciones_incorrectas = predicciones_incorrectas + 1 WHERE usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
        return;
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $puntos, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

// Verificar y otorgar medallas a todos los usuarios
function verificarMedallas($conexion) {
    // Obtener todos los usuarios
    $usuarios_sql = "SELECT id FROM usuarios";
    $usuarios_result = $conexion->query($usuarios_sql);
    
    while ($usuario = $usuarios_result->fetch_assoc()) {
        $usuario_id = $usuario['id'];
        
        // 1. Verificar medalla de primer amigo
        $amigos_sql = "SELECT COUNT(*) as total FROM friends WHERE user_id = ? OR friend_id = ?";
        $amigos_stmt = $conexion->prepare($amigos_sql);
        $amigos_stmt->bind_param("ii", $usuario_id, $usuario_id);
        $amigos_stmt->execute();
        $amigos_result = $amigos_stmt->get_result();
        $amigos = $amigos_result->fetch_assoc();
        
        if ($amigos['total'] >= 1) {
            otorgarMedalla($conexion, $usuario_id, 'primer_amigo');
        }
        $amigos_stmt->close();
        
        // 2. Verificar medallas de victorias y derrotas
        $stats_sql = "SELECT predicciones_exactas, predicciones_tendencia, predicciones_incorrectas FROM puntuaciones_torneo WHERE usuario_id = ?";
        $stats_stmt = $conexion->prepare($stats_sql);
        $stats_stmt->bind_param("i", $usuario_id);
        $stats_stmt->execute();
        $stats_result = $stats_stmt->get_result();
        $stats = $stats_result->fetch_assoc();
        
        if ($stats) {
            $victorias = $stats['predicciones_exactas'] + $stats['predicciones_tendencia'];
            $derrotas = $stats['predicciones_incorrectas'];
            
            if ($victorias >= 1) {
                otorgarMedalla($conexion, $usuario_id, 'primera_victoria');
            }
            
            if ($derrotas >= 1) {
                otorgarMedalla($conexion, $usuario_id, 'primera_derrota');
            }
            
            if ($derrotas >= 10) {
                otorgarMedalla($conexion, $usuario_id, 'salado');
            }
        }
        $stats_stmt->close();
    }
}

// Otorgar medalla de top global al finalizar el torneo
function otorgarMedallasTopGlobal($conexion) {
    // Obtener top 3 del ranking
    $ranking_sql = "
        SELECT usuario_id 
        FROM puntuaciones_torneo 
        ORDER BY puntos_totales DESC, predicciones_exactas DESC 
        LIMIT 3
    ";
    $ranking_result = $conexion->query($ranking_sql);
    
    while ($row = $ranking_result->fetch_assoc()) {
        otorgarMedalla($conexion, $row['usuario_id'], 'top_global');
    }
}

// Otorgar medalla a un usuario
function otorgarMedalla($conexion, $usuario_id, $codigo_medalla) {
    // Obtener ID de la medalla
    $medalla_sql = "SELECT id FROM medallas WHERE codigo = ? AND activa = TRUE";
    $medalla_stmt = $conexion->prepare($medalla_sql);
    $medalla_stmt->bind_param("s", $codigo_medalla);
    $medalla_stmt->execute();
    $medalla_result = $medalla_stmt->get_result();
    
    if ($medalla = $medalla_result->fetch_assoc()) {
        $medalla_id = $medalla['id'];
        
        // Intentar insertar (si ya existe, no hace nada por el UNIQUE constraint)
        $insert_sql = "INSERT IGNORE INTO usuario_medallas (usuario_id, medalla_id) VALUES (?, ?)";
        $insert_stmt = $conexion->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $usuario_id, $medalla_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    
    $medalla_stmt->close();
}
