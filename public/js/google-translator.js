/**
 * Google Translate Full Page Translator for RIVANA
 * Exact implementation adapted from nusantara-karya
 */

// Function called by Google Translate script callback
function googleTranslateElementInit() {
    try {
        if (window.google && window.google.translate && window.google.translate.TranslateElement) {
            new window.google.translate.TranslateElement({
                pageLanguage: 'id',
                includedLanguages: 'id,en',
                autoDisplay: false
            }, 'google_translate_element');
        }
    } catch (e) {
        console.warn('Google Translate initialization error:', e);
    }
}

// Function to switch language
window.changeLanguage = function(lang) {
    localStorage.setItem('site_lang', lang);

    if (lang === 'en') {
        // Set google translate cookie for English (matching nusantara-karya)
        document.cookie = "googtrans=/id/en; path=/";
        document.cookie = "googtrans=/id/en; domain=" + window.location.hostname + "; path=/";
        document.cookie = "googtrans=/auto/en; path=/";
        document.cookie = "googtrans=/auto/en; domain=" + window.location.hostname + "; path=/";
    } else {
        // Reset to original Indonesian (matching nusantara-karya)
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=" + window.location.hostname + "; path=/;";
        document.cookie = "googtrans=/id/id; path=/";
        document.cookie = "googtrans=/auto/id; path=/";
    }

    // Trigger Google Translate select change if available, or reload
    const selectElem = document.querySelector('.goog-te-combo');
    if (selectElem) {
        selectElem.value = lang;
        selectElem.dispatchEvent(new Event('change'));
    } else {
        // Reload to let Google translate apply instantly across the full DOM
        window.location.reload();
    }
    updateActiveLanguageIndicator();
};

function getActiveLanguage() {
    const match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]*)/);
    if (match) {
        const val = decodeURIComponent(match[1]);
        if (val.includes('/id/en') || val.includes('/auto/en')) return 'en';
    }
    return localStorage.getItem('site_lang') || 'id';
}

function updateActiveLanguageIndicator() {
    const lang = getActiveLanguage();
    const isEn = lang === 'en';

    document.querySelectorAll('[data-lang-label]').forEach(el => {
        el.textContent = isEn ? 'English 🇬🇧' : 'Indonesia 🇮🇩';
    });
    document.querySelectorAll('[data-lang-flag]').forEach(el => {
        el.textContent = isEn ? '🇬🇧' : '🇮🇩';
    });
    document.querySelectorAll('[data-lang-short]').forEach(el => {
        el.textContent = isEn ? 'EN 🇬🇧' : 'ID 🇮🇩';
    });
    document.querySelectorAll('[data-lang-opt]').forEach(el => {
        const opt = el.getAttribute('data-lang-opt');
        if ((isEn && opt === 'en') || (!isEn && opt === 'id')) {
            el.classList.add('bg-blue-50', 'font-bold');
        } else {
            el.classList.remove('bg-blue-50', 'font-bold');
        }
    });
    document.querySelectorAll('[data-lang-check]').forEach(el => {
        const checkLang = el.getAttribute('data-lang-check');
        if ((isEn && checkLang === 'en') || (!isEn && checkLang === 'id')) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

// Initial check and combo auto-sync
document.addEventListener('DOMContentLoaded', () => {
    updateActiveLanguageIndicator();
    const lang = getActiveLanguage();
    if (lang === 'en') {
        const checkInterval = setInterval(() => {
            const selectElem = document.querySelector('.goog-te-combo');
            if (selectElem) {
                if (selectElem.value !== 'en') {
                    selectElem.value = 'en';
                    selectElem.dispatchEvent(new Event('change'));
                }
                clearInterval(checkInterval);
            }
        }, 300);
        setTimeout(() => clearInterval(checkInterval), 5000);
    }
});
