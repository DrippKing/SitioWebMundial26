# Servidor de Videollamada

Este proyecto incluye un servidor Node.js/Express con Socket.io para facilitar videollamadas WebRTC entre usuarios.

## Instalación

1. Asegúrate de tener Node.js instalado (descarga desde https://nodejs.org/)

2. Abre una terminal en la carpeta del proyecto:
```bash
cd c:\xampp\htdocs\SitioWebMundial26
```

3. Instala las dependencias:
```bash
npm install
```

## Ejecución

Para iniciar el servidor de videollamada:
```bash
npm start
```

O directamente:
```bash
node server.js
```

El servidor se ejecutará en `http://localhost:3001`

## Características

- ✅ Videollamadas WebRTC P2P
- ✅ Servidor de señalización con Socket.io
- ✅ Manejo automático de candidatos ICE
- ✅ Detección de desconexiones
- ✅ Múltiples salas simultáneas

## Uso

1. Asegúrate que el servidor está corriendo en el puerto 3001
2. Abre la página de chats en tu navegador
3. Selecciona un contacto
4. Haz clic en el icono de videollamada
5. Presiona "Iniciar" para comenzar la videollamada
6. El otro usuario recibirá una notificación automáticamente

## Requisitos

- Node.js v14 o superior
- Navegador moderno con soporte WebRTC
- Cámara y micrófono habilitados

## Puertos

- **3001**: Servidor de videollamada (Socket.io)
- **3307**: Base de datos MySQL (XAMPP)
- **80**: Servidor web XAMPP

## Troubleshooting

Si la videollamada no funciona:
1. Verifica que el servidor está corriendo (npm start)
2. Abre la consola del navegador (F12) y revisa los errores
3. Comprueba que tienes permisos de cámara y micrófono
4. Intenta reiniciar el servidor

## Notas

- El servidor puede manejar múltiples videollamadas simultáneamente
- Las conexiones se pueden perder si se cierra el navegador
- Para desplegar en producción, usa un servidor HTTPS y certifica Socket.io
