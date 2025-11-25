// ========== SISTEMA DE TRADUCCIONES ==========

const translations = {
    es: {
        // Navegación general
        'nav.home': 'Inicio',
        'nav.games': 'Juegos',
        'nav.ranking': 'Ranking',
        'nav.friends': 'Amigos',
        'nav.chats': 'Chats',
        'nav.profile': 'Perfil',
        'nav.admin': 'Admin',
        'nav.logout': 'Cerrar Sesión',
        'nav.language': 'Idioma',
        
        // Login
        'login.title': 'Iniciar Sesión',
        'login.username': 'Usuario',
        'login.password': 'Contraseña',
        'login.button': 'Ingresar',
        'login.noAccount': '¿No tienes cuenta?',
        'login.signup': 'Regístrate aquí',
        'login.error': 'Usuario o contraseña incorrectos',
        'login.loading': 'Cargando...',
        
        // Signup
        'signup.title': 'Crear Cuenta',
        'signup.username': 'Usuario',
        'signup.email': 'Correo',
        'signup.password': 'Contraseña',
        'signup.confirmPassword': 'Confirmar Contraseña',
        'signup.country': 'País',
        'signup.button': 'Registrarse',
        'signup.hasAccount': '¿Ya tienes cuenta?',
        'signup.login': 'Inicia sesión aquí',
        'signup.error': 'Error al registrarse',
        'signup.success': 'Cuenta creada exitosamente',
        
        // Chats
        'chats.title': 'Mensajes',
        'chats.noContact': 'Selecciona un contacto para empezar a chatear',
        'chats.search': 'Buscar contactos...',
        'chats.type': 'Escribe un mensaje...',
        'chats.send': 'Enviar',
        'chats.videocall': 'Videollamada',
        'chats.calling': 'Llamando...',
        'chats.callEnded': 'Llamada finalizada',
        'chats.acceptCall': 'Aceptar',
        'chats.rejectCall': 'Rechazar',
        'chats.incomingCall': 'está llamando...',
        'chats.cameraError': 'Error de cámara',
        'chats.microphoneError': 'Error de micrófono',
        'chats.noVideoSupport': 'Tu navegador no soporta videollamadas',
        
        // Friends
        'friends.title': 'Amigos',
        'friends.search': 'Buscar usuarios...',
        'friends.add': 'Agregar',
        'friends.remove': 'Eliminar',
        'friends.pending': 'Pendiente',
        'friends.accept': 'Aceptar',
        'friends.reject': 'Rechazar',
        'friends.noFriends': 'No tienes amigos aún',
        'friends.requests': 'Solicitudes de amistad',
        'friends.myFriends': 'Mis amigos',
        
        // Games
        'games.title': 'Juegos',
        'games.gameList': 'Lista de Juegos',
        'games.everyGame': 'Todos los Juegos',
        'games.mundial': 'Mundial de Fútbol',
        'games.bet': 'Apostar',
        'games.predict': 'Predecir Resultado',
        'games.leaderboard': 'Tabla de Posiciones',
        'games.points': 'Puntos',
        'games.predictions': 'Mis Predicciones',
        'games.noGames': 'No hay juegos disponibles',
        'games.phaseGroup': 'Fase de Grupos',
        'games.phaseRound16': 'Octavos de Final',
        'games.phaseQuarter': 'Cuartos de Final',
        'games.phaseSemi': 'Semifinal',
        'games.phaseFinal': 'Final',
        'games.matchday1': 'Jornada 1',
        'games.matchday2': 'Jornada 2',
        'games.matchday3': 'Jornada 3',
        'games.loadingMatches': 'Cargando partidos...',
        'games.saveAllPredictions': 'Guardar Todas las Predicciones',
        
        // Ranking
        'ranking.title': 'Ranking Global',
        'ranking.position': 'Posición',
        'ranking.player': 'Jugador',
        'ranking.points': 'Puntos',
        'ranking.country': 'País',
        'ranking.loadingData': 'Cargando ranking...',
        'ranking.noData': 'No hay datos disponibles',
        
        // Profile
        'profile.title': 'Mi Perfil',
        'profile.username': 'Usuario',
        'profile.email': 'Correo',
        'profile.country': 'País',
        'profile.joinDate': 'Se unió el',
        'profile.totalPoints': 'Puntos Totales',
        'profile.friends': 'Amigos',
        'profile.edit': 'Editar Perfil',
        'profile.changePassword': 'Cambiar Contraseña',
        'profile.save': 'Guardar',
        'profile.cancel': 'Cancelar',
        'profile.avatar': 'Avatar',
        'profile.selectPhoto': 'Seleccionar foto',
        
        // Admin
        'admin.title': 'Panel de Administración',
        'admin.backProfile': 'Volver al Perfil',
        'admin.finishedMatches': 'Partidos Finalizados:',
        'admin.pendingMatches': 'Partidos Pendientes:',
        'admin.totalPredictions': 'Total Predicciones:',
        'admin.setResults': 'Establecer Resultados de Partidos',
        'admin.loadingMatches': 'Cargando partidos...',
        'admin.users': 'Usuarios',
        'admin.games': 'Juegos',
        'admin.reports': 'Reportes',
        'admin.settings': 'Configuración',
        'admin.totalUsers': 'Total de Usuarios',
        'admin.activeGames': 'Juegos Activos',
        'admin.totalBets': 'Total de Apuestas',
        'admin.delete': 'Eliminar',
        'admin.edit': 'Editar',
        'admin.confirm': '¿Estás seguro?',
        
        // General
        'general.loading': 'Cargando...',
        'general.error': 'Error',
        'general.success': 'Éxito',
        'general.save': 'Guardar',
        'general.cancel': 'Cancelar',
        'general.delete': 'Eliminar',
        'general.edit': 'Editar',
        'general.close': 'Cerrar',
        'general.search': 'Buscar',
        'general.noResults': 'Sin resultados',
        'general.empty': 'Vacío',
        'general.offline': 'Sin conexión',
        'general.online': 'En línea',
    },
    en: {
        // Navigation
        'nav.home': 'Home',
        'nav.games': 'Games',
        'nav.ranking': 'Ranking',
        'nav.friends': 'Friends',
        'nav.chats': 'Chats',
        'nav.profile': 'Profile',
        'nav.admin': 'Admin',
        'nav.logout': 'Logout',
        'nav.language': 'Language',
        
        // Login
        'login.title': 'Login',
        'login.username': 'Username',
        'login.password': 'Password',
        'login.button': 'Sign In',
        'login.noAccount': "Don't have an account?",
        'login.signup': 'Sign up here',
        'login.error': 'Invalid username or password',
        'login.loading': 'Loading...',
        
        // Signup
        'signup.title': 'Create Account',
        'signup.username': 'Username',
        'signup.email': 'Email',
        'signup.password': 'Password',
        'signup.confirmPassword': 'Confirm Password',
        'signup.country': 'Country',
        'signup.button': 'Register',
        'signup.hasAccount': 'Already have an account?',
        'signup.login': 'Sign in here',
        'signup.error': 'Error creating account',
        'signup.success': 'Account created successfully',
        
        // Chats
        'chats.title': 'Messages',
        'chats.noContact': 'Select a contact to start chatting',
        'chats.search': 'Search contacts...',
        'chats.type': 'Type a message...',
        'chats.send': 'Send',
        'chats.videocall': 'Video Call',
        'chats.calling': 'Calling...',
        'chats.callEnded': 'Call ended',
        'chats.acceptCall': 'Accept',
        'chats.rejectCall': 'Reject',
        'chats.incomingCall': 'is calling...',
        'chats.cameraError': 'Camera error',
        'chats.microphoneError': 'Microphone error',
        'chats.noVideoSupport': 'Your browser does not support video calls',
        
        // Friends
        'friends.title': 'Friends',
        'friends.search': 'Search users...',
        'friends.add': 'Add',
        'friends.remove': 'Remove',
        'friends.pending': 'Pending',
        'friends.accept': 'Accept',
        'friends.reject': 'Reject',
        'friends.noFriends': 'You have no friends yet',
        'friends.requests': 'Friend Requests',
        'friends.myFriends': 'My Friends',
        
        // Games
        'games.title': 'Games',
        'games.gameList': 'Game List',
        'games.everyGame': 'Every Game Every Time',
        'games.mundial': 'Football World Cup',
        'games.bet': 'Bet',
        'games.predict': 'Predict Result',
        'games.leaderboard': 'Leaderboard',
        'games.points': 'Points',
        'games.predictions': 'My Predictions',
        'games.noGames': 'No games available',
        'games.phaseGroup': 'Group Stage',
        'games.phaseRound16': 'Round of 16',
        'games.phaseQuarter': 'Quarterfinals',
        'games.phaseSemi': 'Semifinals',
        'games.phaseFinal': 'Final',
        'games.matchday1': 'Matchday 1',
        'games.matchday2': 'Matchday 2',
        'games.matchday3': 'Matchday 3',
        'games.loadingMatches': 'Loading matches...',
        'games.saveAllPredictions': 'Save All Predictions',
        
        // Ranking
        'ranking.title': 'Global Ranking',
        'ranking.position': 'Position',
        'ranking.player': 'Player',
        'ranking.points': 'Points',
        'ranking.country': 'Country',
        'ranking.loadingData': 'Loading ranking...',
        'ranking.noData': 'No data available',
        
        // Profile
        'profile.title': 'My Profile',
        'profile.username': 'Username',
        'profile.email': 'Email',
        'profile.country': 'Country',
        'profile.joinDate': 'Joined',
        'profile.totalPoints': 'Total Points',
        'profile.friends': 'Friends',
        'profile.edit': 'Edit Profile',
        'profile.changePassword': 'Change Password',
        'profile.save': 'Save',
        'profile.cancel': 'Cancel',
        'profile.avatar': 'Avatar',
        'profile.selectPhoto': 'Select photo',
        
        // Admin
        'admin.title': 'Admin Panel',
        'admin.backProfile': 'Back to Profile',
        'admin.finishedMatches': 'Finished Matches:',
        'admin.pendingMatches': 'Pending Matches:',
        'admin.totalPredictions': 'Total Predictions:',
        'admin.setResults': 'Set Match Results',
        'admin.loadingMatches': 'Loading matches...',
        'admin.users': 'Users',
        'admin.games': 'Games',
        'admin.reports': 'Reports',
        'admin.settings': 'Settings',
        'admin.totalUsers': 'Total Users',
        'admin.activeGames': 'Active Games',
        'admin.totalBets': 'Total Bets',
        'admin.delete': 'Delete',
        'admin.edit': 'Edit',
        'admin.confirm': 'Are you sure?',
        
        // General
        'general.loading': 'Loading...',
        'general.error': 'Error',
        'general.success': 'Success',
        'general.save': 'Save',
        'general.cancel': 'Cancel',
        'general.delete': 'Delete',
        'general.edit': 'Edit',
        'general.close': 'Close',
        'general.search': 'Search',
        'general.noResults': 'No results',
        'general.empty': 'Empty',
        'general.offline': 'Offline',
        'general.online': 'Online',
    }
};

