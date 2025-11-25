# 💬 Sistema de Chat - Inicio Rápido

## 🚀 Configuración en 3 Pasos

### 1️⃣ Crear la tabla de mensajes
Abre phpMyAdmin (http://localhost/phpmyadmin) y ejecuta:

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

O importa: **TABLE MENSAJES.sql**

### 2️⃣ (Opcional) Insertar datos de prueba
Ejecuta en phpMyAdmin: **DATOS_PRUEBA.sql**

Esto creará 5 usuarios de prueba y algunos mensajes. Contraseña para todos: `test123`

### 3️⃣ Probar el sistema

1. **Verificar configuración:**
   - Ve a: `http://localhost/SitioWebMundial26/php/test_chat.php`
   - Verifica que todo esté ✅ verde

2. **Iniciar sesión:**
   - Ve a: `html/login.html`
   - Usuario: `juanp` / Contraseña: `test123` (si usaste datos de prueba)

3. **Usar el chat:**
   - Después de login, irás automáticamente a `chats.html`
   - Selecciona un contacto del panel izquierdo
   - Escribe y envía mensajes!

## 📁 Archivos Nuevos Creados

```
✅ TABLE MENSAJES.sql          → Estructura de la tabla de mensajes
✅ DATOS_PRUEBA.sql            → Usuarios y mensajes de ejemplo
✅ INSTRUCCIONES_CHAT.md       → Documentación completa
✅ README_CHAT.md              → Este archivo (inicio rápido)
✅ php/test_chat.php           → Script de verificación del sistema
✅ php/generar_hash.php        → Generador de contraseñas hasheadas
```

## 🔧 Archivos Modificados

```
✅ js/chat.js                  → Lógica mejorada del chat
✅ php/msg.php                 → API del chat mejorada
✅ css/chats.css               → Estilos para mensajes enviados/recibidos
```

## 🛠️ Herramientas Útiles

- **Test del sistema:** `php/test_chat.php`
- **Generar contraseñas:** `php/generar_hash.php`
- **Ver documentación completa:** `INSTRUCCIONES_CHAT.md`

## ⚡ Características Implementadas

✅ **Envío y recepción de mensajes** en tiempo real (polling cada 2s)  
✅ **Carga dinámica de contactos** desde la base de datos  
✅ **Sistema de sesiones** para identificar usuarios  
✅ **Estilos diferenciados** para mensajes enviados vs recibidos  
✅ **Auto-scroll** al final de la conversación  
✅ **Enter para enviar** mensajes  
✅ **Protección contra XSS y SQL Injection**  
✅ **Cambio dinámico de conversación** sin recargar página  

## 🐛 ¿Problemas?

1. **No carga contactos:** Verifica que tienes usuarios en la tabla `usuarios`
2. **Error de sesión:** Asegúrate de hacer login primero
3. **No se envían mensajes:** Verifica que existe la tabla `mensajes`
4. **Puerto incorrecto:** Confirma que tu MySQL usa el puerto **3307**

## 📞 Próximos Pasos

Para mejorar aún más tu chat, considera agregar:
- 🔔 Notificaciones de mensajes no leídos
- ⏰ Timestamps en los mensajes
- 📎 Adjuntar archivos/imágenes
- 😀 Selector de emojis
- 🔍 Búsqueda de contactos
- 💬 Indicador de "escribiendo..."
- 🌐 WebSockets para tiempo real (reemplazar polling)

---

**¡Disfruta tu chat! 🎉**

Para más detalles técnicos, revisa: **INSTRUCCIONES_CHAT.md**
