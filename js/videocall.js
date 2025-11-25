// ========== VIDEOLLAMADA CON WEBRTC + SOCKET.IO ==========

class VideoCallManager {
    constructor() {
        this.socket = null;
        this.localStream = null;
        this.peerConnection = null;
        this.roomId = null;
        this.remoteSocketId = null;
        this.serverIp = null;
        this.userRegistered = false; // Flag para evitar re-registros
        this.isCallInitiator = false; // Flag para saber si iniciamos la llamada
        this.activeCallRoomId = null; // Track de la llamada activa
        this.offerSent = false; // Flag para evitar enviar oferta múltiples veces
        this.localVideo = document.getElementById('localVideo');
        this.remoteVideo = document.getElementById('remoteVideo');
        this.videoCallModal = document.getElementById('videoCallModal');
        this.callStatus = document.getElementById('callStatus');
        this.startCallBtn = document.getElementById('startCallBtn');
        this.stopCallBtn = document.getElementById('stopCallBtn');
        this.closeVideoCallBtn = document.getElementById('closeVideoCallBtn');
        this.videoCallBtn = document.getElementById('videoCallBtn');
        this.videoCallContact = document.getElementById('videoCallContact');

        // Configuración STUN
        this.rtcConfig = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };

        // Método para intentar registrar el usuario (espera a que MY_USER_ID esté disponible)
        this.attemptUserRegistration = () => {
            // Evitar re-registros si ya se registró exitosamente
            if (this.userRegistered) {
                console.log('✅ Ya registrado, ignorando intento adicional');
                return;
            }
            
            if (typeof MY_USER_ID !== 'undefined' && MY_USER_ID !== null && MY_USER_ID !== 0) {
                if (this.socket && this.socket.connected) {
                    this.socket.emit('register-user', MY_USER_ID);
                    this.userRegistered = true;
                    console.log('🔐 Usuario registrado:', MY_USER_ID);
                } else {
                    console.log('⏳ Socket no conectado, reintentando en 500ms...');
                    setTimeout(() => this.attemptUserRegistration(), 500);
                }
            } else {
                // Reintentar cada 500ms hasta que MY_USER_ID esté disponible
                console.log('⏳ MY_USER_ID no disponible (', MY_USER_ID, '), reintentando...');
                setTimeout(() => this.attemptUserRegistration(), 500);
            }
        };

