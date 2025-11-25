// ========================================
// SIMULADOR DEL MUNDIAL - mundial.js
// ========================================

let currentFase = 'grupo';
let currentGrupo = 'A';
let currentJornada = 1;
let partidosData = [];

// ========================================
// INICIALIZACIÓN
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    initEventListeners();
    loadPartidosJornada();
});

function initEventListeners() {
    // Select de fase
    document.getElementById('fase-select').addEventListener('change', function() {
        currentFase = this.value;
        
        // Mostrar/ocultar selectores de grupo y jornada
        if (currentFase === 'grupo') {
            document.getElementById('grupo-select').style.display = 'block';
            document.getElementById('jornada-select').style.display = 'block';
        } else {
            document.getElementById('grupo-select').style.display = 'none';
            document.getElementById('jornada-select').style.display = 'none';
        }
        
        loadPartidosJornada();
    });
    
    // Select de grupo
    document.getElementById('grupo-select').addEventListener('change', function() {
        currentGrupo = this.value;
        loadPartidosJornada();
    });
    
    // Select de jornada
    document.getElementById('jornada-select').addEventListener('change', function() {
        currentJornada = parseInt(this.value);
        loadPartidosJornada();
    });
    
    // Botón guardar todas las predicciones
    document.getElementById('save-all-predictions').addEventListener('click', saveAllPredictions);
}

// ========================================
// CARGAR PARTIDOS DE LA JORNADA
// ========================================

function loadPartidosJornada() {
    let url = `../php/mundial.php?action=get_partidos_fase&fase=${currentFase}`;
    
    if (currentFase === 'grupo') {
        url += `&jornada=${currentJornada}`;
    }
    
    fetch(url)
        .then(res => res.json())
        .then(partidos => {
            partidosData = partidos;
            renderPartidosJornada(partidos);
            updateTitle();
        })
        .catch(error => {
            console.error('Error al cargar partidos:', error);
            document.getElementById('partidos-jornada-list').innerHTML = 
                '<div class="error-message">Error loading matches</div>';
        });
}

function updateTitle() {
    const faseNombres = {
        'grupo': `MATCHDAY ${currentJornada} - GROUPS`,
        'octavos': 'ROUND OF 16',
        'cuartos': 'QUARTERFINALS',
        'semifinal': 'SEMIFINALS',
        'final': 'FINAL'
    };
    
    document.getElementById('jornada-title').textContent = faseNombres[currentFase] || currentFase.toUpperCase();
}

function renderPartidosJornada(partidos) {
    const container = document.getElementById('partidos-jornada-list');
    
    if (partidos.length === 0) {
        container.innerHTML = '<div class="empty-message">No matches available</div>';
        return;
    }
    
    container.innerHTML = partidos.map(partido => {
        const finalizado = partido.finalizado;
        const hasPrediccion = partido.prediccion !== null;
        
        return `
            <div class="partido-card ${finalizado ? 'finalizado' : ''}" data-partido-id="${partido.id}">
                <div class="partido-header">
                    <div class="equipos">
                        <div class="equipo">
                            <img src="${partido.local.bandera || 'https://via.placeholder.com/50'}" alt="${partido.local.nombre}">
                            <span>${partido.local.nombre}</span>
                        </div>
                        <span class="vs">VS</span>
                        <div class="equipo">
                            <img src="${partido.visitante.bandera || 'https://via.placeholder.com/50'}" alt="${partido.visitante.nombre}">
                            <span>${partido.visitante.nombre}</span>
                        </div>
                    </div>
                    <div class="partido-info">
                        <span class="estadio">${partido.estadio}</span>
                        <span class="fecha">${formatFecha(partido.fecha)}</span>
                    </div>
                </div>
                
                ${finalizado ? renderResultado(partido) : renderFormulario(partido)}
            </div>
        `;
    }).join('');
    
    // Agregar event listeners para inputs de penales
    addPenalesListeners();
}

function renderResultado(partido) {
    const prediccion = partido.prediccion;
    
    return `
        <div class="partido-resultado">
            <div class="resultado-final">
                <h4>FINAL RESULT</h4>
                <div class="score-display">
                    <span class="score-big">${partido.local.goles}</span>
                    <span>-</span>
                    <span class="score-big">${partido.visitante.goles}</span>
                </div>
                ${partido.local.penales !== null ? `
                    <div class="penales-display">
                        Penalties: ${partido.local.penales} - ${partido.visitante.penales}
                    </div>
                ` : ''}
            </div>
            
            ${prediccion ? `
                <div class="tu-prediccion ${prediccion.puntos > 0 ? 'correcta' : 'incorrecta'}">
                    <h4>YOUR PREDICTION</h4>
                    <div class="score-display">
                        <span>${prediccion.local}</span>
                        <span>-</span>
                        <span>${prediccion.visitante}</span>
                    </div>
                    ${prediccion.penales_local !== null ? `
                        <div class="penales-display">
                            Penalties: ${prediccion.penales_local} - ${prediccion.penales_visitante}
                        </div>
                    ` : ''}
                    <div class="puntos ${prediccion.puntos > 0 ? 'ganados' : 'perdidos'}">
                        ${prediccion.puntos > 0 ? `+${prediccion.puntos} points ✓` : 'No points ✗'}
                    </div>
                </div>
            ` : `
                <div class="no-prediccion">
                    You didn't make a prediction
                </div>
            `}
        </div>
    `;
}

