<?php
session_start();
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Conexión a base de datos
try {
    require_once 'db_connection.php';
    $conexion->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a base de datos']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// ========================================
// RUTAS DE LA API
// ========================================

switch ($action) {
    case 'get_grupos':
        getGrupos($conexion);
        break;
    
    case 'get_partidos_fase':
        getPartidosFase($conexion, $user_id);
        break;
    
    case 'get_partido':
        getPartido($conexion, $user_id);
        break;
    
    case 'save_prediccion':
        savePrediccion($conexion, $user_id);
        break;
    
    case 'get_mis_predicciones':
        getMisPredicciones($conexion, $user_id);
        break;
    
    case 'get_ranking':
        getRanking($conexion);
        break;
    
    case 'get_tabla_grupo':
        getTablaGrupo($conexion);
        break;
    
    case 'get_clasificados':
        getClasificados($conexion);
        break;
    
    case 'get_user_stats':
        getUserStats($conexion, $user_id);
        break;
    
    case 'get_medallas':
        getMedallas($conexion, $user_id);
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

// Obtener todos los grupos con sus equipos
function getGrupos($conexion) {
    $grupos = [];
    
    for ($letra = 'A'; $letra <= 'H'; $letra++) {
        $sql = "
            SELECT id, nombre, codigo, bandera, puntos, 
                   partidos_jugados, partidos_ganados, partidos_empatados, partidos_perdidos,
                   goles_favor, goles_contra, diferencia_goles
            FROM equipos
            WHERE grupo = ?
            ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $letra);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = [
                'id' => $row['id'],
                'nombre' => htmlspecialchars($row['nombre']),
                'codigo' => htmlspecialchars($row['codigo']),
                'bandera' => $row['bandera'] ? "../assets/football icons/teams/" . htmlspecialchars($row['bandera']) : null,
                'puntos' => (int)$row['puntos'],
                'pj' => (int)$row['partidos_jugados'],
                'pg' => (int)$row['partidos_ganados'],
                'pe' => (int)$row['partidos_empatados'],
                'pp' => (int)$row['partidos_perdidos'],
                'gf' => (int)$row['goles_favor'],
                'gc' => (int)$row['goles_contra'],
                'dif' => (int)$row['diferencia_goles']
            ];
        }
        
        $grupos[$letra] = $equipos;
        $stmt->close();
    }
    
    echo json_encode($grupos);
}

// Obtener partidos de una fase específica
function getPartidosFase($conexion, $user_id) {
    $fase = $_GET['fase'] ?? 'grupo';
    $jornada = isset($_GET['jornada']) ? (int)$_GET['jornada'] : null;
    $grupo = $_GET['grupo'] ?? null;
    
    $sql = "
        SELECT 
            p.id,
            p.fase,
            p.jornada,
            p.grupo,
            p.fecha_partido,
            p.estadio,
            p.goles_local,
            p.goles_visitante,
            p.penales_local,
            p.penales_visitante,
            p.finalizado,
            el.id as local_id,
            el.nombre as local_nombre,
            el.codigo as local_codigo,
            el.bandera as local_bandera,
            ev.id as visitante_id,
            ev.nombre as visitante_nombre,
            ev.codigo as visitante_codigo,
            ev.bandera as visitante_bandera,
            pred.goles_local_prediccion,
            pred.goles_visitante_prediccion,
            pred.penales_local_prediccion,
            pred.penales_visitante_prediccion,
            pred.puntos_ganados
        FROM partidos p
        INNER JOIN equipos el ON p.equipo_local_id = el.id
        INNER JOIN equipos ev ON p.equipo_visitante_id = ev.id
        LEFT JOIN predicciones pred ON p.id = pred.partido_id AND pred.usuario_id = ?
        WHERE p.fase = ?
    ";
    
    $params = [$user_id, $fase];
    $types = "is";
    
    if ($jornada !== null && $fase === 'grupo') {
        $sql .= " AND p.jornada = ?";
        $params[] = $jornada;
        $types .= "i";
    }
    
    if ($grupo !== null && $fase === 'grupo') {
        $sql .= " AND p.grupo = ?";
        $params[] = $grupo;
        $types .= "s";
    }
    
    $sql .= " ORDER BY p.fecha_partido ASC, p.id ASC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $partidos = [];
    while ($row = $result->fetch_assoc()) {
        $partidos[] = [
            'id' => $row['id'],
            'fase' => $row['fase'],
            'jornada' => $row['jornada'],
            'grupo' => $row['grupo'],
            'fecha' => $row['fecha_partido'],
            'estadio' => htmlspecialchars($row['estadio']),
            'finalizado' => (bool)$row['finalizado'],
            'local' => [
                'id' => $row['local_id'],
                'nombre' => htmlspecialchars($row['local_nombre']),
                'codigo' => htmlspecialchars($row['local_codigo']),
                'bandera' => $row['local_bandera'] ? "../assets/football icons/teams/" . htmlspecialchars($row['local_bandera']) : null,
                'goles' => $row['goles_local'],
                'penales' => $row['penales_local']
            ],
            'visitante' => [
                'id' => $row['visitante_id'],
                'nombre' => htmlspecialchars($row['visitante_nombre']),
                'codigo' => htmlspecialchars($row['visitante_codigo']),
                'bandera' => $row['visitante_bandera'] ? "../assets/football icons/teams/" . htmlspecialchars($row['visitante_bandera']) : null,
                'goles' => $row['goles_visitante'],
                'penales' => $row['penales_visitante']
            ],
            'prediccion' => $row['goles_local_prediccion'] !== null ? [
                'local' => (int)$row['goles_local_prediccion'],
                'visitante' => (int)$row['goles_visitante_prediccion'],
                'penales_local' => $row['penales_local_prediccion'],
                'penales_visitante' => $row['penales_visitante_prediccion'],
                'puntos' => (int)$row['puntos_ganados']
            ] : null
        ];
    }
    
    $stmt->close();
    echo json_encode($partidos);
}

