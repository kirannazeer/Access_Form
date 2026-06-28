/**
 * AccessForm - Accessibility Engine + Admin Compliance Logging
 */

let screenReaderActive = false;

// ==================== ACCESSIBILITY LOGGING FOR ADMIN ====================
function logAccessibility(action, formId = null) {
    // Only log meaningful actions
    const validActions = ['high-contrast', 'dyslexia', 'default', 'large-text', 'voice-reader'];
    if (!validActions.includes(action)) return;

    const data = {
        action: action,
        form_id: formId || null,
        timestamp: new Date().toISOString()
    };

    fetch('./log_accessibility.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .catch(err => {
        console.warn('Accessibility log failed (no impact on user):', err);
    });
}

// 1. Theme Switcher with Logging
function setTheme(themeName) {
    document.body.classList.remove('theme-high-contrast', 'theme-dyslexia');
    
    if (themeName === 'high-contrast') {
        document.body.classList.add('theme-high-contrast');
    } else if (themeName === 'dyslexia') {
        document.body.classList.add('theme-dyslexia');
    }
    
    localStorage.setItem('accessform_theme', themeName);
    syncAccessibilityMenuUI(themeName);
    
    // Log for Admin Compliance
    logAccessibility(themeName);
    
    speakText(`Theme changed to ${themeName}`);
}

// 2. Large Text Toggle with Logging
function toggleLargeText(enable) {
    if (enable) {
        document.body.classList.add('text-large');
        localStorage.setItem('accessform_large_text', 'true');
        speakText("Larger text enabled");
        logAccessibility('large-text');
    } else {
        document.body.classList.remove('text-large');
        localStorage.setItem('accessform_large_text', 'false');
        speakText("Larger text disabled");
    }
}

// 3. Screen Reader Toggle with Logging
function toggleScreenReader(enable) {
    screenReaderActive = enable;
    localStorage.setItem('accessform_screen_reader', enable ? 'true' : 'false');
    
    if (enable) {
        speakText("Screen reader activated. Hover over any text to hear it.");
        initScreenReaderHoverListeners();
        logAccessibility('voice-reader');
    } else {
        speakText("Screen reader deactivated.");
        window.speechSynthesis.cancel();
    }
}

// ==========================================================================
// SPEECH SYNTHESIS & HOVER LISTENERS (Existing Code - Improved)
// ==========================================================================

function speakText(text) {
    if ('speechSynthesis' in window && text.trim() !== "") {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 1.0;
        utterance.pitch = 1.0;
        window.speechSynthesis.speak(utterance);
    }
}

function initScreenReaderHoverListeners() {
    const targets = 'h1, h2, h3, h4, p, label, .btn, .nav-link, button, [role="button"]';
    
    document.querySelectorAll(targets).forEach(el => {
        el.removeEventListener('mouseenter', handleElementHoverSpeech);
        el.addEventListener('mouseenter', handleElementHoverSpeech);
    });
}

function handleElementHoverSpeech(event) {
    if (!screenReaderActive) return;
    
    let textToSpeak = event.target.innerText || 
                     event.target.getAttribute('placeholder') || 
                     event.target.getAttribute('aria-label') || 
                     "Element";

    speakText(textToSpeak.trim());
}

// ==================== INITIALIZATION ====================
document.addEventListener("DOMContentLoaded", function() {
    const cachedTheme = localStorage.getItem('accessform_theme') || 'default';
    const cachedLargeText = localStorage.getItem('accessform_large_text');
    const cachedScreenReader = localStorage.getItem('accessform_screen_reader');

    setTheme(cachedTheme);

    if (cachedLargeText === 'true') {
        toggleLargeText(true);
        const textToggle = document.getElementById('textToggle');
        if (textToggle) textToggle.checked = true;
    }

    if (cachedScreenReader === 'true') {
        screenReaderActive = true;
        const readerToggle = document.getElementById('screenReaderToggle');
        if (readerToggle) readerToggle.checked = true;
        initScreenReaderHoverListeners();
    }
});

// UI Sync Helper
function syncAccessibilityMenuUI(activeTheme) {
    document.querySelectorAll('.btn-theme-option').forEach(btn => {
        btn.classList.remove('active');
        if (btn.id === 'btn-' + activeTheme) {
            btn.classList.add('active');
        }
    });
}