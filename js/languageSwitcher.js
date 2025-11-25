// ========== LANGUAGE SWITCHER COMPONENT ==========

class LanguageSwitcher {
    constructor(containerId = 'languageSwitcher') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.warn('Language switcher container not found');
            return;
        }
        this.init();
    }

    init() {
        this.createSwitcher();
        this.attachEventListeners();
    }

    createSwitcher() {
        const currentLang = languageManager.getLanguage();
        
        this.container.innerHTML = `
            <div class="language-switcher">
                <button class="lang-btn ${currentLang === 'es' ? 'active' : ''}" data-lang="es">
                    <span class="lang-flag">🇪🇸</span>
                    <span class="lang-label">Español</span>
                </button>
                <button class="lang-btn ${currentLang === 'en' ? 'active' : ''}" data-lang="en">
                    <span class="lang-flag">🇺🇸</span>
                    <span class="lang-label">English</span>
                </button>
            </div>
        `;
    }

    attachEventListeners() {
        this.container.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const lang = e.currentTarget.getAttribute('data-lang');
                languageManager.setLanguage(lang);
                this.updateActiveButton();
            });
        });

        // Escuchar cambios de idioma
        languageManager.subscribe((newLang) => {
            this.updateActiveButton();
        });
    }

    updateActiveButton() {
        this.container.querySelectorAll('.lang-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        const currentLang = languageManager.getLanguage();
        this.container.querySelector(`[data-lang="${currentLang}"]`)?.classList.add('active');
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new LanguageSwitcher();
    });
} else {
    new LanguageSwitcher();
}