// ========== LANGUAGE MANAGER ==========

class LanguageManager {
    constructor() {
        this.currentLanguage = localStorage.getItem('language') || 'es';
        this.observers = [];
        // No call init() here - wait for DOM to be ready
        this.initOnDomReady();
    }

    initOnDomReady() {
        // Wait for DOM to be ready before applying language
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.applyLanguage(this.currentLanguage);
            });
        } else {
            // DOM is already ready
            this.applyLanguage(this.currentLanguage);
        }
    }

    setLanguage(lang) {
        if (translations[lang]) {
            this.currentLanguage = lang;
            localStorage.setItem('language', lang);
            this.applyLanguage(lang);
            this.notifyObservers();
        }
    }

    getLanguage() {
        return this.currentLanguage;
    }

    t(key, defaultValue = key) {
        const translation = translations[this.currentLanguage];
        if (translation && translation[key]) {
            return translation[key];
        }
        return defaultValue;
    }

    applyLanguage(lang) {
        // Cambiar atributo lang del documento
        document.documentElement.lang = lang;
        
        // Cambiar dirección del texto (LTR por defecto)
        document.documentElement.dir = 'ltr';
        
        // Traducir todos los elementos con data-i18n
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            element.textContent = this.t(key);
        });

        // Traducir placeholders
        document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
            const key = element.getAttribute('data-i18n-placeholder');
            element.placeholder = this.t(key);
        });

        // Traducir atributos title
        document.querySelectorAll('[data-i18n-title]').forEach(element => {
            const key = element.getAttribute('data-i18n-title');
            element.title = this.t(key);
        });

        // Traducir atributos aria-label
        document.querySelectorAll('[data-i18n-aria]').forEach(element => {
            const key = element.getAttribute('data-i18n-aria');
            element.setAttribute('aria-label', this.t(key));
        });
    }

    subscribe(callback) {
        this.observers.push(callback);
    }

    notifyObservers() {
        this.observers.forEach(callback => callback(this.currentLanguage));
    }
}

// Crear instancia global
const languageManager = new LanguageManager();