function renderFormulario(partido) {
    const prediccion = partido.prediccion;
    const localVal = prediccion ? prediccion.local : 0;
    const visitanteVal = prediccion ? prediccion.visitante : 0;
    const penLocalVal = prediccion?.penales_local || 0;
    const penVisitanteVal = prediccion?.penales_visitante || 0;
    
    return `
        <div class="partido-prediccion">
            <div class="prediction-inputs-inline">
                <div class="input-group">
                    <label>${partido.local.codigo}</label>
                    <input type="number" 
                           class="pred-input" 
                           data-tipo="local" 
                           min="0" max="20" 
                           value="${localVal}">
                </div>
                <span class="separator">-</span>
                <div class="input-group">
                    <label>${partido.visitante.codigo}</label>
                    <input type="number" 
                           class="pred-input" 
                           data-tipo="visitante" 
                           min="0" max="20" 
                           value="${visitanteVal}">
                </div>
            </div>
            
            ${currentFase !== 'grupo' ? `
                <div class="penales-inputs" style="display:${localVal === visitanteVal ? 'flex' : 'none'};">
                    <label>Penalties:</label>
                    <input type="number" 
                           class="pen-input" 
                           data-tipo="pen-local" 
                           min="0" max="10" 
                           value="${penLocalVal}">
                    <span>-</span>
                    <input type="number" 
                           class="pen-input" 
                           data-tipo="pen-visitante" 
                           min="0" max="10" 
                           value="${penVisitanteVal}">
                </div>
            ` : ''}
            
            ${prediccion ? '<div class="prediction-status">✓ Prediction saved</div>' : ''}
        </div>
    `;
}

function addPenalesListeners() {
    document.querySelectorAll('.partido-card').forEach(card => {
        const inputs = card.querySelectorAll('.pred-input');
        const penalesDiv = card.querySelector('.penales-inputs');
        
        if (penalesDiv && currentFase !== 'grupo') {
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    const localInput = card.querySelector('[data-tipo="local"]');
                    const visitanteInput = card.querySelector('[data-tipo="visitante"]');
                    
                    if (parseInt(localInput.value) === parseInt(visitanteInput.value)) {
                        penalesDiv.style.display = 'flex';
                    } else {
                        penalesDiv.style.display = 'none';
                    }
                });
            });
        }
    });
}

// ========================================
// GUARDAR TODAS LAS PREDICCIONES
// ========================================

function saveAllPredictions() {
    const predicciones = [];
    let hasErrors = false;
    
    document.querySelectorAll('.partido-card:not(.finalizado)').forEach(card => {
        const partidoId = parseInt(card.dataset.partidoId);
        const localInput = card.querySelector('[data-tipo="local"]');
        const visitanteInput = card.querySelector('[data-tipo="visitante"]');
        
        if (!localInput || !visitanteInput) return;
        
        const golesLocal = parseInt(localInput.value);
        const golesVisitante = parseInt(visitanteInput.value);
        
        let penalesLocal = null;
        let penalesVisitante = null;
        
        // Verificar penales si es fase eliminatoria y hay empate
        if (currentFase !== 'grupo' && golesLocal === golesVisitante) {
            const penLocalInput = card.querySelector('[data-tipo="pen-local"]');
            const penVisitanteInput = card.querySelector('[data-tipo="pen-visitante"]');
            
            if (penLocalInput && penVisitanteInput) {
                penalesLocal = parseInt(penLocalInput.value);
                penalesVisitante = parseInt(penVisitanteInput.value);
                
                if (penalesLocal === penalesVisitante) {
                    hasErrors = true;
                    alert(`In penalties there must be a winner for match ${partidoId}`);
                    return;
                }
            }
        }
        
        predicciones.push({
            partido_id: partidoId,
            goles_local: golesLocal,
            goles_visitante: golesVisitante,
            penales_local: penalesLocal,
            penales_visitante: penalesVisitante
        });
    });
    
    if (hasErrors) return;
    
    if (predicciones.length === 0) {
        alert('No predictions to save');
        return;
    }
    
    // Enviar todas las predicciones
    Promise.all(predicciones.map(pred => 
        fetch('../php/mundial.php?action=save_prediccion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(pred)
        }).then(res => res.json())
    ))
    .then(responses => {
        const allSuccess = responses.every(r => r.success);
        
        if (allSuccess) {
            alert(`✅ ${predicciones.length} predictions saved successfully!`);
            loadPartidosJornada(); // Recargar
        } else {
            alert('❌ Some predictions failed to save');
        }
    })
    .catch(error => {
        console.error('Error saving predictions:', error);
        alert('❌ Error saving predictions');
    });
}

// ========================================
// UTILIDADES
// ========================================

function formatFecha(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}