        this.setupEventListeners();
        this.initializeSocket();
    }

    initializeSocket() {
        // Intentar conectar al servidor de socket.io (opcional)
        if (typeof io !== 'undefined') {
            // Detectar si está en localhost o accediendo remotamente
            const isRemote = !window.location.hostname.includes('localhost') && 
                           !window.location.hostname.includes('127.0.0.1');
            
            // Si es remoto, usar la IP Hamachi; si es local, usar localhost
            const socketUrl = isRemote ? 'http://25.7.206.194:3001' : 'http://localhost:3001';
            
            console.log(`🔗 Conectando a servidor: ${socketUrl}`);
            
            this.socket = io(socketUrl, {
                reconnection: true,
                reconnectionDelay: 1000,
                reconnectionDelayMax: 5000,
                reconnectionAttempts: 3
            });

            this.socket.on('connect', () => {
                console.log('✅ Conectado al servidor de videollamada');
                // Registrar el usuario con su ID en el servidor (con reintentos)
                this.attemptUserRegistration();
            });

            this.socket.on('connection-success', (data) => {
                this.serverIp = data.serverIp;
                console.log('✅ Conectado al servidor de videollamada');
                console.log('📍 IP del servidor:', this.serverIp);
            });

            this.socket.on('offer', async (data) => {
                console.log('📥 Oferta recibida en receptor');
                this.remoteSocketId = data.sender;
                
                // Esperar a que localStream esté disponible (por si la oferta llega antes de getUserMedia)
                let attempts = 0;
                while (!this.localStream && attempts < 50) {
                    console.log('⏳ Esperando localStream... intento', attempts + 1);
                    await new Promise(resolve => setTimeout(resolve, 100));
                    attempts++;
                }
                
                if (!this.localStream) {
                    console.error('❌ localStream no disponible después de 5 segundos');
                    return;
                }
                
                if (!this.peerConnection) {
                    console.log('🔌 No hay peerConnection, creando...');
                    this.createPeerConnection();
                }
                
                try {
                    console.log('📋 Estableciendo remoteDescription (offer)');
                    await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer));
                    console.log('✅ remoteDescription establecida');
                    
                    console.log('🎤 Creando respuesta (answer)');
                    const answer = await this.peerConnection.createAnswer();
                    console.log('✅ Answer creado');
                    
                    await this.peerConnection.setLocalDescription(answer);
                    console.log('✅ LocalDescription (answer) establecida');
                    
                    console.log('📤 Enviando answer al servidor');
                    this.socket.emit('answer', {
                        answer: answer,
                        room: this.roomId
                    });
                    console.log('✅ Answer enviado');
                } catch (error) {
                    console.error('❌ Error procesando oferta:', error);
                }
            });

            this.socket.on('answer', async (data) => {
                console.log('📨 Respuesta recibida');
                console.log('📍 data.sender:', data.sender);
                console.log('📍 this.remoteSocketId:', this.remoteSocketId);
                console.log('📍 this.peerConnection existe:', !!this.peerConnection);
                
                if (!this.peerConnection) {
                    console.error('❌ peerConnection no existe al recibir answer');
                    return;
                }
                
                if (data.sender !== this.remoteSocketId) {
                    console.warn('⚠️ Answer de socket incorrecto, ignorando');
                    return;
                }
                try {
                    console.log('🔄 Estableciendo remoteDescription (answer)');
                    await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
                    console.log('✅ RemoteDescription (answer) establecida');
                } catch (error) {
                    console.error('❌ Error procesando respuesta:', error);
                }
            });

            this.socket.on('ice-candidate', async (data) => {
                // No verificar sender porque podría no estar establecido aún
                // Confiamos en que estamos en la sala correcta
                try {
                    if (data.candidate) {
                        console.log('❄️ Agregando ICE candidate');
                        await this.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
                    }
                } catch (error) {
                    console.error('Error agregando candidato ICE:', error);
                }
            });

            this.socket.on('user-joined', async (data) => {
                console.log('👤 Otro usuario se unió:', data.userId);
                this.remoteSocketId = data.socketId;
                // La oferta se envía en el handler de 'video-call-accepted', no aquí
            });

            this.socket.on('user-disconnected', () => {
                console.log('🔴 Otro usuario se desconectó');
                this.callStatus.textContent = 'El otro usuario se desconectó';
                this.stopCall();
            });

            // Usuario no disponible
            this.socket.on('user-not-available', (data) => {
                console.log('⚠️ Usuario no disponible:', data.userId);
                this.callStatus.textContent = `Usuario ${data.userId} no está disponible`;
                setTimeout(() => {
                    this.closeModal();
                }, 2000);
            });

            // Recibir solicitud de videollamada entrante
            this.socket.on('video-call-request', (data) => {
                console.log('📞 Solicitud de videollamada de:', data.from);
                this.handleIncomingCall(data);
            });

            // Respuesta a solicitud de videollamada
            this.socket.on('video-call-accepted', async (data) => {
                console.log('✅ Llamada aceptada por usuario:', data.from);
                this.remoteSocketId = data.recipientSocketId;
                this.callStatus.textContent = '✅ Llamada aceptada - Conectando...';
                
                // Solo el receptor se une a la sala (el iniciador ya estó unido)
                if (!this.isCallInitiator) {
                    this.socket.emit('join-room', this.roomId, MY_USER_ID);
                    console.log('👥 Uniéndose a la sala (receptor):', this.roomId);
                }
                
                // SOLO el iniciador crea la oferta (y solo una vez)
                if (this.isCallInitiator && this.localStream && this.peerConnection && !this.offerSent) {
                    console.log('📤 Enviando oferta WebRTC (iniciador)');
                    this.offerSent = true; // Marcar que ya se envió
                    try {
                        const offer = await this.peerConnection.createOffer();
                        await this.peerConnection.setLocalDescription(offer);
                        this.socket.emit('offer', {
                            offer: offer,
                            room: this.roomId
                        });
                    } catch (error) {
                        console.error('Error creando oferta:', error);
                        this.offerSent = false; // Resetear si hay error
                    }
                } else {
                    if (this.offerSent) {
                        console.log('⏳ Oferta ya fue enviada, esperando respuesta...');
                    } else {
                        console.log('⏳ Esperando oferta del iniciador (receptor)');
                    }
                }
            });

            this.socket.on('video-call-rejected', (data) => {
                console.log('❌ Llamada rechazada');
                this.callStatus.textContent = '❌ Llamada rechazada';
                this.stopCall();
            });

            this.socket.on('connect_error', (error) => {
                console.warn('⚠️ Error de conexión con socket.io:', error);
                this.callStatus.textContent = 'Funcionando en modo local (sin servidor)';
            });
        } else {
            console.warn('Socket.io no está disponible. Funcionando en modo local.');
        }
    }

    setupEventListeners() {
        this.startCallBtn.addEventListener('click', () => this.startCall());
        this.stopCallBtn.addEventListener('click', () => this.stopCall());
        this.closeVideoCallBtn.addEventListener('click', () => this.closeModal());
        this.videoCallBtn.addEventListener('click', () => {
            console.log('🔘 Click en botón de videollamada');
            this.openVideoCallModal();
        });
    }

    openVideoCallModal() {
        console.log('📹 openVideoCallModal llamado');
        console.log('currentContactId:', currentContactId);
        console.log('contactsCache:', contactsCache);
        
        if (!currentContactId) {
            alert('Por favor selecciona un contacto primero');
            return;
        }
        
        const contactName = contactsCache[currentContactId]?.username || 'Usuario';
        this.videoCallContact.textContent = contactName;
        
        // Mostrar el modal
        this.videoCallModal.style.display = 'flex';
        
        this.callStatus.textContent = 'Preparando videollamada...';
        this.startCallBtn.style.display = 'block';
        this.stopCallBtn.style.display = 'none';
        
        console.log('✅ Modal abierto para:', contactName);
    }

    closeModal() {
        this.videoCallModal.style.display = 'none';
        this.stopCall();
    }

    handleIncomingCall(data) {
        // Mostrar notificación de llamada entrante
        const caller = contactsCache[data.from]?.username || 'Usuario ' + data.from;
        
        // Crear notificación visual
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            z-index: 2000;
            max-width: 350px;
            font-family: Arial, sans-serif;
        `;
        
        notification.innerHTML = `
            <div style="margin-bottom: 10px; font-weight: bold; font-size: 16px;">
                📞 Llamada entrante de <strong>${caller}</strong>
            </div>
            <div style="display:flex; gap:10px;">
                <button id="acceptCallBtn" style="
                    flex:1;
                    padding: 10px;
                    background: #4CAF50;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                ">
                    ✅ Aceptar
                </button>
                <button id="rejectCallBtn" style="
                    flex:1;
                    padding: 10px;
                    background: #f44336;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                ">
                    ❌ Rechazar
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Reproducir sonido de notificación (opcional)
        this.playNotificationSound();
        
        // Manejar aceptación
        document.getElementById('acceptCallBtn').addEventListener('click', () => {
            this.acceptIncomingCall(data);
            notification.remove();
        });
        
        // Manejar rechazo
        document.getElementById('rejectCallBtn').addEventListener('click', () => {
            this.rejectIncomingCall(data);
            notification.remove();
        });
        
        // Auto-desaparecer después de 10 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }

    acceptIncomingCall(data) {
        console.log('📞 Aceptando llamada de:', data.from);
        
        // Evitar procesar la misma llamada dos veces
        if (this.activeCallRoomId === data.roomId) {
            console.log('⚠️ Esta llamada ya está siendo procesada');
            return;
        }
        
        this.activeCallRoomId = data.roomId;
        this.roomId = data.roomId;
        this.remoteSocketId = data.callerSocketId;
        this.isCallInitiator = false; // NO somos quien inicia, estamos recibiendo
        
        // Abrir modal
        const contactName = contactsCache[data.from]?.username || 'Usuario';
        this.videoCallContact.textContent = contactName;
        this.videoCallModal.style.display = 'flex';
        this.callStatus.textContent = 'Aceptando llamada...';
        this.startCallBtn.style.display = 'none';
        this.stopCallBtn.style.display = 'block';
        
        // Unirse a la sala
        if (this.socket && this.socket.connected) {
            this.socket.emit('join-room', this.roomId, MY_USER_ID);
            console.log('👥 Uniéndose a la sala:', this.roomId);
        }
        
        // Iniciar cámara (pero NO crear oferta)
        this.startCallForIncomingAcceptance();
        
        // Notificar al otro usuario que aceptaste
        if (this.socket && this.socket.connected) {
            this.socket.emit('video-call-accepted', {
                to: data.from,
                roomId: this.roomId,
                socketId: this.socket.id
            });
        }
    }

    startCallForIncomingAcceptance() {
        // Similar a startCall() pero sin iniciar la oferta
        console.log('🎯 startCallForIncomingAcceptance() ejecutado');
        try {
            this.callStatus.textContent = '🎥 Solicitando acceso a cámara y micrófono...';
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Tu navegador no soporta acceso a cámara/micrófono. Usa Chrome, Firefox o Edge.');
            }
            
            const constraints = { 
                video: { 
                    width: { ideal: 640 }, 
                    height: { ideal: 480 } 
                },
                audio: true 
            };
            
            navigator.mediaDevices.getUserMedia(constraints)
                .then(stream => {
                    console.log('✅ localStream obtenido en receptor');
                    this.localStream = stream;
                    this.localVideo.srcObject = this.localStream;
                    this.callStatus.textContent = '✅ Cámara activada. Esperando conexión...';
                    
                    if (!this.peerConnection) {
                        console.log('🔌 Creando peerConnection en receptor');
                        this.createPeerConnection();
                    }
                })
                .catch(error => {
                    console.error('❌ Error en getUserMedia en receptor:', error);
                    this.handleGetUserMediaError(error);
                });
            
        } catch (error) {
            console.error('Error en videollamada:', error);
            this.callStatus.textContent = error.message;
            alert(error.message);
        }
    }

    rejectIncomingCall(data) {
        console.log('❌ Rechazando llamada de:', data.from);
        
        // Notificar al otro usuario que rechazaste
        if (this.socket && this.socket.connected) {
            this.socket.emit('video-call-rejected', {
                to: data.from,
                roomId: data.roomId
            });
        }
    }

    playNotificationSound() {
        // Crear un sonido simple usando Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('No se pudo reproducir sonido');
        }
    }

    async startCall() {
        try {
            this.callStatus.textContent = '🎥 Solicitando acceso a cámara y micrófono...';
            this.isCallInitiator = true; // Somos quienes iniciamos la llamada
            
            // Verificar soporte de getUserMedia
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Tu navegador no soporta acceso a cámara/micrófono. Usa Chrome, Firefox o Edge.');
            }
            
            const constraints = { 
                video: { 
                    width: { ideal: 640 }, 
                    height: { ideal: 480 } 
                },
                audio: true 
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            this.localVideo.srcObject = this.localStream;
            
            this.callStatus.textContent = '✅ Cámara activada. Conectando...';
            this.startCallBtn.style.display = 'none';
            this.stopCallBtn.style.display = 'block';
            
            // Crear conexión P2P
            this.createPeerConnection();
            
            // Generar ID de sala único
            this.roomId = [MY_USER_ID, currentContactId].sort().join('-');
            
            // Conectarse a la sala si hay socket
            if (this.socket && this.socket.connected) {
                // Unirse a la sala primero
                this.socket.emit('join-room', this.roomId, MY_USER_ID);
                console.log('👥 Uniéndose a la sala:', this.roomId);
                
                // Enviar solicitud de videollamada
                console.log('📞 Enviando solicitud de videollamada:', {
                    from: MY_USER_ID,
                    to: currentContactId,
                    roomId: this.roomId
                });
                this.socket.emit('video-call-request', {
                    from: MY_USER_ID,
                    to: currentContactId,
                    roomId: this.roomId
                });
                this.callStatus.textContent = '⏳ Esperando que acepte la llamada...';
            } else {
                console.error('❌ Socket no conectado o no disponible');
                console.log('Socket:', this.socket);
                console.log('Connected:', this.socket?.connected);
                this.callStatus.textContent = '❌ Error: Sin conexión al servidor';
            }
            
        } catch (error) {
            this.handleGetUserMediaError(error);
        }
    }

    handleGetUserMediaError(error) {
        console.error('Error en videollamada:', error);
        
        let mensajeError = error.message;
        let codigoError = error.name;
        
        if (error.name === 'NotAllowedError') {
            mensajeError = '❌ ACCESO DENEGADO\n\nDebes permitir acceso a cámara y micrófono.\n\nHaz clic en 🔒 Candado > Cambiar permisos';
        } else if (error.name === 'NotFoundError') {
            mensajeError = '❌ DISPOSITIVO NO ENCONTRADO\n\nTu computadora no tiene cámara o micrófono conectados.\n\nVerifica que estén conectados.';
        } else if (error.name === 'NotSecureError') {
            mensajeError = '❌ CONEXIÓN NO SEGURA\n\nSi accedes desde IP Hamachi (25.x.x.x):\n1. Ve a chrome://flags/#unsafely-treat-insecure-origin-as-secure\n2. Agrega tu IP\n3. Reinicia Chrome';
        } else if (error.name === 'NotReadableError') {
            mensajeError = '❌ CÁMARA EN USO\n\nOtra aplicación o navegador está usando la cámara.\n\nCierra otras aplicaciones e intenta de nuevo.';
        } else if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            mensajeError = '❌ NAVEGADOR NO COMPATIBLE\n\nTu navegador no soporta videollamadas.\n\nUsa Chrome, Firefox o Edge (versiones recientes).\n\nVe a: https://www.google.com/chrome/';
            codigoError = 'UnsupportedBrowser';
        }
        
        this.callStatus.innerHTML = `
            <div style="color: #ff6b6b; padding: 10px; background: #ffe0e0; border-radius: 5px; font-size: 14px;">
                ${mensajeError}<br><br>
                <a href="../diagnostico_videocamara.html" target="_blank" style="color: #0066cc; text-decoration: underline; font-weight: bold;">
                    → Abre el Diagnóstico de Cámara para más ayuda
                </a>
            </div>
        `;
        
        alert(mensajeError + '\n\nCódigo: ' + codigoError);
    }

    createPeerConnection() {
        if (this.peerConnection) {
            console.log('⚠️ peerConnection ya existe, retornando');
            return;
        }

        console.log('🔌 Creando RTCPeerConnection');
        this.peerConnection = new RTCPeerConnection(this.rtcConfig);

        // Añadir tracks de video y audio
        if (this.localStream) {
            console.log('📡 Añadiendo tracks locales:', this.localStream.getTracks().length);
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
        } else {
            console.warn('⚠️ localStream no disponible al crear peerConnection');
        }

        // Recibir video remoto
        this.peerConnection.ontrack = (event) => {
            console.log('📹 ontrack disparado!');
            console.log('   - streams.length:', event.streams.length);
            console.log('   - track.kind:', event.track.kind);
            console.log('   - track.enabled:', event.track.enabled);
            
            if (event.streams && event.streams.length > 0) {
                console.log('📹 Video remoto recibido, streams:', event.streams.length);
                console.log('   - Setting srcObject:', event.streams[0]);
                this.remoteVideo.srcObject = event.streams[0];
                this.callStatus.textContent = '✅ Videollamada en progreso';
            } else {
                console.warn('⚠️ ontrack disparado pero sin streams');
            }
        };

        // Manejar candidatos ICE
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate && this.socket) {
                console.log('❄️ Enviando ICE candidate');
                this.socket.emit('ice-candidate', {
                    candidate: event.candidate,
                    room: this.roomId
                });
            }
        };

        // Monitorear estado de conexión
        this.peerConnection.onconnectionstatechange = () => {
            const state = this.peerConnection.connectionState;
            console.log('🔗 Estado de conexión:', state);
            
            if (state === 'connected' || state === 'completed') {
                this.callStatus.textContent = '✅ Conectado - Videollamada activa';
            } else if (state === 'disconnected' || state === 'failed') {
                this.callStatus.textContent = '⚠️ Conexión perdida';
            }
        };
    }

    stopCall() {
        try {
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
                this.localStream = null;
                this.localVideo.srcObject = null;
            }

            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }

            this.remoteVideo.srcObject = null;
            this.startCallBtn.style.display = 'block';
            this.stopCallBtn.style.display = 'none';
            this.callStatus.textContent = 'Videollamada finalizada';
            this.activeCallRoomId = null; // Resetear la llamada activa
            this.offerSent = false; // Resetear flag de oferta para próximas llamadas
            this.isCallInitiator = false; // Resetear flag de iniciador
        } catch (error) {
            console.error('Error al detener llamada:', error);
        }
    }
}
