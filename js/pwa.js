/**
 * EPAMNHS PWA – Service Worker Registration & Install Prompt
 */

(function () {
  'use strict';

  // ── 1. Register Service Worker ──────────────────────────────────────────
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker
        .register('sw.js', { scope: './' })
        .then(reg => {
          console.log('[PWA] Service worker registered. Scope:', reg.scope);

          // Notify user when a new version is available
          reg.addEventListener('updatefound', () => {
            const worker = reg.installing;
            worker.addEventListener('statechange', () => {
              if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                showUpdateBanner();
              }
            });
          });
        })
        .catch(err => console.error('[PWA] Registration failed:', err));
    });
  }

  // ── 2. Install Prompt (A2HS) ────────────────────────────────────────────
  let deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallButton();
  });

  window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed.');
    deferredPrompt = null;
    hideInstallButton();
    showToast('EPAMNHS has been added to your home screen!', 'success');
  });

  function showInstallButton() {
    const btn = document.getElementById('pwa-install-btn');
    if (btn) btn.classList.remove('d-none');
  }

  function hideInstallButton() {
    const btn = document.getElementById('pwa-install-btn');
    if (btn) btn.classList.add('d-none');
  }

  // Called from the install button's onclick handler
  window.triggerPWAInstall = function () {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(choice => {
      console.log('[PWA] User choice:', choice.outcome);
      deferredPrompt = null;
      hideInstallButton();
    });
  };

  // ── 3. Update Banner ────────────────────────────────────────────────────
  function showUpdateBanner() {
    const existing = document.getElementById('pwa-update-banner');
    if (existing) return;

    const banner = document.createElement('div');
    banner.id = 'pwa-update-banner';
    banner.innerHTML = `
      <div style="
        position:fixed; bottom:1rem; left:50%; transform:translateX(-50%);
        background:#0b2b5c; color:#fff; border-radius:12px;
        padding:.85rem 1.4rem; display:flex; align-items:center; gap:.9rem;
        box-shadow:0 6px 24px rgba(0,0,0,.25); z-index:9999;
        font-family:inherit; max-width:92vw;">
        <span style="font-size:.93rem;">A new version of EPAMNHS is available.</span>
        <button onclick="window.location.reload()" style="
          background:#f0c040; color:#0b2b5c; border:none; border-radius:8px;
          padding:.4rem .9rem; font-weight:700; cursor:pointer; white-space:nowrap;">
          Update
        </button>
        <button onclick="this.closest('#pwa-update-banner').remove()" style="
          background:transparent; color:#ccc; border:none; cursor:pointer;
          font-size:1.1rem; line-height:1;">✕</button>
      </div>`;
    document.body.appendChild(banner);
  }

  // ── 4. Online / Offline Status Toast ───────────────────────────────────
  window.addEventListener('online',  () => showToast('You\'re back online.', 'success'));
  window.addEventListener('offline', () => showToast('You\'re offline. Some features may be limited.', 'warning'));

  function showToast(message, type) {
    const colors = { success: '#198754', warning: '#ffc107', error: '#dc3545' };
    const textColors = { success: '#fff', warning: '#000', error: '#fff' };

    const toast = document.createElement('div');
    toast.style.cssText = `
      position:fixed; bottom:1.2rem; right:1.2rem;
      background:${colors[type] || '#333'}; color:${textColors[type] || '#fff'};
      padding:.75rem 1.2rem; border-radius:10px; font-size:.88rem;
      box-shadow:0 4px 16px rgba(0,0,0,.2); z-index:10000;
      max-width:280px; line-height:1.4; font-family:inherit;
      animation: fadeInUp .3s ease;`;
    toast.textContent = message;

    // CSS animation
    if (!document.getElementById('pwa-toast-style')) {
      const style = document.createElement('style');
      style.id = 'pwa-toast-style';
      style.textContent = `
        @keyframes fadeInUp {
          from { opacity:0; transform:translateY(12px); }
          to   { opacity:1; transform:translateY(0); }
        }`;
      document.head.appendChild(style);
    }

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
  }

  // ── 5. Network quality indicator (optional) ─────────────────────────────
  if ('connection' in navigator) {
    const conn = navigator.connection;
    conn.addEventListener('change', () => {
      if (conn.effectiveType === 'slow-2g' || conn.effectiveType === '2g') {
        showToast('Slow connection detected.', 'warning');
      }
    });
  }
})();
