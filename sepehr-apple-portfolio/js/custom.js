/**
 * Custom JS logic for Language switching, Dark/Light Mode, and general Apple animations
 */

jQuery(document).ready(function($) {
    var body = document.body;
    var button = document.querySelector('.st-theme-toggle');
    var key = 'st-theme-mode';
    var saved = localStorage.getItem(key);

    // Apply theme on load
    if (saved === 'light') {
        body.classList.add('light-mode');
        if (button) {
            var icon = button.querySelector('i');
            if (icon) icon.className = 'fa-regular fa-sun';
        }
    } else {
        body.classList.remove('light-mode');
        if (button) {
            var icon = button.querySelector('i');
            if (icon) icon.className = 'fa-regular fa-moon';
        }
    }

    if (button) {
        button.addEventListener('click', function () {
            body.classList.toggle('light-mode');
            var isLight = body.classList.contains('light-mode');
            localStorage.setItem(key, isLight ? 'light' : 'dark');
            var icon = button.querySelector('i');
            if (icon) {
                icon.className = isLight ? 'fa-regular fa-sun' : 'fa-regular fa-moon';
            }
        });
    }

    // Handle standard multi-language triggers
    var initialLang = localStorage.getItem('sepehr_portfolio_lang') || 'fa';
    setAppLanguage(initialLang);

    $(document).on('click', '.st-lang-switcher span, .st-lang-switcher a', function() {
        var lang = $(this).data('lang') || $(this).text().toLowerCase().trim();
        if (lang === 'fa' || lang === 'en' || lang === 'de') {
            setAppLanguage(lang);
        }
    });
});

/**
 * Handle App Multi-Language switching logic
 */
function setAppLanguage(lang) {
    localStorage.setItem('sepehr_portfolio_lang', lang);

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

    // Adjust document alignments and tags based on directionality
    var htmlTag = document.documentElement;
    if (lang === 'fa') {
        htmlTag.setAttribute('dir', 'rtl');
        htmlTag.setAttribute('lang', 'fa-IR');
        document.body.style.direction = "rtl";
        document.body.style.fontFamily = "'Vazirmatn', 'Inter', sans-serif";
        jQuery('.st-brand, .st-hero-copy, .st-card, .st-content-card, .entry-content, .st-footer-card').css('text-align', 'right');
    } else {
        htmlTag.setAttribute('dir', 'ltr');
        document.body.style.direction = "ltr";
        if (lang === 'de') {
            htmlTag.setAttribute('lang', 'de-DE');
        } else {
            htmlTag.setAttribute('lang', 'en-US');
        }
        document.body.style.fontFamily = "'Inter', 'Vazirmatn', sans-serif";
        jQuery('.st-brand, .st-hero-copy, .st-card, .st-content-card, .entry-content, .st-footer-card').css('text-align', 'left');
    }

    // Custom language element switching
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

    // Fire custom translator trigger for automatic Google translation (EN / DE)
    triggerAutoTranslation(lang);
}

/**
 * Handle Automatic Google Translate trigger for EN / DE
 */
function triggerAutoTranslation(lang) {
    if (lang === 'fa') {
        // Return back to original Persian
        var iframe = document.querySelector('.goog-te-banner-frame');
        if (iframe) {
            var doc = iframe.contentDocument || iframe.contentWindow.document;
            var restoreBtn = doc.getElementById(':1.restore') || doc.querySelector('.goog-te-button button');
            if (restoreBtn) restoreBtn.click();
        }
        return;
    }
    // Select translate select element and dispatch manual change events
    var translateSelect = document.querySelector('.goog-te-combo');
    if (translateSelect) {
        translateSelect.value = lang;
        translateSelect.dispatchEvent(new Event('change'));
    }
}
