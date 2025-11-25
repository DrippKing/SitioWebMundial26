// ========== TRANSLATION SYSTEM DEBUG & TEST ==========
// Add this to your HTML file temporarily to test translations
// Or run in the browser console (F12) on any page with translations.js loaded

console.group('🌐 Translation System Debug');

// 1. Check if LanguageManager exists
console.log('✅ LanguageManager loaded:', typeof LanguageManager !== 'undefined');
console.log('✅ languageManager instance:', typeof languageManager !== 'undefined');

// 2. Check current language
if (typeof languageManager !== 'undefined') {
    const currentLang = languageManager.getLanguage();
    console.log(`Current Language: ${currentLang === 'es' ? '🇪🇸 Spanish' : '🇺🇸 English'}`);
}

// 3. Check localStorage
const savedLang = localStorage.getItem('language');
console.log('Saved in localStorage:', savedLang || '(not set - will use default)');

// 4. Count data-i18n elements
const translatableElements = document.querySelectorAll('[data-i18n]');
console.log(`Found ${translatableElements.length} translatable elements [data-i18n]`);

if (translatableElements.length > 0) {
    console.log('Sample elements:');
    Array.from(translatableElements).slice(0, 5).forEach(el => {
        const key = el.getAttribute('data-i18n');
        const text = el.textContent.substring(0, 50);
        console.log(`  • [${key}] = "${text}${text.length === 50 ? '...' : ''}"`);
    });
}

// 5. Check for data-i18n-placeholder elements
const placeholderElements = document.querySelectorAll('[data-i18n-placeholder]');
console.log(`Found ${placeholderElements.length} elements with [data-i18n-placeholder]`);

// 6. Check for data-i18n-title elements
const titleElements = document.querySelectorAll('[data-i18n-title]');
console.log(`Found ${titleElements.length} elements with [data-i18n-title]`);

// 7. Test translation function
if (typeof languageManager !== 'undefined') {
    console.group('Testing translation function t():');
    const testKeys = [
        'nav.home',
        'login.title',
        'chats.title',
        'games.gameList',
        'ranking.title',
        'admin.title'
    ];
    testKeys.forEach(key => {
        try {
            const es = languageManager.currentLanguage === 'es' 
                ? languageManager.t(key) 
                : translations['es'][key];
            const en = languageManager.currentLanguage === 'en' 
                ? languageManager.t(key) 
                : translations['en'][key];
            console.log(`  ${key}`);
            console.log(`    🇪🇸 ${es}`);
            console.log(`    🇺🇸 ${en}`);
        } catch (e) {
            console.warn(`  ❌ ${key} - Error:`, e.message);
        }
    });
    console.groupEnd();
}

// 8. Language Switcher check
const switcherContainer = document.getElementById('languageSwitcher');
console.log('Language switcher container exists:', switcherContainer !== null);
if (switcherContainer) {
    const buttons = switcherContainer.querySelectorAll('.lang-btn');
    console.log(`Language switcher has ${buttons.length} buttons`);
    buttons.forEach(btn => {
        const lang = btn.getAttribute('data-lang');
        const isActive = btn.classList.contains('active');
        console.log(`  • ${lang === 'es' ? '🇪🇸' : '🇺🇸'} ${lang.toUpperCase()} - ${isActive ? '(ACTIVE)' : '(inactive)'}`);
    });
}

// 9. Test applyLanguage function
console.group('Testing applyLanguage() function:');
if (typeof languageManager !== 'undefined') {
    const testElement = document.querySelector('[data-i18n]');
    if (testElement) {
        const key = testElement.getAttribute('data-i18n');
        const originalText = testElement.textContent;
        
        // Get the other language
        const currentLang = languageManager.getLanguage();
        const otherLang = currentLang === 'es' ? 'en' : 'es';
        
        console.log(`Current text [${key}]: "${originalText}"`);
        console.log(`Switching to ${otherLang === 'es' ? '🇪🇸 Spanish' : '🇺🇸 English'}...`);
        
        // Change language
        languageManager.setLanguage(otherLang);
        
        // Check if text changed
        const newText = testElement.textContent;
        console.log(`New text [${key}]: "${newText}"`);
        
        if (originalText !== newText) {
            console.log('✅ Translation SUCCESSFUL! Text changed.');
        } else {
            console.log('❌ Translation FAILED! Text did not change.');
        }
        
        // Change back
        languageManager.setLanguage(currentLang);
    }
}
console.groupEnd();

// 10. Summary
console.group('📋 Summary');
console.log('If you see mostly ✅ above, the translation system is working!');
console.log('If you see ❌, check:');
console.log('  1. Scripts are in correct order: translations.js → languageSwitcher.js');
console.log('  2. HTML elements have data-i18n attributes');
console.log('  3. Translation keys exist in translations.js');
console.log('  4. No JavaScript errors in console');
console.groupEnd();

console.groupEnd();

// ========== QUICK TEST FUNCTIONS ==========
// Call these in console to test specific functionality

function testLanguageSwitch() {
    console.log('Testing language switch...');
    const current = languageManager.getLanguage();
    const target = current === 'es' ? 'en' : 'es';
    console.log(`Switching from ${current} to ${target}`);
    languageManager.setLanguage(target);
    console.log(`✅ Language switched to ${target}`);
    console.log(`localStorage now has: ${localStorage.getItem('language')}`);
}

function listAllTranslationKeys() {
    console.log('All Spanish translation keys:');
    const keys = Object.keys(translations.es);
    keys.forEach((key, i) => {
        if (i % 3 === 0) console.log('');
        process.stdout.write(`${key.padEnd(30)} `);
    });
    console.log(`\n\nTotal keys: ${keys.length}`);
}

function testElementTranslation(key) {
    const element = document.querySelector(`[data-i18n="${key}"]`);
    if (element) {
        console.log(`Found element for key "${key}"`);
        console.log('Element:', element);
        console.log('Current text:', element.textContent);
        console.log('Spanish:', translations.es[key]);
        console.log('English:', translations.en[key]);
    } else {
        console.warn(`No element found with data-i18n="${key}"`);
    }
}

console.log('\n💡 Available test functions:');
console.log('  • testLanguageSwitch() - Test switching language');
console.log('  • testElementTranslation("key.name") - Test a specific translation key');
console.log('  • languageManager.t("key.name") - Get translation for a key');