// Guardar predicción
function savePrediccion($conexion, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $partido_id = $data['partido_id'] ?? null;
    $goles_local = $data['goles_local'] ?? null;
    $goles_visitante = $data['goles_visitante'] ?? null;
    $penales_local = $data['penales_local'] ?? null;
    $penales_visitante = $data['penales_visitante'] ?? null;
    
    if (!$partido_id || $goles_local === null || $goles_visitante === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }
    
    // Verificar que el partido no haya finalizado
    $sql = "SELECT finalizado, fecha_partido FROM partidos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $partido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $partido = $result->fetch_assoc();
    $stmt->close();
    
    if (!$partido) {
        http_response_code(404);
        echo json_encode(['error' => 'Partido no encontrado']);
        return;
    }
    
    if ($partido['finalizado']) {
        http_response_code(400);
        echo json_encode(['error' => 'El partido ya finalizó']);
        return;
    }
    
    // Verificar que no haya pasado la fecha del partido
    $fecha_partido = strtotime($partido['fecha_partido']);
    if (time() > $fecha_partido) {
        http_response_code(400);
        echo json_encode(['error' => 'Ya pasó la fecha del partido']);
        return;
    }
    
    // Insertar o actualizar predicción
    $sql = "
        INSERT INTO predicciones 
        (usuario_id, partido_id, goles_local_prediccion, goles_visitante_prediccion, 
         penales_local_prediccion, penales_visitante_prediccion)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            goles_local_prediccion = VALUES(goles_local_prediccion),
            goles_visitante_prediccion = VALUES(goles_visitante_prediccion),
            penales_local_prediccion = VALUES(penales_local_prediccion),
            penales_visitante_prediccion = VALUES(penales_visitante_prediccion),
            updated_at = CURRENT_TIMESTAMP
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiiiii", $user_id, $partido_id, $goles_local, $goles_visitante, 
                      $penales_local, $penales_visitante);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Predicción guardada']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar predicción']);
    }
    
    $stmt->close();
}

// Obtener mis predicciones
function getMisPredicciones($conexion, $user_id) {
    $sql = "
        SELECT 
            p.id as partido_id,
            pred.goles_local_prediccion,
            pred.goles_visitante_prediccion,
            pred.penales_local_prediccion,
            pred.penales_visitante_prediccion,
            pred.puntos_ganados,
            p.goles_local as resultado_local,
            p.goles_visitante as resultado_visitante,
            p.finalizado,
            el.nombre as local_nombre,
            ev.nombre as visitante_nombre
        FROM predicciones pred
        INNER JOIN partidos p ON pred.partido_id = p.id
        INNER JOIN equipos el ON p.equipo_local_id = el.id
        INNER JOIN equipos ev ON p.equipo_visitante_id = ev.id
        WHERE pred.usuario_id = ?
        ORDER BY p.fecha_partido ASC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $predicciones = [];
    while ($row = $result->fetch_assoc()) {
        $predicciones[] = [
            'partido_id' => $row['partido_id'],
            'prediccion' => [
                'local' => (int)$row['goles_local_prediccion'],
                'visitante' => (int)$row['goles_visitante_prediccion'],
                'penales_local' => $row['penales_local_prediccion'],
                'penales_visitante' => $row['penales_visitante_prediccion']
            ],
            'resultado' => $row['finalizado'] ? [
                'local' => (int)$row['resultado_local'],
                'visitante' => (int)$row['resultado_visitante']
            ] : null,
            'puntos' => (int)$row['puntos_ganados'],
            'equipos' => [
                'local' => htmlspecialchars($row['local_nombre']),
                'visitante' => htmlspecialchars($row['visitante_nombre'])
            ]
        ];
    }
    
    $stmt->close();
    echo json_encode([
        'success' => true,
        'predicciones' => $predicciones
    ]);
}

