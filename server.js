const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});
const os = require('os');

// Servir archivos estáticos
app.use(express.static('public'));

// Obtener IP local (preferiblemente Hamachi)
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            // Buscar IPv4 y saltar localhost
            if (iface.family === 'IPv4' && !iface.internal) {
                // Preferir IPs Hamachi (25.x.x.x)
                if (iface.address.startsWith('25.')) {
                    return iface.address;
                }
            }
        }
    }
    // Si no encontró Hamachi, devolver localhost
    return '127.0.0.1';
}

const SERVER_IP = getLocalIP();

// Almacenar salas activas y mapeo usuario-socket
const rooms = new Map();
const userSocketMap = new Map(); // Mapear userId a socketId

io.on('connection', (socket) => {
    console.log('🟢 Usuario conectado:', socket.id, 'desde:', socket.handshake.address);

    // Cuando un usuario se registra (proporcionando su userId)
    socket.on('register-user', (userId) => {
        userSocketMap.set(userId, socket.id);
        console.log(`🔐 Usuario ${userId} registrado con socket ${socket.id}`);
        socket.emit('registration-success', { userId, socketId: socket.id });
    });

    // Solicitud de videollamada
    socket.on('video-call-request', (data) => {
        console.log(`📞 Solicitud de llamada recibida:`, data);
        const recipientSocketId = userSocketMap.get(data.to);
        console.log(`🔍 Buscando usuario ${data.to} en mapa:`, userSocketMap);
        
        if (recipientSocketId) {
            console.log(`📞 Solicitud de llamada de usuario ${data.from} a usuario ${data.to}`);
            io.to(recipientSocketId).emit('video-call-request', {
                from: data.from,
                to: data.to,
                roomId: data.roomId,
                callerSocketId: socket.id
            });
        } else {
            console.log(`⚠️ Usuario ${data.to} no está conectado. Socket ID: ${socket.id}`);
            socket.emit('user-not-available', { userId: data.to });
        }
    });

    // Aceptación de llamada
    socket.on('video-call-accepted', (data) => {
        const callerSocketId = userSocketMap.get(data.to);
        
        if (callerSocketId) {
            console.log(`✅ Usuario ${data.to} aceptó la llamada de usuario actual`);
            io.to(callerSocketId).emit('video-call-accepted', {
                from: data.to,
                roomId: data.roomId,
                recipientSocketId: socket.id
            });
        }
    });

    // Rechazo de llamada
    socket.on('video-call-rejected', (data) => {
        const callerSocketId = userSocketMap.get(data.to);
        
        if (callerSocketId) {
            console.log(`❌ Usuario ${data.to} rechazó la llamada`);
            io.to(callerSocketId).emit('video-call-rejected', {
                from: data.to,
                roomId: data.roomId,
                reason: data.reason || 'rejected'
            });
        }
    });

    // Cuando un usuario se une a una sala
    socket.on('join-room', (roomId, userId) => {
        socket.join(roomId);
        rooms.set(roomId, (rooms.get(roomId) || 0) + 1);
        
        console.log(`👥 ${userId} se unió a la sala ${roomId}`);
        
        // Avisar a otros en la sala que hay un nuevo usuario
        socket.to(roomId).emit('user-joined', {
            userId: userId,
            socketId: socket.id
        });
    });

    // Retransmitir ofertas SDP
    socket.on('offer', (data) => {
        console.log(`📤 Oferta enviada a sala ${data.room}`);
        socket.to(data.room).emit('offer', {
            offer: data.offer,
            sender: socket.id
        });
    });

    // Retransmitir respuestas SDP
    socket.on('answer', (data) => {
        console.log(`📨 Respuesta enviada a sala ${data.room}`);
        socket.to(data.room).emit('answer', {
            answer: data.answer,
            sender: socket.id
        });
    });

    // Retransmitir candidatos ICE
    socket.on('ice-candidate', (data) => {
        socket.to(data.room).emit('ice-candidate', {
            candidate: data.candidate,
            sender: socket.id
        });
    });

    // Cuando un usuario se desconecta
    socket.on('disconnect', () => {
        console.log('🔴 Usuario desconectado:', socket.id);
        
        // Limpiar mapeo de usuario
        for (const [userId, socketId] of userSocketMap.entries()) {
            if (socketId === socket.id) {
                userSocketMap.delete(userId);
                console.log(`🧹 Limpiado mapeo para usuario ${userId}`);
                break;
            }
        }
        
        io.emit('user-disconnected', socket.id);
    });

    // Notificar si la conexión fue exitosa
    socket.emit('connection-success', { 
        socketId: socket.id,
        serverIp: SERVER_IP,
        message: 'Conectado al servidor de videollamada'
    });
});

const PORT = process.env.PORT || 3001;
http.listen(PORT, "0.0.0.0", () => {
    console.log(`✅ Servidor de videollamada corriendo en puerto ${PORT}`);
    console.log(`🌐 Dirección local: http://localhost:${PORT}`);
    console.log(`🌐 Dirección remota (Hamachi/Red): http://${SERVER_IP}:${PORT}`);
});
