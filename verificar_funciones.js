// SCRIPT DE VERIFICACIÓN - Pega esto en la consola del navegador (F12)
// cuando estés en chats.html

console.log('%c🔍 VERIFICACIÓN DE FUNCIONES DEL CHAT', 'background: #4CAF50; color: white; font-size: 16px; padding: 10px;');

const tests = {
    'createToolbar': typeof createToolbar !== 'undefined',
    'createEmojiPicker': typeof createEmojiPicker !== 'undefined',
    'updateUnreadCounts': typeof updateUnreadCounts !== 'undefined',
    'showTypingIndicator': typeof showTypingIndicator !== 'undefined',
    'checkTypingStatus': typeof checkTypingStatus !== 'undefined',
    'setTypingStatus': typeof setTypingStatus !== 'undefined',
    'toggleSearchMode': typeof toggleSearchMode !== 'undefined',
    'searchMessages': typeof searchMessages !== 'undefined',
    'toggleEmojiPicker': typeof toggleEmojiPicker !== 'undefined',
    'insertEmoji': typeof insertEmoji !== 'undefined',
    'triggerFileUpload': typeof triggerFileUpload !== 'undefined',
    'handleFileUpload': typeof handleFileUpload !== 'undefined',
    'createAndAppendMessage': typeof createAndAppendMessage !== 'undefined',
    'loadMessages': typeof loadMessages !== 'undefined',
    'markMessagesAsRead': typeof markMessagesAsRead !== 'undefined',
    'handleContactChange': typeof handleContactChange !== 'undefined',
    'loadContacts': typeof loadContacts !== 'undefined',
    'loadGroups': typeof loadGroups !== 'undefined',
    'sendMessage': typeof sendMessage !== 'undefined'
};

console.table(tests);

// Verificar elementos del DOM
console.log('%c📊 ELEMENTOS DEL DOM', 'background: #2196F3; color: white; font-size: 14px; padding: 5px;');
console.log('Message Input:', document.querySelector('.message-input-area input[type="text"]'));
console.log('Send Button:', document.querySelector('.message-input-area .send-btn'));
console.log('Messages Area:', document.querySelector('.messages-area'));
console.log('Contacts Panel:', document.querySelector('.contacts-panel'));
console.log('Chat Header:', document.querySelector('.chat-header'));
console.log('Toolbar:', document.querySelector('.chat-toolbar'));
console.log('Emoji Picker:', document.getElementById('emoji-picker'));

// Verificar variables globales
console.log('%c🌐 VARIABLES GLOBALES', 'background: #FF9800; color: white; font-size: 14px; padding: 5px;');
console.log('currentContactId:', typeof currentContactId !== 'undefined' ? currentContactId : '❌ No definida');
console.log('currentChatType:', typeof currentChatType !== 'undefined' ? currentChatType : '❌ No definida');
console.log('MY_USER_ID:', typeof MY_USER_ID !== 'undefined' ? MY_USER_ID : '❌ No definida');
console.log('MY_AVATAR:', typeof MY_AVATAR !== 'undefined' ? MY_AVATAR : '❌ No definida');

console.log('%c✅ VERIFICACIÓN COMPLETA', 'background: #4CAF50; color: white; font-size: 16px; padding: 10px;');
