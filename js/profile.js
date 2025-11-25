// ========================================
// PROFILE.JS - Cargar datos del usuario y predicciones
// ========================================

console.log('✅ Profile.js cargado correctamente');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado, iniciando funciones...');
    loadUserData();
    loadUserStats();
    loadUserPredictions();
    loadTopPredictions();
    loadMedallas();
});

// Cargar datos del usuario
function loadUserData() {
    fetch('../php/session_check.php')
        .then(res => res.json())
        .then(data => {
            console.log('User data:', data); // Para debug
            
            if (data.loggedIn && data.username) {
                // Guardar el user_id en sessionStorage para las otras funciones
                sessionStorage.setItem('user_id', data.id);
                
                document.getElementById('username').innerHTML = 
                    data.username + ' <span class="settings">⚙️</span>';
                
                // Mostrar botón admin si es user_id = 1
                if (data.id == 1) {
                    document.getElementById('admin-btn').style.display = 'block';
                }
                
                const avatar = document.getElementById('user-avatar');
                if (data.foto_perfil && data.foto_perfil !== 'null' && data.foto_perfil !== '') {
                    avatar.style.backgroundImage = `url(../pictures/${data.foto_perfil})`;
                    avatar.style.backgroundSize = 'cover';
                    avatar.style.backgroundPosition = 'center';
                } else {
                    // Mantener el avatar por defecto del CSS
                    console.log('No profile picture set');
                }
            } else {
                console.error('User not logged in');
                window.location.href = 'login.html';
            }
        })
        .catch(error => {
            console.error('Error al cargar datos del usuario:', error);
        });
}

// Cargar estadísticas del usuario
function loadUserStats() {
    const userId = sessionStorage.getItem('user_id');
    
    fetch(`../php/mundial.php?action=get_user_stats&usuario_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Stats data:', data); // Para debug
            
            if (data.success) {
                document.getElementById('total-predicciones').textContent = data.stats.total_predicciones || 0;
                document.getElementById('puntos-totales').textContent = data.stats.puntos_totales || 0;
                
                const precision = data.stats.total_predicciones > 0 
                    ? Math.round((data.stats.predicciones_exactas / data.stats.total_predicciones) * 100)
                    : 0;
                document.getElementById('precision').textContent = precision + '%';
            }
        })
        .catch(error => {
            console.error('Error al cargar estadísticas:', error);
        });
}

// Cargar todas las predicciones del usuario
function loadUserPredictions() {
    const userId = sessionStorage.getItem('user_id');
    
    fetch(`../php/mundial.php?action=get_mis_predicciones&usuario_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Predicciones data:', data); // Para debug
            
            const container = document.getElementById('predicciones-list');
            
            if (!data.success || !data.predicciones || data.predicciones.length === 0) {
                container.innerHTML = '<div class="row empty">No has hecho predicciones aún</div>';
                return;
            }
            
            container.innerHTML = data.predicciones.map(pred => {
                const finalizado = pred.resultado !== null;
                const puntos = pred.puntos || 0;
                
                const status = finalizado 
                    ? (puntos > 0 ? 'correcta' : 'incorrecta') 
                    : 'pendiente';
                
                const statusText = status === 'correcta' ? 'Correcta' : 
                                 status === 'incorrecta' ? 'Incorrecta' : 'Pendiente';
                
                const icon = finalizado 
                    ? (puntos > 0 ? '✔' : '✘') 
                    : '⏱';
                
                let predictionText = `${pred.prediccion.local}-${pred.prediccion.visitante}`;
                if (pred.prediccion.penales_local !== null) {
                    predictionText += ` (Pen: ${pred.prediccion.penales_local}-${pred.prediccion.penales_visitante})`;
                }
                
                const puntosText = puntos > 0 ? `+${puntos}` : 
                                 (finalizado ? '0' : '—');
                
                return `
                    <div class="row ${status}">
                        <span class="icon">${icon}</span>
                        <span class="status ${status}">${statusText}</span>
                        <div class="match-info">
                            <span class="teams">${pred.equipos.local} VS ${pred.equipos.visitante}</span>
                            <span class="prediction">
                                Tu predicción: <span class="prediction-score">${predictionText}</span>
                            </span>
                        </div>
                        <span class="points ${status === 'pendiente' ? 'pendiente' : (puntos > 0 ? 'ganados' : 'perdidos')}">${puntosText} pts</span>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error al cargar predicciones:', error);
            document.getElementById('predicciones-list').innerHTML = 
                '<div class="row error">Error al cargar predicciones</div>';
        });
}

// Cargar las mejores predicciones (las que más puntos ganaron)
function loadTopPredictions() {
    const userId = sessionStorage.getItem('user_id');
    
    fetch(`../php/mundial.php?action=get_mis_predicciones&usuario_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Top predictions data:', data); // Para debug
            
            const container = document.getElementById('history-items');
            
            if (!data.success || !data.predicciones) {
                container.innerHTML = '<div class="item">No tienes predicciones acertadas aún</div>';
                return;
            }
            
            // Filtrar solo las que ganaron puntos y ordenar por puntos
            const topPredictions = data.predicciones
                .filter(p => p.puntos > 0)
                .sort((a, b) => b.puntos - a.puntos)
                .slice(0, 5);
            
            if (topPredictions.length === 0) {
                container.innerHTML = '<div class="item">No tienes predicciones acertadas aún</div>';
                return;
            }
            
            container.innerHTML = topPredictions.map((pred, index) => {
                let predText = `${pred.prediccion.local}-${pred.prediccion.visitante}`;
                if (pred.prediccion.penales_local !== null) {
                    predText += ` (Pen: ${pred.prediccion.penales_local}-${pred.prediccion.penales_visitante})`;
                }
                
                return `
                    <div class="item">
                        <span class="rank">#${index + 1}</span>
                        <span class="match">${pred.equipos.local} vs ${pred.equipos.visitante}</span>
                        <span class="score">${predText}</span>
                        <span class="points">+${pred.puntos} pts</span>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error al cargar top predicciones:', error);
        });
}

// Cargar medallas del usuario
function loadMedallas() {
    const userId = sessionStorage.getItem('user_id');
    
    fetch(`../php/mundial.php?action=get_medallas&usuario_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Medallas data:', data);
            
            const container = document.getElementById('medallas-list');
            
            if (!data.success || !data.medallas || data.medallas.length === 0) {
                container.innerHTML = '<div class="empty">Aún no has ganado medallas. ¡Sigue jugando!</div>';
                return;
            }
            
            container.innerHTML = data.medallas.map(medalla => {
                const fecha = new Date(medalla.fecha_obtencion).toLocaleDateString('es-MX');
                
                return `
                    <div class="medalla-item" title="${medalla.descripcion}">
                        <img src="${medalla.icono}" alt="${medalla.nombre}">
                        <span class="medalla-nombre">${medalla.nombre}</span>
                        <span class="medalla-descripcion">${medalla.descripcion}</span>
                        <span class="medalla-fecha">${fecha}</span>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error al cargar medallas:', error);
            document.getElementById('medallas-list').innerHTML = 
                '<div class="empty">Error al cargar medallas</div>';
        });
}