// Obtener ranking general
function getRanking($conexion) {
    $sql = "
        SELECT 
            u.id,
            u.usuario,
            u.foto_perfil,
            COALESCE(pt.puntos_totales, 0) as puntos,
            COALESCE(pt.predicciones_exactas, 0) as exactas,
            COALESCE(pt.predicciones_tendencia, 0) as tendencia,
            COALESCE(pt.predicciones_incorrectas, 0) as incorrectas
        FROM usuarios u
        LEFT JOIN puntuaciones_torneo pt ON u.id = pt.usuario_id
        ORDER BY puntos DESC, exactas DESC, tendencia DESC
        LIMIT 50
    ";
    
    $result = $conexion->query($sql);
    
    $ranking = [];
    $posicion = 1;
    while ($row = $result->fetch_assoc()) {
        $ranking[] = [
            'posicion' => $posicion++,
            'usuario_id' => $row['id'],
            'usuario' => htmlspecialchars($row['usuario']),
            'avatar' => $row['foto_perfil'] ? "../pictures/" . htmlspecialchars($row['foto_perfil']) : "../assets/general icons/default-avatar.png",
            'puntos' => (int)$row['puntos'],
            'exactas' => (int)$row['exactas'],
            'tendencia' => (int)$row['tendencia'],
            'incorrectas' => (int)$row['incorrectas']
        ];
    }
    
    echo json_encode($ranking);
}

// Obtener tabla de posiciones de un grupo
function getTablaGrupo($conexion) {
    $grupo = $_GET['grupo'] ?? 'A';
    
    $sql = "
        SELECT 
            nombre, codigo, bandera, puntos,
            partidos_jugados, partidos_ganados, partidos_empatados, partidos_perdidos,
            goles_favor, goles_contra, diferencia_goles
        FROM equipos
        WHERE grupo = ?
        ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $grupo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tabla = [];
    $posicion = 1;
    while ($row = $result->fetch_assoc()) {
        $tabla[] = [
            'posicion' => $posicion++,
            'nombre' => htmlspecialchars($row['nombre']),
            'codigo' => htmlspecialchars($row['codigo']),
            'bandera' => $row['bandera'] ? "../assets/football icons/teams/" . htmlspecialchars($row['bandera']) : null,
            'puntos' => (int)$row['puntos'],
            'pj' => (int)$row['partidos_jugados'],
            'pg' => (int)$row['partidos_ganados'],
            'pe' => (int)$row['partidos_empatados'],
            'pp' => (int)$row['partidos_perdidos'],
            'gf' => (int)$row['goles_favor'],
            'gc' => (int)$row['goles_contra'],
            'dif' => (int)$row['diferencia_goles']
        ];
    }
    
    $stmt->close();
    echo json_encode($tabla);
}

// Obtener equipos clasificados para fase eliminatoria
function getClasificados($conexion) {
    $clasificados = [];
    
    for ($letra = 'A'; $letra <= 'H'; $letra++) {
        $sql = "
            SELECT id, nombre, codigo, bandera, puntos, diferencia_goles
            FROM equipos
            WHERE grupo = ?
            ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC
            LIMIT 2
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $letra);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = [
                'id' => $row['id'],
                'nombre' => htmlspecialchars($row['nombre']),
                'codigo' => htmlspecialchars($row['codigo']),
                'bandera' => $row['bandera'] ? "../assets/football icons/teams/" . htmlspecialchars($row['bandera']) : null
            ];
        }
        
        $clasificados[$letra] = $equipos;
        $stmt->close();
    }
    
    echo json_encode($clasificados);
}

// Obtener estadísticas del usuario
function getUserStats($conexion, $user_id) {
    // Obtener puntuación del torneo
    $sql = "
        SELECT 
            puntos_totales,
            predicciones_exactas,
            predicciones_tendencia,
            predicciones_incorrectas
        FROM puntuaciones_torneo
        WHERE usuario_id = ?
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $torneo = $result->fetch_assoc();
    $stmt->close();
    
    // Contar total de predicciones
    $sql = "SELECT COUNT(*) as total FROM predicciones WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_predicciones' => (int)$count['total'],
            'puntos_totales' => $torneo ? (int)$torneo['puntos_totales'] : 0,
            'predicciones_exactas' => $torneo ? (int)$torneo['predicciones_exactas'] : 0,
            'predicciones_tendencia' => $torneo ? (int)$torneo['predicciones_tendencia'] : 0,
            'predicciones_incorrectas' => $torneo ? (int)$torneo['predicciones_incorrectas'] : 0
        ]
    ]);
}

// Obtener medallas del usuario
function getMedallas($conexion, $user_id) {
    $sql = "
        SELECT 
            m.id,
            m.codigo,
            m.nombre,
            m.descripcion,
            m.icono,
            um.fecha_obtencion
        FROM usuario_medallas um
        INNER JOIN medallas m ON um.medalla_id = m.id
        WHERE um.usuario_id = ?
        ORDER BY um.fecha_obtencion DESC
    ";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $medallas = [];
    while ($row = $result->fetch_assoc()) {
        $medallas[] = [
            'id' => $row['id'],
            'codigo' => $row['codigo'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'icono' => $row['icono'],
            'fecha_obtencion' => $row['fecha_obtencion']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'medallas' => $medallas
    ]);
}

?>
