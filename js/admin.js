// ========================================
// ADMIN.JS - Panel de administración
// ========================================

console.log('✅ Admin.js cargado');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando panel admin...');
    verificarAcceso();
});

function verificarAcceso() {
    // Verificar que el usuario esté logueado y sea admin
    fetch('../php/session_check.php')
        .then(res => res.json())
        .then(data => {
            console.log('Session data:', data);
            
            if (!data.loggedIn) {
                alert('Debes iniciar sesión');
                window.location.href = 'login.html';
                return;
            }
            
            if (data.id != 1) {
                alert('No tienes permisos de administrador');
                window.location.href = 'profile.html';
                return;
            }
            
            // Usuario autorizado, cargar partidos
            cargarPartidos();
        })
        .catch(error => {
            console.error('Error verificando sesión:', error);
            alert('Error de conexión');
            window.location.href = 'login.html';
        });
}

function cargarPartidos() {
    fetch('../php/admin.php?action=get_all_partidos')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderPartidos(data.partidos);
                actualizarEstadisticas(data.partidos);
            } else {
                document.getElementById('partidos-list').innerHTML = 
                    '<div class="loading">Error al cargar partidos</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('partidos-list').innerHTML = 
                '<div class="loading">Error de conexión</div>';
        });
}

function renderPartidos(partidos) {
    const container = document.getElementById('partidos-list');
    
    if (partidos.length === 0) {
        container.innerHTML = '<div class="loading">No hay partidos disponibles</div>';
        return;
    }
    
    container.innerHTML = partidos.map(partido => {
        const faseTexto = partido.fase === 'grupos' ? 
            `Grupo ${partido.grupo} - Jornada ${partido.jornada}` : 
            partido.fase.toUpperCase();
        
        const fecha = new Date(partido.fecha).toLocaleString('es-MX');
        
        if (partido.finalizado) {
            return `
                <div class="partido-card finalizado">
                    <div class="partido-header">
                        <div class="partido-info">
                            <span class="fase-info">${faseTexto}</span>
                            <span class="fecha-info">${fecha} - ${partido.estadio}</span>
                        </div>
                        <span class="status-badge finalizado">✓ FINALIZADO</span>
                    </div>
                    <div class="partido-equipos">
                        <div class="equipo">
                            <img src="../assets/football icons/teams/${partido.equipo_local.bandera}" alt="${partido.equipo_local.nombre}">
                            <span class="equipo-nombre">${partido.equipo_local.nombre}</span>
                        </div>
                        <span class="vs">VS</span>
                        <div class="equipo">
                            <span class="equipo-nombre">${partido.equipo_visitante.nombre}</span>
                            <img src="../assets/football icons/teams/${partido.equipo_visitante.bandera}" alt="${partido.equipo_visitante.nombre}">
                        </div>
                    </div>
                    <div class="resultado-display">
                        <span>${partido.equipo_local.nombre}</span>
                        <span class="score">${partido.resultado.goles_local} - ${partido.resultado.goles_visitante}</span>
                        <span>${partido.equipo_visitante.nombre}</span>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="partido-card" data-partido-id="${partido.id}">
                    <div class="partido-header">
                        <div class="partido-info">
                            <span class="fase-info">${faseTexto}</span>
                            <span class="fecha-info">${fecha} - ${partido.estadio}</span>
                        </div>
                        <span class="status-badge pendiente">⏱ PENDIENTE</span>
                    </div>
                    <div class="partido-equipos">
                        <div class="equipo">
                            <img src="../assets/football icons/teams/${partido.equipo_local.bandera}" alt="${partido.equipo_local.nombre}">
                            <span class="equipo-nombre">${partido.equipo_local.nombre}</span>
                        </div>
                        <span class="vs">VS</span>
                        <div class="equipo">
                            <span class="equipo-nombre">${partido.equipo_visitante.nombre}</span>
                            <img src="../assets/football icons/teams/${partido.equipo_visitante.bandera}" alt="${partido.equipo_visitante.nombre}">
                        </div>
                    </div>
                    <form class="resultado-form" onsubmit="guardarResultado(event, ${partido.id})">
                        <div class="input-group">
                            <label>Goles ${partido.equipo_local.nombre}</label>
                            <input type="number" name="goles_local" min="0" max="20" value="0" required>
                        </div>
                        <div class="vs">-</div>
                        <div class="input-group">
                            <label>Goles ${partido.equipo_visitante.nombre}</label>
                            <input type="number" name="goles_visitante" min="0" max="20" value="0" required>
                        </div>
                        
                        <button type="submit" class="btn-guardar">💾 GUARDAR RESULTADO</button>
                    </form>
                </div>
            `;
        }
    }).join('');
}

function guardarResultado(event, partido_id) {
    event.preventDefault();
    
    console.log('Guardando resultado para partido:', partido_id);
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', 'set_resultado');
    formData.append('partido_id', partido_id);
    
    // Debug: mostrar datos que se enviarán
    console.log('Datos a enviar:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    const btn = form.querySelector('.btn-guardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    
    fetch('../php/admin.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        console.log('Respuesta status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Respuesta data:', data);
        
        if (data.success) {
            mostrarMensaje(form, 'success', '✓ Resultado guardado y puntos calculados');
            setTimeout(() => {
                cargarPartidos(); // Recargar lista
            }, 1500);
        } else {
            mostrarMensaje(form, 'error', '✗ Error: ' + (data.error || 'Error desconocido'));
            btn.disabled = false;
            btn.textContent = '💾 GUARDAR RESULTADO';
        }
    })
    .catch(error => {
        console.error('Error en guardar:', error);
        mostrarMensaje(form, 'error', '✗ Error de conexión: ' + error.message);
        btn.disabled = false;
        btn.textContent = '💾 GUARDAR RESULTADO';
    });
}

function mostrarMensaje(form, tipo, texto) {
    // Eliminar mensaje anterior si existe
    const mensajeAnterior = form.querySelector('.mensaje');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
    const mensaje = document.createElement('div');
    mensaje.className = `mensaje ${tipo}`;
    mensaje.textContent = texto;
    form.appendChild(mensaje);
    
    setTimeout(() => {
        mensaje.remove();
    }, 3000);
}

function actualizarEstadisticas(partidos) {
    const finalizados = partidos.filter(p => p.finalizado).length;
    const pendientes = partidos.filter(p => !p.finalizado).length;
    
    document.getElementById('partidos-finalizados').textContent = finalizados;
    document.getElementById('partidos-pendientes').textContent = pendientes;
    
    // Obtener total de predicciones (esto requeriría otra llamada API, por ahora dejamos en 0)
    // document.getElementById('total-predicciones').textContent = totalPredicciones;
}
