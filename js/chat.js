document.addEventListener('DOMContentLoaded', () => {
    let currentContactId = null; 
    let contactsCache = {}; 
    const MY_AVATAR = '../pictures/default.jpg'; 
    const MY_USER_ID = 1; // ID del usuario logueado (debe coincidir con PHP)
    
    const API_URL = '../php/msg.php'; 

    // Referencias a los elementos clave, usando los selectores de tu HTML
    const messageInput = document.querySelector('.message-input-area input[type="text"]');
    const sendButton = document.querySelector('.message-input-area .send-btn');
    const messagesArea = document.querySelector('.messages-area');
    const contactsPanel = document.querySelector('.contacts-panel');
    const chatHeaderTitle = document.querySelector('.chat-header .user-info h3');
    
    // --- Lógica de Renderizado ---
    const createAndAppendMessage = (text, type, avatarSrc) => {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', type);

        const avatarImg = document.createElement('img');
        avatarImg.src = avatarSrc;
        avatarImg.alt = 'Avatar';
        avatarImg.classList.add('avatar');

        const messageP = document.createElement('p');
        messageP.textContent = text;

        if (type === 'sent') {
            messageDiv.appendChild(messageP);
            messageDiv.appendChild(avatarImg); 
        } else {
            messageDiv.appendChild(avatarImg);
            messageDiv.appendChild(messageP);
        }
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    };

    // --- Lógica de Carga de Mensajes ---
    const loadMessages = async (contactId, isPolling = false) => {
        if (!contactId) return;

        try {
            const response = await fetch(`${API_URL}?action=get_messages&contact_id=${contactId}`);
            const messages = await response.json();
            
            if (!isPolling) {
                messagesArea.innerHTML = ''; 
            }
            
            // Recargar solo si hay un cambio en el número de mensajes (simplificación)
            if (!isPolling || messages.length !== messagesArea.children.length) {
                messagesArea.innerHTML = '';
                
                messages.forEach(msg => {
                    const avatar = (msg.type === 'sent') ? MY_AVATAR : contactsCache[contactId]?.avatar_url || 'https://i.pravatar.cc/150?u=default';
                    createAndAppendMessage(msg.text, msg.type, avatar);
                });
            }

        } catch (error) {
            console.error('Error al cargar mensajes:', error);
        }
    };

    // --- Lógica de Cambio de Contacto ---
    const handleContactChange = function() {
        document.querySelector('.contact-item.active')?.classList.remove('active');
        this.classList.add('active');

        const newContactId = parseInt(this.getAttribute('data-contact-id'));
        const newContactName = contactsCache[newContactId].username;

        currentContactId = newContactId;
        chatHeaderTitle.textContent = newContactName;

        loadMessages(currentContactId);
    };

    // --- Lógica de Carga de Contactos ---
    const loadContacts = async () => {
        try {
            const response = await fetch(`${API_URL}?action=get_contacts`);
            const contacts = await response.json();

            // Guardamos los items estáticos (canales) y limpiamos el panel
            const staticItems = Array.from(contactsPanel.querySelectorAll('.contact-item.channel, .contact-item:not([data-contact-id])'));
            contactsPanel.innerHTML = '';
            
            contacts.forEach((contact, index) => {
                contactsCache[contact.id] = contact; 

                const item = document.createElement('div');
                item.classList.add('contact-item');
                item.setAttribute('data-contact-id', contact.id);
                
                // Establecer el primer contacto activo al iniciar
                if (currentContactId === null) {
                    item.classList.add('active');
                    currentContactId = contact.id;
                    chatHeaderTitle.textContent = contact.username;
                    loadMessages(contact.id); 
                }

                item.innerHTML = `
                    <img src="${contact.avatar_url}" alt="Avatar" class="avatar">
                    <span>${contact.username}</span>
                `;
                item.addEventListener('click', handleContactChange);
                contactsPanel.appendChild(item);
            });
            
            // Reinsertar estáticos al final (o donde prefieras)
            staticItems.forEach(item => contactsPanel.appendChild(item));

            // Iniciar sondeo (polling) para nuevos mensajes
            if (contacts.length > 0) {
                 setInterval(() => loadMessages(currentContactId, true), 3000); 
            }

        } catch (error) {
            console.error('Error al cargar contactos:', error);
        }
    };
    
    // --- Lógica de Envío de Mensajes ---
    const sendMessage = async () => {
        const messageText = messageInput.value.trim();
        if (messageText === '' || currentContactId === null) return; 

        createAndAppendMessage(messageText, 'sent', MY_AVATAR);
        messageInput.value = '';

        try {
            const formData = new FormData();
            formData.append('receiver_id', currentContactId);
            formData.append('message_text', messageText);
            formData.append('action', 'send_message'); 

            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                console.error('Error al guardar el mensaje:', result.error);
            }

        } catch (error) {
            console.error('Error de red al enviar mensaje:', error);
        }
    };

    // --- Listeners y Inicialización ---
    sendButton.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    loadContacts();
});