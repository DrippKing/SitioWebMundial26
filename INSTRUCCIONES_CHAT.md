# Sistema de Chat - Instrucciones de Configuración

## ✅ Mejoras Implementadas

### 1. **JavaScript (chat.js)**
- ✅ Obtención dinámica del `user_id` desde el servidor
- ✅ Sistema de polling mejorado (cada 2 segundos)
- ✅ Prevención de duplicación de mensajes
- ✅ Mejor manejo de errores
- ✅ Avatar del usuario actual cargado dinámicamente
- ✅ Recarga automática después de enviar mensajes

### 2. **PHP (msg.php)**
- ✅ Retorna información del usuario actual (ID y avatar)
- ✅ Mejora en la respuesta de contactos

### 3. **CSS (chats.css)**
- ✅ Estilos diferenciados para mensajes enviados y recibidos
- ✅ Mensajes enviados: fondo beige (#D1C0A8) alineados a la derecha
- ✅ Mensajes recibidos: fondo blanco alineados a la izquierda

---

## 📋 Pasos para Activar el Chat

### Paso 1: Crear la tabla de mensajes
Ejecuta el siguiente SQL en phpMyAdmin (puerto 3307):

```sql
CREATE TABLE mensajes (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    sender_id INT(11) NOT NULL,
    receiver_id INT(11) NOT NULL,
    message_text TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_conversation (sender_id, receiver_id, timestamp),
    INDEX idx_timestamp (timestamp)
);
```

O simplemente importa el archivo: **TABLE MENSAJES.sql**

### Paso 2: Verificar la sesión
Asegúrate de que cuando el usuario inicia sesión en `login.php`, se guarde el `user_id` en la sesión:

```php
$_SESSION['user_id'] = $row['id']; // ID del usuario
$_SESSION['usuario'] = $row['usuario']; // Nombre de usuario
```

### Paso 3: Verificar imágenes de perfil
- Las fotos de perfil deben estar en: `pictures/`
- Por defecto usa: `default.jpg`
- Asegúrate de que existe el archivo `pictures/default.jpg`

### Paso 4: Probar el sistema
1. Inicia sesión con un usuario
2. Ve a `chats.html`
3. El sistema debería:
   - Cargar tus contactos automáticamente
   - Mostrar tu avatar en los mensajes enviados
   - Permitir enviar y recibir mensajes
   - Actualizar automáticamente cada 2 segundos

---

## 🔧 Funcionalidades del Chat

### ✅ Implementadas
- Envío de mensajes uno a uno
- Carga de contactos dinámicos desde la base de datos
- Visualización de conversaciones
- Auto-scroll al final de los mensajes
- Polling automático para nuevos mensajes
- Estilos diferenciados para mensajes enviados/recibidos
- Enter para enviar mensajes

### 🚀 Próximas Mejoras Sugeridas
1. **Notificaciones**: Badge con contador de mensajes no leídos
2. **Marca de leído**: Actualizar `is_read` cuando se abre una conversación
3. **Timestamp**: Mostrar hora de cada mensaje
4. **Typing indicator**: "Usuario está escribiendo..."
5. **Búsqueda de contactos**: Filtro en el panel lateral
6. **Adjuntar archivos**: Envío de imágenes
7. **Emojis**: Selector de emojis
8. **WebSockets**: Reemplazar polling por conexión en tiempo real

---

## 🐛 Solución de Problemas

### Error: "Usuario no autenticado"
- Verifica que `login.php` guarde correctamente `$_SESSION['user_id']`
- Revisa que `session_start()` esté al inicio de `msg.php`

### No cargan los contactos
- Verifica la conexión a la base de datos (puerto 3307)
- Revisa la consola del navegador (F12) para ver errores JavaScript
- Confirma que existen usuarios en la tabla `usuarios`

### No se envían mensajes
- Verifica que existe la tabla `mensajes`
- Revisa las claves foráneas (`sender_id` y `receiver_id`)
- Checa la consola del navegador

### Mensajes duplicados
- Ya está corregido con el sistema de `lastMessageCount`
- Si persiste, verifica que no haya múltiples intervalos de polling

---

## 📝 Estructura de Archivos

```
SitioWebMundial26/
├── html/
│   └── chats.html          ← Interfaz del chat
├── css/
│   └── chats.css           ← Estilos mejorados
├── js/
│   └── chat.js             ← Lógica mejorada
├── php/
│   ├── msg.php             ← API del chat (mejorada)
│   └── login.php           ← Debe guardar user_id en sesión
├── pictures/
│   └── default.jpg         ← Avatar por defecto
├── TABLE USUARIOS.txt      ← Estructura tabla usuarios
└── TABLE MENSAJES.sql      ← Estructura tabla mensajes (NUEVO)
```

---

## 💡 Notas Técnicas

- **Puerto MySQL**: 3307 (configurado en `msg.php`)
- **Polling**: Cada 2 segundos
- **Encoding**: UTF-8
- **Seguridad**: Se usa `htmlspecialchars()` para prevenir XSS
- **Prepared Statements**: Protección contra SQL Injection

---

¡Tu chat está listo! Solo necesitas crear la tabla `mensajes` e iniciar sesión. 🎉
