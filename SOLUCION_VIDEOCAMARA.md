# 🎥 Solución: "El navegador no soporta video y micrófono"

## ¿Cuál es el problema?

Tu navegador no puede acceder a la cámara o micrófono de tu computadora. Esto puede ocurrir por varias razones.

---

## 📋 Pasos para Resolver

### **Paso 1: Usar un Navegador Compatible**

Los navegadores recomendados son:
- ✅ **Google Chrome** (RECOMENDADO)
- ✅ **Mozilla Firefox**
- ✅ **Microsoft Edge**
- ❌ Internet Explorer (muy antiguo, no funciona)

**Si usas otro navegador, descarga Chrome desde aquí:**
👉 https://www.google.com/chrome/

---

### **Paso 2: Verificar tu Instalación**

**Abre esta página en tu navegador:**
```
http://localhost/SitioWebMundial26/diagnostico_videocamara.html
```

O desde Hamachi/Red:
```
http://25.7.206.194/SitioWebMundial26/diagnostico_videocamara.html
```

Esta página te dirá exactamente cuál es el problema.

---

### **Paso 3: Resolver el Problema Específico**

#### ❌ **Error: "Se requiere HTTPS"**

**Si accedes desde HAMACHI (IP 25.x.x.x):**

1. Abre Chrome
2. Ve a esta dirección:
   ```
   chrome://flags/#unsafely-treat-insecure-origin-as-secure
   ```
3. Busca la opción **"unsafely treat insecure origin as secure"**
4. Cámbiala a **ENABLED** (azul)
5. En el campo de texto, añade:
   ```
   http://25.7.206.194
   ```
6. Haz clic en **RELAUNCH** (Reiniciar Chrome)

---

#### ❌ **Error: "Acceso denegado a cámara"**

El navegador está pidiendo permiso pero lo bloqueaste.

1. En la barra de direcciones, busca el icono de **🔒 Candado**
2. Haz clic en él
3. Busca **"Cámara"** y **"Micrófono"**
4. Cambia de **"Bloqueado"** a **"Permitir"**
5. Recarga la página (presiona F5)
6. Intenta de nuevo

---

#### ❌ **Error: "Cámara no encontrada"**

Tu dispositivo no tiene cámara o no está conectada.

**Soluciones:**

1. **Si es laptop con cámara integrada:**
   - Reinicia tu computadora
   - Entra en Configuración > Privacidad > Cámara
   - Verifica que las aplicaciones tengan permiso
   - Vuelve a intentar

2. **Si usas cámara USB externa:**
   - Conecta la cámara correctamente
   - Espera 10 segundos a que se instale
   - Reinicia el navegador
   - Vuelve a intentar

---

#### ❌ **Error: "Navegador no soporta videollamadas"**

Tu navegador es muy antiguo o no es compatible.

**Solución:** Descarga un navegador nuevo:
- **Chrome:** https://www.google.com/chrome/
- **Firefox:** https://www.mozilla.org/firefox/
- **Edge:** https://www.microsoft.com/edge

---

### **Paso 4: Prueba la Cámara**

1. Ve a: `http://localhost/SitioWebMundial26/diagnostico_videocamara.html`
2. Haz clic en **"Encender Cámara"**
3. Si te pide permiso, haz clic en **"Permitir"**
4. Deberías verte en la pantalla

---

## ✅ ¿Funciona todo?

Si ya puedes ver tu cámara en la página de diagnóstico:

1. Ve a **Chats** en la aplicación
2. Haz clic en el botón de **📞 Videollamada**
3. Selecciona el contacto
4. Haz clic en **"Llamar"**
5. ¡Dile al otro usuario que acepte!

---

## 🆘 Aún No Funciona

Si después de todo esto sigue sin funcionar:

1. **Cierra todas las ventanas de Chrome**
2. **Reinicia tu computadora**
3. **Abre Chrome nuevamente**
4. **Vuelve a intentar**

Si aún así no funciona, probablemente:
- Tu cámara está rota o no compatible
- Necesitas actualizar los drivers de tu cámara
- Hay un problema de hardware

---

## 📞 Contacto

Si necesitas más ayuda, contacta al administrador del sitio.
