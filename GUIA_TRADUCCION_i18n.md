# Guía de Sistema de Traducciones (i18n)

## Descripción

Sistema completo de traducción (i18n) para cambiar entre Español e Inglés en toda la aplicación.

## Archivos Creados

1. **`js/translations.js`** - Diccionario de traducciones y clase `LanguageManager`
2. **`js/languageSwitcher.js`** - Componente visual para cambiar idioma
3. **`css/language.css`** - Estilos para el selector de idioma

## Cómo Integrar en una Página

### 1. Agregar los Scripts en el HEAD

```html
<link rel="stylesheet" href="../css/language.css">

<!-- Al final del body, antes de cerrar -->
<script src="../js/translations.js"></script>
<script src="../js/languageSwitcher.js"></script>
```

### 2. Agregar el Contenedor del Language Switcher

En el header o navbar donde desees que aparezca:

```html
<div id="languageSwitcher"></div>
```

El contenedor debe tener el id `languageSwitcher` (puedes cambiar el ID pasándolo al constructor).

### 3. Traducir Elementos HTML

#### Opción A: Usar `data-i18n`

Para traducir el texto de un elemento:

```html
<!-- Antes -->
<h1>Chats</h1>

<!-- Después -->
<h1 data-i18n="chats.title"></h1>
```

#### Opción B: Usar `data-i18n-placeholder`

Para traducir placeholders en inputs:

```html
<!-- Antes -->
<input type="text" placeholder="Buscar contactos...">

<!-- Después -->
<input type="text" data-i18n-placeholder="chats.search">
```

#### Opción C: Usar `data-i18n-title`

Para traducir atributos title:

```html
<!-- Antes -->
<button title="Enviar mensaje">Send</button>

<!-- Después -->
<button data-i18n-title="chats.send">Send</button>
```

#### Opción D: Usar `data-i18n-aria`

Para traducir atributos aria-label:

```html
<!-- Antes -->
<button aria-label="Cerrar">X</button>

<!-- Después -->
<button data-i18n-aria="general.close">X</button>
```

### 4. Traducir Texto en JavaScript

```javascript
// Obtener una traducción
const mensaje = languageManager.t('chats.noContact');
console.log(mensaje); // "Selecciona un contacto para empezar a chatear"

// O en inglés
languageManager.setLanguage('en');
const mensajeEn = languageManager.t('chats.noContact');
console.log(mensajeEn); // "Select a contact to start chatting"
```

### 5. Suscribirse a Cambios de Idioma

Si necesitas ejecutar código cuando cambie el idioma:

```javascript
languageManager.subscribe((nuevoIdioma) => {
    console.log('Idioma cambiado a:', nuevoIdioma);
    // Hacer algo cuando el idioma cambie
});
```

## Ejemplos de Uso

### Ejemplo 1: Traducir un Título

```html
<h2 data-i18n="friends.title"></h2>
```

Mostrará "Amigos" en español y "Friends" en inglés.

### Ejemplo 2: Traducir un Input

```html
<input type="text" data-i18n-placeholder="games.leaderboard">
```

El placeholder será "Tabla de Posiciones" en español y "Leaderboard" en inglés.

### Ejemplo 3: Traducir en JavaScript (Chat)

```javascript
const contactoNoSeleccionado = languageManager.t('chats.noContact');
document.getElementById('chatArea').textContent = contactoNoSeleccionado;
```

### Ejemplo 4: Agregar Nuevas Traducciones

En `js/translations.js`, agregar la llave en ambos idiomas:

```javascript
const translations = {
    es: {
        'miModulo.miTexto': 'Mi texto en español',
        // ...
    },
    en: {
        'miModulo.miTexto': 'My text in English',
        // ...
    }
};
```

Luego usarlo:

```html
<div data-i18n="miModulo.miTexto"></div>
```

O en JavaScript:

```javascript
languageManager.t('miModulo.miTexto');
```

## Idiomas Disponibles

- **es** - Español (por defecto)
- **en** - English

## Almacenamiento

El idioma seleccionado se guarda en `localStorage` como `language`. La próxima vez que el usuario visite la página, su idioma preferido será restaurado automáticamente.

## CSS Personalizable

El selector de idioma tiene las siguientes clases CSS que puedes personalizar:

- `.language-switcher` - Contenedor principal
- `.lang-btn` - Botón de idioma
- `.lang-btn.active` - Botón activo
- `.lang-flag` - Flag/emoji
- `.lang-label` - Texto del idioma

### Ejemplo de Personalización

```css
.lang-btn {
    padding: 10px 20px;
    background: #f0f0f0;
    border-radius: 50px;
}

.lang-btn.active {
    background: #667eea;
    color: white;
}
```

## Cómo Funciona

1. **`LanguageManager`** - Clase que gestiona las traducciones
   - `setLanguage(lang)` - Cambia el idioma y aplica las traducciones
   - `t(key)` - Obtiene una traducción por clave
   - `subscribe(callback)` - Se ejecuta cuando cambia el idioma

2. **`LanguageSwitcher`** - Componente visual
   - Crea botones para español e inglés
   - Maneja clics en los botones
   - Muestra qué idioma está activo

## Cosas Importantes

✅ El idioma se cambia instantáneamente en toda la página
✅ Se guarda la preferencia del usuario en localStorage
✅ Responsive - En móviles solo muestra el flag
✅ Compatible con dark mode
✅ Accesible - Soporta aria-label

## Checklist de Integración

- [ ] Agregar `<link rel="stylesheet" href="../css/language.css">`
- [ ] Agregar `<script src="../js/translations.js"></script>` antes de cerrar body
- [ ] Agregar `<script src="../js/languageSwitcher.js"></script>` antes de cerrar body
- [ ] Agregar `<div id="languageSwitcher"></div>` en el header/navbar
- [ ] Traducir títulos con `data-i18n`
- [ ] Traducir placeholders con `data-i18n-placeholder`
- [ ] Agregar claves de traducción a `js/translations.js` si falta algo

## Páginas Actualizadas

- ✅ `chats.html` - Totalmente integrado

## Páginas Pendientes de Integración

- `index.html`
- `login.html`
- `signup.html`
- `profile.html`
- `friends.html`
- `games.html`
- `ranking.html`
- `admin.html`

---

**Nota:** Puedes agregar más idiomas en `js/translations.js` siguiendo el mismo patrón.
