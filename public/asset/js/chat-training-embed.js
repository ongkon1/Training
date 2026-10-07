(function () {
    'use strict';

    var container = document.getElementById('chat-training-widget');
    if (!container) return;

    var loading = document.getElementById('chat-training-loading');
    var error = document.getElementById('chat-training-error');
    var vendorScript = document.getElementById('chat-training-script');
    var hostId = 'saiw-widget-host-' + container.dataset.chatbotId;
    var mounted = false;

    function showError() {
        if (mounted) return;
        loading.hidden = true;
        error.hidden = false;
    }

    function mount() {
        if (mounted) return;
        var host = document.getElementById(hostId);
        var shadow = host && host.shadowRoot;
        var root = shadow && shadow.querySelector('.saiw-root');
        var launcher = shadow && shadow.querySelector('.saiw-launcher');
        if (!root || !launcher) return;

        // Keep the vendor's event handlers, session, and messaging intact.
        container.appendChild(host);
        Object.assign(host.style, {
            position: 'relative', right: 'auto', bottom: 'auto',
            width: '100%', height: 'auto', zIndex: 'auto', pointerEvents: 'auto'
        });
        var style = document.createElement('style');
        style.textContent = `
            .saiw-root { position:relative!important; inset:auto!important; width:100%!important; height:680px!important; height:clamp(560px,75vh,760px)!important; z-index:auto!important; }
            .saiw-panel { display:flex!important; border-radius:0!important; box-shadow:none!important; }
            .saiw-close, .saiw-launcher { display:none!important; }
            .saiw-prechat { background:#f5f8f6; }
            .saiw-prechat-form { max-width:440px; border-radius:16px; }
            .saiw-input:focus-visible, button:focus-visible { outline:2px solid var(--saiw-theme); outline-offset:3px; }
            @media(max-width:575px) { .saiw-root { height:560px!important; } }
            @media(prefers-reduced-motion:reduce) { .saiw-launcher:before,.saiw-launcher:after,.saiw-typing span { animation:none!important; } }
        `;
        shadow.appendChild(style);
        loading.hidden = true;
        error.hidden = true;
        mounted = true;
        observer.disconnect();
        window.clearTimeout(timeout);
        if (!root.classList.contains('saiw-open')) launcher.click();
    }

    var observer = new MutationObserver(mount);
    observer.observe(document.body, { childList: true, subtree: true });
    var timeout = window.setTimeout(showError, 25000);
    if (vendorScript) vendorScript.addEventListener('error', showError);
    mount();
})();
