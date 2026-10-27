import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
Alpine.plugin(focus);

window.Alpine = Alpine;

Alpine.start();

// Give every non-GET form a visible, consistent pending state.
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form.method.toLowerCase() === 'get') return;
    form.setAttribute('aria-busy', 'true');
    form.querySelectorAll('button[type="submit"], button:not([type])').forEach((button) => {
        button.disabled = true;
        button.dataset.originalLabel = button.textContent;
        button.textContent = 'Memproses...';
    });
});
window.addEventListener('pageshow', () => {
    document.querySelectorAll('[aria-busy="true"]').forEach((form) => {
        form.removeAttribute('aria-busy');
        form.querySelectorAll('[data-original-label]').forEach((button) => {
            button.disabled = false;
            button.textContent = button.dataset.originalLabel;
        });
    });
});
