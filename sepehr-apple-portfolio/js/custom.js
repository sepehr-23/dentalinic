/**
 * Custom JS logic for Language switching, Dark/Light Mode, and general Apple animations
 */

jQuery(document).ready(function($) {
    // Read the actual direction and language of the document as initially loaded by WP
    var docLang = document.documentElement.getAttribute('lang') || 'fa';
    if (docLang.indexOf('de') === 0) {
        docLang = 'de';
    } else if (docLang.indexOf('en') === 0) {
        docLang = 'en';
    } else {
        docLang = 'fa';
    }

    // Check local storage for language & theme preferences, fallback to document default
    var initialLang = localStorage.getItem('sepehr_portfolio_lang') || docLang;
    var initialTheme = localStorage.getItem('sepehr_portfolio_theme') || 'dark';

    // Apply language and theme mode
    setAppLanguage(initialLang);
    applyThemeMode(initialTheme);

    // Toggle theme button listener
    $('.st-theme-toggle').on('click', function() {
        toggleThemeMode();
    });

    // Custom language switching clicks inside standard controls
    $(document).on('click', '.st-lang-switcher span, .st-lang-switcher a', function() {
        var lang = $(this).data('lang') || $(this).text().toLowerCase().trim();
        if (lang === 'fa' || lang === 'en' || lang === 'de') {
            setAppLanguage(lang);
        }
    });
});

/**
 * Handle Theme switching logic
 */
function applyThemeMode(theme) {
    var body = document.body;
    var toggleBtn = document.querySelector('.st-theme-toggle-icon');

    if (theme === 'light') {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        if (toggleBtn) {
            toggleBtn.textContent = '☼';
        }
    } else {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        if (toggleBtn) {
            toggleBtn.textContent = '◐';
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
 * Handle App Multi-Language switching logic with fully clean style direction integration
 */
function setAppLanguage(lang) {
    // Store in localStorage
    localStorage.setItem('sepehr_portfolio_lang', lang);

    // Update active state in switcher element
    var switcherContainer = document.querySelector('.st-lang-switcher');
    if (switcherContainer) {
        var children = switcherContainer.children;
        for (var i = 0; i < children.length; i++) {
            var child = children[i];
            var childLang = child.getAttribute('data-lang') || child.textContent.toLowerCase().trim();
            if (childLang === lang) {
                child.classList.add('is-active');
            } else {
                child.classList.remove('is-active');
            }
        }
    }

    // Toggle HTML direction based on RTL or LTR language
    var htmlTag = document.documentElement;
    if (lang === 'fa') {
        htmlTag.setAttribute('dir', 'rtl');
        htmlTag.setAttribute('lang', 'fa-IR');
        document.body.classList.remove('st-ltr');
        document.body.classList.add('st-rtl');
        document.body.style.fontFamily = "'Vazirmatn', 'Inter', sans-serif";
    } else {
        htmlTag.setAttribute('dir', 'ltr');
        document.body.classList.remove('st-rtl');
        document.body.classList.add('st-ltr');
        if (lang === 'de') {
            htmlTag.setAttribute('lang', 'de-DE');
        } else {
            htmlTag.setAttribute('lang', 'en-US');
        }
        document.body.style.fontFamily = "'Inter', 'Vazirmatn', sans-serif";
    }

    // Toggle visible elements inside document containing class tags
    var allLangTexts = document.querySelectorAll('.lang-text');
    allLangTexts.forEach(function(el) {
        el.style.display = 'none';
    });

    var selectedLangTexts = document.querySelectorAll('.' + lang + '-text');
    selectedLangTexts.forEach(function(el) {
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
