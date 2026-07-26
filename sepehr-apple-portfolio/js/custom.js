/**
 * Custom JS logic for Language switching, Dark/Light Mode, and general Apple animations
 */

jQuery(document).ready(function($) {
    // Check local storage for language & theme preferences
    var initialLang = localStorage.getItem('sepehr_portfolio_lang') || 'fa';
    var initialTheme = localStorage.getItem('sepehr_portfolio_theme') || 'dark';

    // Apply language
    setAppLanguage(initialLang);

    // Apply theme
    applyThemeMode(initialTheme);

    // Toggle language dropdown list on click
    $('#langSelectorBtn').on('click', function(e) {
        e.stopPropagation();
        $('#langDropdown').toggleClass('show');
    });

    $(document).on('click', function() {
        $('#langDropdown').removeClass('show');
    });
});

/**
 * Handle Theme switching logic
 */
function applyThemeMode(theme) {
    var body = document.body;
    var themeIcon = document.getElementById('themeIcon');

    if (theme === 'light') {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        if (themeIcon) {
            themeIcon.className = 'fas fa-sun';
        }
    } else {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        if (themeIcon) {
            themeIcon.className = 'fas fa-moon';
        }
    }
}

function toggleThemeMode() {
    var currentTheme = document.body.classList.contains('light-mode') ? 'light' : 'dark';
    var newTheme = currentTheme === 'light' ? 'dark' : 'light';
    localStorage.setItem('sepehr_portfolio_theme', newTheme);
    applyThemeMode(newTheme);
}

/**
 * Handle App Multi-Language switching logic
 */
function setAppLanguage(lang) {
    // Store in localStorage
    localStorage.setItem('sepehr_portfolio_lang', lang);

    // Update active label text
    var labelMap = {
        'fa': 'فارسی',
        'en': 'English',
        'de': 'Deutsch'
    };
    var activeLabel = document.getElementById('activeLangLabel');
    if (activeLabel) {
        activeLabel.textContent = labelMap[lang] || 'فارسی';
    }

    // Toggle HTML direction based on RTL or LTR language
    var htmlTag = document.documentElement;
    if (lang === 'fa') {
        htmlTag.setAttribute('dir', 'rtl');
        htmlTag.setAttribute('lang', 'fa-IR');
        document.body.style.fontFamily = "'Vazirmatn', 'Inter', sans-serif";
    } else {
        htmlTag.setAttribute('dir', 'ltr');
        if (lang === 'de') {
            htmlTag.setAttribute('lang', 'de-DE');
        } else {
            htmlTag.setAttribute('lang', 'en-US');
        }
        document.body.style.fontFamily = "'Inter', sans-serif";
    }

    // Toggle visible elements inside document containing class tags
    var allLangTexts = document.querySelectorAll('.lang-text');
    allLangTexts.forEach(function(el) {
        el.style.display = 'none';
    });

    var selectedLangTexts = document.querySelectorAll('.' + lang + '-text');
    selectedLangTexts.forEach(function(el) {
        // Handle inline layout preservation
        if (el.tagName === 'SPAN') {
            el.style.display = 'inline';
        } else if (el.tagName === 'LI') {
            el.style.display = 'list-item';
        } else if (el.tagName === 'P' || el.tagName === 'DIV') {
            el.style.display = 'block';
        } else {
            el.style.display = 'initial';
        }
    });
}
