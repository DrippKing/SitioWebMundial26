document.addEventListener('DOMContentLoaded', async () => {
    const API_URL = '../php/friends.php';
    
    // Referencias a elementos del DOM
    const searchInput = document.querySelector('.search-bar input');
    const searchBtn = document.querySelector('.search-btn');
    const resultsList = document.querySelector('.results-list');
    const friendsList = document.querySelector('.friends-section:first-of-type .user-list');
    const requestsList = document.querySelector('.friends-section:nth-of-type(2) .user-list');
    const viewProfileBtn = document.querySelector('.view-profile');
    const addFriendBtn = document.querySelector('.add-friend');
    
    let selectedUserId = null;
    let selectedUserData = null;
    let myUserId = null;
    
    // ========================================
    // INICIALIZACIÓN
    // ========================================
    
    const init = async () => {
        await loadFriends();
        await loadFriendRequests();
        await searchUsers(); // Cargar todos los usuarios inicialmente
        
        // Actualizar cada 3 segundos
        setInterval(async () => {
            await loadFriends();
            await loadFriendRequests();
        }, 3000);
    };
    
    // ========================================
    // CARGAR AMIGOS
    // ========================================
    
    const loadFriends = async () => {
        try {
            const response = await fetch(`${API_URL}?action=get_friends`);
            const friends = await response.json();
            
            if (friends.error) {
                console.error('Error:', friends.error);
                return;
            }
            
            friendsList.innerHTML = '';
            
            if (friends.length === 0) {
                friendsList.innerHTML = '<li style="text-align:center; color:#999; padding:20px;">No tienes amigos aún</li>';
                return;
            }
            
            friends.forEach(friend => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <img src="${friend.avatar}" alt="Avatar" class="avatar" onerror="this.src='../pictures/default.jpg'">
                    <span>${friend.username}</span>
                    ${friend.unread_count > 0 ? `<span class="unread-badge">${friend.unread_count}</span>` : ''}
                    <button class="btn-icon btn-remove" data-friend-id="${friend.id}" title="Eliminar amigo">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Click en amigo para abrir chat
                li.querySelector('span').style.cursor = 'pointer';
                li.querySelector('span').addEventListener('click', () => {
                    window.location.href = `chats.html?contact=${friend.id}`;
                });
                
                // Botón eliminar
                li.querySelector('.btn-remove').addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (confirm(`¿Eliminar a ${friend.username} de tus amigos?`)) {
                        removeFriend(friend.id);
                    }
                });
                
                friendsList.appendChild(li);
            });
        } catch (error) {
            console.error('Error al cargar amigos:', error);
        }
    };
    
    // ========================================
    // CARGAR SOLICITUDES
    // ========================================
    
    const loadFriendRequests = async () => {
        try {
            const response = await fetch(`${API_URL}?action=get_requests`);
            const requests = await response.json();
            
            if (requests.error) {
                console.error('Error:', requests.error);
                return;
            }
            
            requestsList.innerHTML = '';
            
            if (requests.length === 0) {
                requestsList.innerHTML = '<li style="text-align:center; color:#999; padding:20px;">Sin solicitudes pendientes</li>';
                return;
            }
            
            requests.forEach(request => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <img src="${request.avatar}" alt="Avatar" class="avatar" onerror="this.src='../pictures/default.jpg'">
                    <span>${request.username}</span>
                    <button class="btn-icon btn-accept" data-request-id="${request.id}" title="Aceptar">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn-icon btn-decline" data-request-id="${request.id}" title="Rechazar">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Botón aceptar
                li.querySelector('.btn-accept').addEventListener('click', () => {
                    respondToRequest(request.id, 'accept');
                });
                
                // Botón rechazar
                li.querySelector('.btn-decline').addEventListener('click', () => {
                    respondToRequest(request.id, 'reject');
                });
                
                requestsList.appendChild(li);
            });
        } catch (error) {
            console.error('Error al cargar solicitudes:', error);
        }
    };
    
    // ========================================
    // BUSCAR USUARIOS
    // ========================================
    
    const searchUsers = async () => {
        const query = searchInput.value.trim();
        
        // Si no hay query, buscar todos los usuarios (query vacío)
        try {
            const url = query.length > 0 
                ? `${API_URL}?action=search_users&query=${encodeURIComponent(query)}`
                : `${API_URL}?action=search_users&query=`; // Query vacío trae todos
            
            const response = await fetch(url);
            const users = await response.json();
            
            resultsList.innerHTML = '';
            
            if (users.length === 0) {
                resultsList.innerHTML = '<li style="text-align:center; color:#999; padding:20px;">No se encontraron usuarios</li>';
                return;
            }
            
            users.forEach(user => {
                const li = document.createElement('li');
                li.setAttribute('data-user-id', user.id);
                
                let statusBadge = '';
                if (user.friendship_status === 'friend') {
                    statusBadge = '<span style="color:#4CAF50; font-size:0.8em;">✓ Amigo</span>';
                } else if (user.friendship_status === 'request_sent') {
                    statusBadge = '<span style="color:#FF9800; font-size:0.8em;">⏳ Solicitud enviada</span>';
                } else if (user.friendship_status === 'request_received') {
                    statusBadge = '<span style="color:#2196F3; font-size:0.8em;">📨 Te envió solicitud</span>';
                }
                
                li.innerHTML = `
                    <img src="${user.avatar}" alt="Avatar" class="avatar" onerror="this.src='../pictures/default.jpg'">
                    <span>${user.username}</span>
                    ${statusBadge}
                `;
                
                li.addEventListener('click', () => {
                    // Quitar selección anterior
                    document.querySelectorAll('.results-list li').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Seleccionar este usuario
                    li.classList.add('active');
                    selectedUserId = user.id;
                    selectedUserData = user;
                    
                    // Actualizar botones
                    updateActionButtons(user);
                });
                
                resultsList.appendChild(li);
            });
        } catch (error) {
            console.error('Error al buscar usuarios:', error);
            resultsList.innerHTML = '<li style="text-align:center; color:#f44336; padding:20px;">Error al buscar</li>';
        }
    };
    
    // ========================================
    // ACTUALIZAR BOTONES DE ACCIÓN
    // ========================================
    
    const updateActionButtons = (user) => {
        viewProfileBtn.disabled = false;
        addFriendBtn.disabled = false;
        
        if (user.friendship_status === 'friend') {
            addFriendBtn.textContent = 'YA SON AMIGOS';
            addFriendBtn.disabled = true;
            addFriendBtn.style.background = '#4CAF50';
        } else if (user.friendship_status === 'request_sent') {
            addFriendBtn.textContent = 'SOLICITUD ENVIADA';
            addFriendBtn.disabled = true;
            addFriendBtn.style.background = '#FF9800';
        } else if (user.friendship_status === 'request_received') {
            addFriendBtn.textContent = 'ACEPTAR SOLICITUD';
            addFriendBtn.disabled = false;
            addFriendBtn.style.background = '#2196F3';
        } else {
            addFriendBtn.textContent = 'ADD FRIEND';
            addFriendBtn.disabled = false;
            addFriendBtn.style.background = '';
        }
    };
    
    // ========================================
    // ENVIAR SOLICITUD DE AMISTAD
    // ========================================
    
    const sendFriendRequest = async () => {
        if (!selectedUserId || !selectedUserData) {
            alert('Selecciona un usuario primero');
            return;
        }
        
        // Si ya tiene solicitud recibida, aceptarla directamente
        if (selectedUserData.friendship_status === 'request_received') {
            const requests = await (await fetch(`${API_URL}?action=get_requests`)).json();
            const request = requests.find(r => r.sender_id === selectedUserId);
            if (request) {
                respondToRequest(request.id, 'accept');
                return;
            }
        }
        
        if (selectedUserData.friendship_status !== 'none') {
            return; // Ya son amigos o ya hay solicitud
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'send_request');
            formData.append('receiver_id', selectedUserId);
            
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`Solicitud enviada a ${selectedUserData.username}`);
                searchUsers(); // Recargar resultados
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error al enviar solicitud:', error);
            alert('Error al enviar solicitud');
        }
    };
    
    // ========================================
    // RESPONDER SOLICITUD
    // ========================================
    
    const respondToRequest = async (requestId, response) => {
        try {
            const formData = new FormData();
            formData.append('action', 'respond_request');
            formData.append('request_id', requestId);
            formData.append('response', response);
            
            const result = await (await fetch(API_URL, {
                method: 'POST',
                body: formData
            })).json();
            
            if (result.success) {
                await loadFriendRequests();
                await loadFriends();
                
                if (selectedUserId) {
                    searchUsers(); // Actualizar resultados de búsqueda
                }
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error al responder solicitud:', error);
        }
    };
    
    // ========================================
    // ELIMINAR AMIGO
    // ========================================
    
    const removeFriend = async (friendId) => {
        try {
            const formData = new FormData();
            formData.append('action', 'remove_friend');
            formData.append('friend_id', friendId);
            
            const result = await (await fetch(API_URL, {
                method: 'POST',
                body: formData
            })).json();
            
            if (result.success) {
                await loadFriends();
                if (selectedUserId) {
                    searchUsers(); // Actualizar resultados
                }
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error al eliminar amigo:', error);
        }
    };
    
    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    searchBtn.addEventListener('click', searchUsers);
    
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            searchUsers();
        }
    });
    
    addFriendBtn.addEventListener('click', sendFriendRequest);
    
    viewProfileBtn.addEventListener('click', () => {
        if (selectedUserId) {
            // Por ahora solo mostrar alerta, puedes implementar modal de perfil
            alert('Ver perfil - Funcionalidad próximamente');
        }
    });
    
    // ========================================
    // INICIAR
    // ========================================
    
    init();
});
