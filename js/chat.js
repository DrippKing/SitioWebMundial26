// Variables globales para acceso desde otros scripts
let currentContactId = null; 
let currentChatType = 'private'; // 'private' o 'group'
let contactsCache = {}; 
let groupsCache = {};
let MY_AVATAR = '../pictures/default.jpg'; 
let MY_USER_ID = null;

document.addEventListener('DOMContentLoaded', async () => {
    let lastMessageCount = 0;
    let pollingInterval = null;
    let typingTimeout = null;
    let searchMode = false;
    let emojiPickerVisible = false;
    let encryptionEnabled = false; // Estado de encriptación
    
    const API_URL = '../php/msg.php'; 

    // Referencias a los elementos clave
    const messageInput = document.querySelector('.message-input-area input[type="text"]');
    const sendButton = document.querySelector('.message-input-area .send-btn');
    const fileButton = document.querySelector('.message-input-area .icon-btn');
    const messagesArea = document.querySelector('.messages-area');
    const contactsPanel = document.querySelector('.contacts-panel');
    const chatHeaderTitle = document.querySelector('.chat-header .user-info h3');
    const chatHeader = document.querySelector('.chat-header');
    const encryptToggleBtn = document.getElementById('encryptToggle');
    
    // ========================================
    // FUNCIONES DE ENCRIPTACIÓN
    // ========================================
    
    // Encriptar mensaje usando Base64 y rotación de caracteres
    const encryptMessage = (text) => {
        // Convertir a Base64
        const base64 = btoa(unescape(encodeURIComponent(text)));
        // Rotar caracteres para ofuscación adicional
        const rotated = base64.split('').reverse().join('');
        return rotated;
    };
    
    // Desencriptar mensaje
    const decryptMessage = (encrypted) => {
        try {
            // Revertir rotación
            const unrotated = encrypted.split('').reverse().join('');
            // Decodificar Base64
            const decoded = decodeURIComponent(escape(atob(unrotated)));
            return decoded;
        } catch (e) {
            return encrypted; // Si falla, retornar texto original
        }
    };
    
    // Toggle encriptación
    const toggleEncryption = () => {
        encryptionEnabled = !encryptionEnabled;
        
        if (encryptionEnabled) {
            encryptToggleBtn.classList.add('active');
            encryptToggleBtn.innerHTML = '<i class="fas fa-lock"></i>';
            encryptToggleBtn.title = 'Encriptación activada';
        } else {
            encryptToggleBtn.classList.remove('active');
            encryptToggleBtn.innerHTML = '<i class="fas fa-lock-open"></i>';
            encryptToggleBtn.title = 'Encriptación desactivada';
        }
    };
    
    // Event listener para el botón de encriptación
    if (encryptToggleBtn) {
        encryptToggleBtn.addEventListener('click', toggleEncryption);
    }
    
    // Función para enviar ubicación
    const sendLocation = async () => {
        if (!currentContactId) {
            alert('Selecciona un contacto primero');
            return;
        }
        
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalización');
            return;
        }
        
        // Mostrar indicador de carga
        const locationBtn = document.getElementById('locationBtn');
        const originalHTML = locationBtn.innerHTML;
        locationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        locationBtn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                const locationMessage = `📍 Mi ubicación: ${googleMapsUrl}`;
                
                // Enviar como mensaje de texto
                const formData = new FormData();
                formData.append('receiver_id', currentContactId);
                formData.append('message_text', locationMessage);
                formData.append('message_type', 'text');
                formData.append('is_encrypted', '0'); // Las ubicaciones no se encriptan
                
                if (currentChatType === 'group') {
                    formData.delete('receiver_id');
                    formData.append('group_id', currentContactId);
                    formData.append('action', 'send_group_message');
                } else {
                    formData.append('action', 'send_message');
                }
                
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        loadMessages(currentContactId);
                    } else {
                        alert('Error al enviar ubicación');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al enviar ubicación');
                } finally {
                    locationBtn.innerHTML = originalHTML;
                    locationBtn.disabled = false;
                }
            },
            (error) => {
                locationBtn.innerHTML = originalHTML;
                locationBtn.disabled = false;
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Debes permitir el acceso a tu ubicación');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert('Ubicación no disponible');
                        break;
                    case error.TIMEOUT:
                        alert('Tiempo de espera agotado');
                        break;
                    default:
                        alert('Error al obtener ubicación');
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    };
    
    // Event listener para el botón de ubicación
    const locationBtn = document.getElementById('locationBtn');
    if (locationBtn) {
        locationBtn.addEventListener('click', sendLocation);
    }
    
    // Emojis comunes
    const emojis = ['😀', '😂', '😍', '😎', '😭', '😡', '👍', '👎', '❤️', '🔥', '✨', '🎉', '💪', '🙏', '👏', '🤔', '😴', '🤩', '🥳', '😱'];
    
    // ========================================
    // CREAR ELEMENTOS DE UI ADICIONALES
    // ========================================
    
    const createToolbar = () => {
        const toolbar = document.createElement('div');
        toolbar.className = 'chat-toolbar';
        toolbar.style.cssText = 'display:flex; gap:10px; padding:10px; background:#f7f7f7; border-bottom:1px solid #ddd; align-items:center; flex-wrap:wrap;';
        
        // Botón de búsqueda
        const searchBtn = document.createElement('button');
        searchBtn.innerHTML = '<i class="fas fa-search"></i> Buscar';
        searchBtn.className = 'toolbar-btn';
        searchBtn.style.cssText = 'padding:8px 15px; border:none; background:#4a4a4a; color:white; border-radius:5px; cursor:pointer;';
        searchBtn.onclick = toggleSearchMode;
        
        // Botón de adjuntar
        const attachBtn = document.createElement('button');
        attachBtn.innerHTML = '<i class="fas fa-paperclip"></i> Adjuntar';
        attachBtn.className = 'toolbar-btn';
        attachBtn.style.cssText = 'padding:8px 15px; border:none; background:#4a4a4a; color:white; border-radius:5px; cursor:pointer;';
        attachBtn.onclick = triggerFileUpload;
        
        toolbar.appendChild(searchBtn);
        toolbar.appendChild(attachBtn);
        
        // Insertar toolbar después del header
        chatHeader.parentNode.insertBefore(toolbar, chatHeader.nextSibling);
    };
    
    const createEmojiPicker = () => {
        const picker = document.createElement('div');
        picker.className = 'emoji-picker';
        picker.id = 'emoji-picker';
        picker.style.cssText = 'display:none; position:absolute; background:white; border:1px solid #ddd; border-radius:10px; padding:15px; box-shadow:0 4px 10px rgba(0,0,0,0.2); z-index:1000; max-width:300px; bottom:80px; right:20px;';
        
        emojis.forEach(emoji => {
            const span = document.createElement('span');
            span.textContent = emoji;
            span.style.cssText = 'font-size:24px; cursor:pointer; padding:5px; display:inline-block;';
            span.onclick = () => insertEmoji(emoji);
            picker.appendChild(span);
        });
        
        document.body.appendChild(picker);
    };
    
    // ========================================
    // FUNCIONES DE NOTIFICACIONES
    // ========================================
    
    const updateUnreadCounts = async () => {
        try {
            const response = await fetch(`${API_URL}?action=get_unread_count`);
            const counts = await response.json();
            
            // Actualizar badges en contactos
            Object.keys(counts).forEach(key => {
                const contactId = key.replace('contact_', '');
                const contactItem = document.querySelector(`[data-contact-id="${contactId}"][data-chat-type="private"]`);
                
                if (contactItem) {
                    let badge = contactItem.querySelector('.notification-badge');
                    
                    if (counts[key] > 0) {
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.classList.add('notification-badge');
                            badge.style.cssText = 'background:#ff4444; color:white; border-radius:50%; width:20px; height:20px; display:flex; justify-content:center; align-items:center; font-size:0.75em; font-weight:bold; margin-left:auto; position:absolute; right:10px; top:50%; transform:translateY(-50%);';
                            contactItem.style.position = 'relative';
                            contactItem.appendChild(badge);
                        }
                        badge.textContent = counts[key];
                    } else if (badge) {
                        badge.remove();
                    }
                }
            });
            
            // Calcular total de no leídos
            const totalUnread = Object.values(counts).reduce((a, b) => a + b, 0);
            updatePageTitle(totalUnread);
            
        } catch (error) {
            console.error('Error al actualizar contadores:', error);
        }
    };
    
    const updatePageTitle = (count) => {
        if (count > 0) {
            document.title = `(${count}) Chats - Mundial 26`;
        } else {
            document.title = 'Chats - Mundial 26';
        }
    };
    
    // ========================================
    // FUNCIONES DE INDICADOR "ESCRIBIENDO"
    // ========================================
    
    const showTypingIndicator = (isTyping, usernames = []) => {
        const existingIndicator = document.querySelector('.typing-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        if (isTyping && usernames.length > 0) {
            const indicator = document.createElement('div');
            indicator.classList.add('typing-indicator');
            indicator.style.cssText = 'padding:10px; display:flex; align-items:center; gap:10px; color:#666; font-style:italic;';
            
            indicator.innerHTML = `
                <span class="typing-text">${usernames.join(', ')} está escribiendo</span>
                <span class="typing-dots" style="display:flex; gap:3px;">
                    <span style="animation: blink 1.4s infinite;">.</span>
                    <span style="animation: blink 1.4s infinite 0.2s;">.</span>
                    <span style="animation: blink 1.4s infinite 0.4s;">.</span>
                </span>
            `;
            
            messagesArea.appendChild(indicator);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    };
    
    const checkTypingStatus = async () => {
        if (!currentContactId) return;
        
        try {
            const response = await fetch(`${API_URL}?action=get_typing&chat_id=${currentContactId}&chat_type=${currentChatType}`);
            const data = await response.json();
            showTypingIndicator(data.is_typing, data.users);
        } catch (error) {
            console.error('Error al verificar estado de escritura:', error);
        }
    };
    
    const setTypingStatus = async (isTyping) => {
        if (!currentContactId) return;
        
        try {
            const formData = new FormData();
            formData.append('chat_id', currentContactId);
            formData.append('chat_type', currentChatType);
            formData.append('is_typing', isTyping ? 1 : 0);
            formData.append('action', 'set_typing');
            
            await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error al establecer estado de escritura:', error);
        }
    };
    
    // ========================================
    // FUNCIONES DE BÚSQUEDA
    // ========================================
    
    const toggleSearchMode = () => {
        searchMode = !searchMode;
        
        if (searchMode) {
            messageInput.placeholder = '🔍 Buscar mensajes... (Enter para buscar)';
            messageInput.value = '';
            messageInput.focus();
            messageInput.style.background = '#fff3cd';
        } else {
            messageInput.placeholder = 'Escribe un mensaje...';
            messageInput.value = '';
            messageInput.style.background = '';
            loadMessages(currentContactId);
        }
    };
    
    const searchMessages = async (query) => {
        if (!query || !currentContactId || currentChatType === 'group') {
            alert('La búsqueda solo está disponible en chats privados');
            return;
        }
        
        try {
            const response = await fetch(`${API_URL}?action=search_messages&query=${encodeURIComponent(query)}&contact_id=${currentContactId}`);
            const messages = await response.json();
            
            messagesArea.innerHTML = '';
            
            if (messages.length === 0) {
                messagesArea.innerHTML = '<div style="text-align:center; padding:20px; color:#999;">No se encontraron mensajes con "' + query + '"</div>';
                return;
            }
            
            const resultHeader = document.createElement('div');
            resultHeader.style.cssText = 'background:#e8f5e9; padding:10px; margin-bottom:10px; border-radius:5px; text-align:center;';
            resultHeader.textContent = `${messages.length} resultado(s) para "${query}"`;
            messagesArea.appendChild(resultHeader);
            
            messages.forEach(msg => {
                const avatar = (msg.type === 'sent') ? MY_AVATAR : contactsCache[currentContactId]?.avatar_url;
                createAndAppendMessage(msg, msg.type, avatar);
            });
            
        } catch (error) {
            console.error('Error al buscar mensajes:', error);
            alert('Error al buscar mensajes');
        }
    };
    
    // ========================================
    // FUNCIONES DE EMOJIS
    // ========================================
    
    const toggleEmojiPicker = () => {
        const picker = document.getElementById('emoji-picker');
        emojiPickerVisible = !emojiPickerVisible;
        picker.style.display = emojiPickerVisible ? 'block' : 'none';
    };
    
    const insertEmoji = (emoji) => {
        messageInput.value += emoji;
        messageInput.focus();
    };
    
    // ========================================
    // FUNCIONES DE ARCHIVOS
    // ========================================
    
    const triggerFileUpload = () => {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*,.pdf,.doc,.docx,.txt';
        
        fileInput.onchange = async (e) => {
            const file = e.target.files[0];
            if (file) {
                await handleFileUpload(file);
            }
        };
        
        fileInput.click();
    };
    
    const handleFileUpload = async (file) => {
        // Validar tamaño (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. Máximo 5MB.');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload_file');
        
        try {
            // Mostrar indicador de carga
            messagesArea.innerHTML += '<div class="uploading" style="text-align:center; padding:10px; color:#666;"><i class="fas fa-spinner fa-spin"></i> Subiendo archivo...</div>';
            
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            // Quitar indicador
            document.querySelector('.uploading')?.remove();
            
            if (result.success) {
                // Enviar mensaje con el archivo
                const messageFormData = new FormData();
                messageFormData.append('message_text', `📎 ${file.name}`);
                messageFormData.append('file_url', result.filename); // Guardar nombre del archivo
                messageFormData.append('message_type', 'file'); // Marcar como archivo
                
                if (currentChatType === 'group') {
                    messageFormData.append('group_id', currentContactId);
                    messageFormData.append('action', 'send_group_message');
                } else {
                    messageFormData.append('receiver_id', currentContactId);
                    messageFormData.append('action', 'send_message');
                }
                
                await fetch(API_URL, {
                    method: 'POST',
                    body: messageFormData
                });
                
                lastMessageCount = 0;
                await loadMessages(currentContactId);
                alert('Archivo enviado correctamente');
            } else {
                alert('Error al subir archivo: ' + result.error);
            }
        } catch (error) {
            console.error('Error al subir archivo:', error);
            document.querySelector('.uploading')?.remove();
            alert('Error al subir el archivo');
        }
    };
    // ========================================
    // FUNCIONES DE RENDERIZADO
    // ========================================
    
    const createAndAppendMessage = (messageData, type, avatarSrc, senderName = '') => {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', type);

        const avatarImg = document.createElement('img');
        avatarImg.src = avatarSrc;
        avatarImg.alt = 'Avatar';
        avatarImg.classList.add('avatar');

        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');
        
        // Si es mensaje de grupo, mostrar nombre del remitente
        if (currentChatType === 'group' && type === 'received' && senderName) {
            const nameSpan = document.createElement('span');
            nameSpan.classList.add('sender-name');
            nameSpan.textContent = senderName;
            nameSpan.style.fontSize = '0.8em';
            nameSpan.style.color = '#666';
            nameSpan.style.display = 'block';
            nameSpan.style.marginBottom = '3px';
            nameSpan.style.fontWeight = 'bold';
            contentDiv.appendChild(nameSpan);
        }

        const messageP = document.createElement('p');
        let text = typeof messageData === 'string' ? messageData : messageData.text;
        
        // Escapar HTML para evitar XSS en mensajes desencriptados
        const escapeHtml = (str) => {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        };
        
        // Agregar indicador visual si el mensaje fue encriptado
        if (messageData.is_encrypted == 1) {
            const lockIcon = document.createElement('i');
            lockIcon.className = 'fas fa-lock';
            lockIcon.style.cssText = 'font-size:0.7em; color:#4CAF50; margin-right:5px;';
            lockIcon.title = 'Mensaje encriptado';
            messageP.appendChild(lockIcon);
            // Escapar mensajes desencriptados
            text = escapeHtml(text);
        }
        
        // Detectar y convertir enlaces de ubicación en clickeables
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        if (text.match(urlRegex)) {
            text = text.replace(urlRegex, (url) => {
                if (url.includes('google.com/maps')) {
                    return `<a href="${url}" target="_blank" style="color:#1976D2; text-decoration:underline; font-weight:bold;">🗺️ Ver en Maps</a>`;
                }
                return `<a href="${url}" target="_blank" style="color:#1976D2; text-decoration:underline;">${url}</a>`;
            });
        }
        
        messageP.innerHTML += text; // Cambio a innerHTML para que los emojis se vean
        messageP.style.margin = '0';
        messageP.style.wordWrap = 'break-word';
        contentDiv.appendChild(messageP);
        
        // Si el mensaje tiene archivo adjunto
        if (messageData.file_url) {
            const fileUrl = messageData.file_url;
            const fileName = fileUrl.split('/').pop();
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            // Verificar si es imagen
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                const imgPreview = document.createElement('img');
                imgPreview.src = fileUrl;
                imgPreview.alt = fileName;
                imgPreview.style.cssText = 'max-width:200px; max-height:200px; border-radius:10px; margin-top:8px; display:block; cursor:pointer; border:2px solid #ddd;';
                imgPreview.onclick = () => window.open(fileUrl, '_blank');
                contentDiv.appendChild(imgPreview);
            } else {
                // Para documentos y otros archivos
                const fileContainer = document.createElement('div');
                fileContainer.style.cssText = 'margin-top:8px; padding:10px; background:#f0f0f0; border-radius:8px; display:inline-block;';
                
                const fileIcon = document.createElement('span');
                if (fileExt === 'pdf') {
                    fileIcon.innerHTML = '📄';
                } else if (['doc', 'docx'].includes(fileExt)) {
                    fileIcon.innerHTML = '📝';
                } else if (['xls', 'xlsx'].includes(fileExt)) {
                    fileIcon.innerHTML = '📊';
                } else {
                    fileIcon.innerHTML = '📎';
                }
                fileIcon.style.fontSize = '24px';
                
                const fileLink = document.createElement('a');
                fileLink.href = fileUrl;
                fileLink.target = '_blank';
                fileLink.textContent = ' ' + fileName;
                fileLink.style.cssText = 'color:#007bff; text-decoration:none; margin-left:5px; font-weight:bold;';
                
                fileContainer.appendChild(fileIcon);
                fileContainer.appendChild(fileLink);
                contentDiv.appendChild(fileContainer);
            }
        }
        
        // Timestamp
        if (messageData.time) {
            const timeSpan = document.createElement('span');
            timeSpan.classList.add('message-time');
            timeSpan.style.fontSize = '0.7em';
            timeSpan.style.color = '#999';
            timeSpan.style.display = 'block';
            timeSpan.style.marginTop = '3px';
            const date = new Date(messageData.time);
            timeSpan.textContent = date.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
            
            // Indicador de leído/no leído (solo para mensajes enviados en chats privados)
            if (type === 'sent' && currentChatType === 'private') {
                const readIndicator = document.createElement('span');
                readIndicator.style.marginLeft = '5px';
                readIndicator.textContent = messageData.is_read ? '✓✓' : '✓';
                readIndicator.style.color = messageData.is_read ? '#4CAF50' : '#999';
                timeSpan.appendChild(readIndicator);
            }
            
            contentDiv.appendChild(timeSpan);
        }

        if (type === 'sent') {
            messageDiv.appendChild(contentDiv);
            messageDiv.appendChild(avatarImg); 
        } else {
            messageDiv.appendChild(avatarImg);
            messageDiv.appendChild(contentDiv);
        }
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    };

    // ========================================
    // FUNCIONES DE CARGA DE MENSAJES
    // ========================================
    
    const loadMessages = async (contactId, isPolling = false) => {
        if (!contactId) return;

        try {
            let url, response, messages;
            
            if (currentChatType === 'group') {
                url = `${API_URL}?action=get_group_messages&group_id=${contactId}`;
            } else {
                url = `${API_URL}?action=get_messages&contact_id=${contactId}`;
            }
            
            response = await fetch(url);
            messages = await response.json();
            
            // Recargar solo si hay cambios en el número de mensajes
            if (!isPolling || messages.length !== lastMessageCount) {
                lastMessageCount = messages.length;
                messagesArea.innerHTML = '';
                
                messages.forEach(msg => {
                    // Desencriptar mensaje si está marcado como encriptado
                    if (msg.is_encrypted == 1 && msg.text) {
                        msg.text = decryptMessage(msg.text);
                        console.log('Mensaje desencriptado:', msg.text);
                    }
                    
                    let avatar;
                    if (currentChatType === 'group') {
                        avatar = msg.sender_avatar || '../pictures/default.jpg';
                    } else {
                        avatar = (msg.type === 'sent') ? MY_AVATAR : contactsCache[contactId]?.avatar_url || '../pictures/default.jpg';
                    }
                    
                    createAndAppendMessage(msg, msg.type, avatar, msg.sender_name);
                });
                
                // Marcar como leídos si no está en polling y es privado
                if (!isPolling && currentChatType === 'private') {
                    markMessagesAsRead(contactId);
                }
            }
            
            // Verificar si alguien está escribiendo (solo en polling)
            if (isPolling) {
                checkTypingStatus();
            }

        } catch (error) {
            console.error('Error al cargar mensajes:', error);
        }
    };
    
    const markMessagesAsRead = async (contactId) => {
        try {
            const formData = new FormData();
            formData.append('contact_id', contactId);
            formData.append('action', 'mark_as_read');
            
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                // Actualizar contadores inmediatamente
                await updateUnreadCounts();
            }
        } catch (error) {
            console.error('Error al marcar como leídos:', error);
        }
    };

    // --- Lógica de Cambio de Contacto ---
    const handleContactChange = function() {
        document.querySelector('.contact-item.active')?.classList.remove('active');
        this.classList.add('active');

        const newContactId = parseInt(this.getAttribute('data-contact-id'));
        const chatType = this.getAttribute('data-chat-type') || 'private';

        currentContactId = newContactId;
        currentChatType = chatType;
        lastMessageCount = 0; // Resetear contador al cambiar de contacto
        
        // Quitar badge inmediatamente del contacto seleccionado
        const badge = this.querySelector('.notification-badge');
        if (badge) {
            badge.remove();
        }
        
        // Actualizar título del chat
        if (chatType === 'group') {
            const group = groupsCache[newContactId];
            chatHeaderTitle.textContent = group.name;
        } else {
            const contact = contactsCache[newContactId];
            chatHeaderTitle.textContent = contact.username;
        }

        loadMessages(currentContactId);
        
        // ✅ AQUÍ: Cargar tareas si es grupo LMEADOS (ID = 2)
        console.log('👆 Contacto cambiado a:', chatType, newContactId);
        if (chatType === 'group' && newContactId === 2) {
            console.log('✅ Entrando a grupo LMEADOS');
            loadLmeadosTasks(2);
        } else {
            const container = document.getElementById('lmeados-tasks-banner');
            if (container) {
                console.log('❌ Ocultando panel de tareas');
                container.style.display = 'none';
            }
        }
    };

    // --- Lógica de Carga de Contactos ---
    const loadContacts = async () => {
        try {
            console.log('🔄 Cargando contactos...');
            const response = await fetch(`${API_URL}?action=get_contacts`);
            const data = await response.json();
            console.log('✅ Contactos recibidos:', data);
            
            // Obtener datos del usuario actual
            if (data.current_user) {
                MY_USER_ID = data.current_user.id;
                MY_AVATAR = data.current_user.avatar_url;
                console.log('👤 Usuario cargado:', MY_USER_ID);
            }
            
            const contacts = data.contacts || data;
            
            if (!Array.isArray(contacts)) {
                console.error('❌ Contactos no es un array:', contacts);
                return;
            }

            // Guardamos los items estáticos (canales) temporalmente
            const staticChannels = Array.from(contactsPanel.querySelectorAll('.contact-item.channel'));
            contactsPanel.innerHTML = '';
            
            contacts.forEach((contact, index) => {
                contactsCache[contact.id] = contact; 

                const item = document.createElement('div');
                item.classList.add('contact-item');
                item.setAttribute('data-contact-id', contact.id);
                item.setAttribute('data-chat-type', 'private');
                
                // Establecer el primer contacto activo al iniciar
                if (currentContactId === null) {
                    item.classList.add('active');
                    currentContactId = contact.id;
                    currentChatType = 'private';
                    chatHeaderTitle.textContent = contact.username;
                    loadMessages(contact.id); 
                }

                const statusClass = contact.status === 'online' ? 'online' : 'offline';
                const statusDot = contact.status === 'online' ? '🟢' : '⚫';

                item.innerHTML = `
                    <img src="${contact.avatar_url}" alt="Avatar" class="avatar">
                    <div class="contact-info">
                        <span class="contact-name">${contact.username}</span>
                        <span class="status-indicator ${statusClass}">${statusDot} ${contact.status === 'online' ? 'En línea' : 'Desconectado'}</span>
                    </div>
                `;
                item.addEventListener('click', handleContactChange);
                contactsPanel.appendChild(item);
            });
            
            // Cargar grupos
            await loadGroups();

            // Iniciar sondeo (polling) para nuevos mensajes - solo un intervalo
            if (contacts.length > 0 && !pollingInterval) {
                pollingInterval = setInterval(() => {
                    if (currentContactId) {
                        loadMessages(currentContactId, true);
                        updateUnreadCounts();
                    }
                    updateOnlineStatus(); // Actualizar estados
                }, 1000); // Cada 1 segundo para más velocidad
            }

        } catch (error) {
            console.error('Error al cargar contactos:', error);
        }
    };
    
    // --- Actualizar estados online/offline ---
    let isUpdatingStatus = false;
    
    const updateOnlineStatus = async () => {
        // Evitar peticiones simultáneas
        if (isUpdatingStatus) return;
        
        isUpdatingStatus = true;
        try {
            const response = await fetch(`${API_URL}?action=get_contacts`);
            const data = await response.json();
            const contacts = data.contacts || data;
            
            if (!Array.isArray(contacts)) {
                console.error('❌ Error en updateOnlineStatus: contacts no es array');
                return;
            }
            
            contacts.forEach(contact => {
                const contactItem = contactsPanel.querySelector(`[data-contact-id="${contact.id}"]`);
                if (contactItem && contactItem.classList.contains('contact-item')) {
                    const statusIndicator = contactItem.querySelector('.status-indicator');
                    if (statusIndicator) {
                        const statusClass = contact.status === 'online' ? 'online' : 'offline';
                        const statusDot = contact.status === 'online' ? '🟢' : '⚫';
                        const statusText = contact.status === 'online' ? 'En línea' : 'Desconectado';
                        
                        statusIndicator.className = `status-indicator ${statusClass}`;
                        statusIndicator.textContent = `${statusDot} ${statusText}`;
                    }
                }
            });
        } catch (error) {
            console.error('❌ Error al actualizar estados:', error);
        } finally {
            isUpdatingStatus = false;
        }
    };
    
    // --- Lógica de Carga de Grupos ---
    const loadGroups = async () => {
        try {
            const response = await fetch(`${API_URL}?action=get_groups`);
            const groups = await response.json();
            
            groups.forEach(group => {
                groupsCache[group.id] = group;
                
                const item = document.createElement('div');
                item.classList.add('contact-item', 'channel');
                item.setAttribute('data-contact-id', group.id);
                item.setAttribute('data-chat-type', 'group');
                
                item.innerHTML = `
                    <span><i class="fas fa-crown"></i> ${group.name} <i class="fas fa-crown"></i></span>
                `;
                
                item.addEventListener('click', handleContactChange);
                contactsPanel.appendChild(item);
            });
        } catch (error) {
            console.error('Error al cargar grupos:', error);
        }
    };
    
    // ========================================
    // FUNCIONES DE ENVÍO DE MENSAJES
    // ========================================
    
    const sendMessage = async () => {
        const messageText = messageInput.value.trim();
        if (messageText === '' || currentContactId === null) return; 

        messageInput.value = ''; // Limpiar input inmediatamente
        
        // Detener indicador de "escribiendo"
        setTypingStatus(false);

        try {
            // Encriptar mensaje si está habilitado
            const finalMessage = encryptionEnabled ? encryptMessage(messageText) : messageText;
            
            const formData = new FormData();
            formData.append('message_text', finalMessage);
            formData.append('is_encrypted', encryptionEnabled ? '1' : '0'); // Indicar si está encriptado
            
            if (currentChatType === 'group') {
                formData.append('group_id', currentContactId);
                formData.append('action', 'send_group_message');
            } else {
                formData.append('receiver_id', currentContactId);
                formData.append('action', 'send_message');
            }

            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Recargar mensajes inmediatamente después de enviar
                lastMessageCount = 0; // Forzar recarga
                await loadMessages(currentContactId);
            } else {
                console.error('Error al guardar el mensaje:', result.error);
                alert('Error al enviar el mensaje');
            }

        } catch (error) {
            console.error('Error de red al enviar mensaje:', error);
            alert('Error de conexión al enviar el mensaje');
        }
    };
    
    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    sendButton.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (searchMode) {
                searchMessages(messageInput.value);
            } else {
                sendMessage();
            }
        }
    });
    
    // Indicador de "escribiendo"
    messageInput.addEventListener('input', () => {
        if (searchMode) return;
        
        clearTimeout(typingTimeout);
        setTypingStatus(true);
        
        // Detener después de 1 segundo de inactividad
        typingTimeout = setTimeout(() => {
            setTypingStatus(false);
        }, 1000);
    });
    
    // Cerrar emoji picker al hacer click fuera
    document.addEventListener('click', (e) => {
        const picker = document.getElementById('emoji-picker');
        if (picker && emojiPickerVisible && !e.target.closest('.toolbar-btn') && !e.target.closest('#emoji-picker')) {
            toggleEmojiPicker();
        }
    });
    
    // Atajos de teclado
    document.addEventListener('keydown', (e) => {
        // Ctrl + F para buscar
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            toggleSearchMode();
        }
        // ESC para salir de búsqueda
        if (e.key === 'Escape' && searchMode) {
            toggleSearchMode();
        }
    });

    // ========================================
    // INICIALIZACIÓN
    // ========================================
    
    // Crear UI adicional
    createToolbar();
    createEmojiPicker();
    
    // Agregar estilos de animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes blink {
            0%, 20%, 50%, 80%, 100% { opacity: 1; }
            40% { opacity: 0.3; }
            60% { opacity: 0.6; }
        }
        .toolbar-btn:hover {
            background-color: #333 !important;
            transform: scale(1.05);
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.1); }
            100% { transform: translateY(-50%) scale(1); }
        }
    `;
    document.head.appendChild(style);
    
    // Inicializar la aplicación
    await loadContacts();
    
    // Actualizar contadores inicialmente
    updateUnreadCounts();
    
    // Recargar contactos cada 3 segundos (para detectar nuevos amigos)
    setInterval(loadContacts, 3000);
    
    // Actualizar estados online cada 3 segundos
    setInterval(updateOnlineStatus, 3000);
    
    // Actualizar contadores cada 5 segundos
    setInterval(updateUnreadCounts, 5000);
    
    // ========================================
    // PANEL DE TAREAS LMEADOS - CON VALIDACIÓN DE MEMBRESÍA
    // ========================================
    
    async function loadLmeadosTasks(groupId) {
        console.log('📋 loadLmeadosTasks llamado con groupId:', groupId);
        
        const banner = document.getElementById('lmeados-tasks-banner');
        const container = document.getElementById('lmeados-tasks-container');
        
        if (!banner || !container) {
            console.error('❌ Elementos del panel LMEADOS no encontrados');
            return;
        }
        
        // Solo mostrar para el grupo LMEADOS (ID = 2)
        if (groupId !== 2) {
            console.log('⏹️ No es el grupo LMEADOS, ocultando banner');
            banner.style.display = 'none';
            return;
        }
        
        try {
            console.log('🌐 Fetching: ../php/grupo_lmeados_tareas_api_v2.php');
            const response = await fetch('../php/grupo_lmeados_tareas_api_v2.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('✅ Datos recibidos:', data);
            
            // Verificar si no es miembro
            if (data.error || !data.miembro) {
                console.log('👤 Usuario no es miembro de LMEADOS');
                banner.style.display = 'none';
                return;
            }
            
            // Verificar si hay mensajes en el chat
            const messagesCount = messagesArea.children.length;
            console.log('💬 Mensajes en el chat:', messagesCount);
            
            if (messagesCount === 0) {
                console.log('⏹️ Sin mensajes en el chat, ocultando banner');
                banner.style.display = 'none';
                return;
            }
            
            // Es miembro y hay mensajes, mostrar banner
            console.log('🟢 Usuario es miembro de LMEADOS y hay mensajes');
            banner.style.display = 'block';
            
            const tareas = data.tareas || [];
            
            if (tareas.length === 0) {
                console.warn('⚠️ No hay tareas');
                container.innerHTML = '<span style="color:#ff9800;">Sin tareas disponibles</span>';
                return;
            }
            
            // Crear lista simple de tareas con checkboxes
            let html = `<div style="padding:5px 0;">`;
            
            tareas.forEach((usuario) => {
                const completadas = [usuario.mensaje, usuario.foto, usuario.documento, usuario.ubicacion].filter(t => t).length;
                const total = 4;
                const porcentaje = Math.round((completadas / total) * 100);
                
                html += `
                    <div style="padding:8px 0; border-bottom:1px solid #e0e0e0; display:flex; align-items:flex-start; gap:8px;">
                        <div style="flex:1;">
                            <div style="font-weight:500; color:#333; margin-bottom:4px;">${usuario.usuario}</div>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; font-size:12px;">
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; ${usuario.mensaje ? 'text-decoration:line-through; color:#999;' : ''}">
                                    <input type="checkbox" ${usuario.mensaje ? 'checked' : ''} disabled style="cursor:pointer;">
                                    💬 Mensaje
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; ${usuario.foto ? 'text-decoration:line-through; color:#999;' : ''}">
                                    <input type="checkbox" ${usuario.foto ? 'checked' : ''} disabled style="cursor:pointer;">
                                    📷 Foto
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; ${usuario.documento ? 'text-decoration:line-through; color:#999;' : ''}">
                                    <input type="checkbox" ${usuario.documento ? 'checked' : ''} disabled style="cursor:pointer;">
                                    📄 Documento
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; ${usuario.ubicacion ? 'text-decoration:line-through; color:#999;' : ''}">
                                    <input type="checkbox" ${usuario.ubicacion ? 'checked' : ''} disabled style="cursor:pointer;">
                                    📍 Ubicación
                                </label>
                            </div>
                            <div style="margin-top:4px; font-size:11px; color:#666;">Progreso: ${completadas}/${total} (${porcentaje}%)</div>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
            
            container.innerHTML = html;
            console.log('✅ Panel de tareas actualizado');
            
        } catch (error) {
            console.error('❌ Error al cargar tareas LMEADOS:', error);
            banner.style.display = 'none';
        }
    }
    
    // Actualizar tareas cada 3 segundos (solo si estás en LMEADOS)
    setInterval(() => {
        if (currentChatType === 'group' && currentContactId === 2) {
            loadLmeadosTasks(2);
        }
    }, 3000);
});