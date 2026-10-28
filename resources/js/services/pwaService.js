import { ref, reactive } from 'vue';

const deferredPrompt = ref(null);
const isInstallable = ref(false);
const isInstalled = ref(false);
const isIOS = ref(false);
const isDismissed = ref(false);
const notificationPermission = ref(typeof Notification !== 'undefined' ? Notification.permission : 'default');
let swRegistration = null;
let initialized = false;
const updateAvailable = ref(false);

export const pwaState = reactive({
  isInstallable,
  isInstalled,
  isIOS,
  isDismissed,
  notificationPermission,
  updateAvailable,
});

export function initPwa() {
  if (typeof window === 'undefined' || initialized) return;
  initialized = true;

  // Check if running as installed standalone PWA
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  isInstalled.value = isStandalone;

  // Check if iOS
  const userAgent = window.navigator.userAgent.toLowerCase();
  isIOS.value = /iphone|ipad|ipod/.test(userAgent) && !isStandalone;

  // Check if user previously dismissed banner in this session
  if (sessionStorage.getItem('pwa_prompt_dismissed') === 'true') {
    isDismissed.value = true;
  }

  // Register Service Worker
  if ('serviceWorker' in navigator) {
    let previouslyControlled = Boolean(navigator.serviceWorker.controller);
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      // Never reload an active form automatically when a release takes control.
      if (previouslyControlled) updateAvailable.value = true;
      previouslyControlled = true;
    });
    const registerWorker = async () => {
      try {
        const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/', updateViaCache: 'none' });
        swRegistration = reg;
        let lastUpdateCheck = 0;
        const checkForUpdate = () => {
          if (document.visibilityState !== 'visible' || !navigator.onLine || Date.now() - lastUpdateCheck < 60_000) return;
          lastUpdateCheck = Date.now();
          reg.update().catch((err) => console.warn('[PWA] Update check failed:', err));
        };
        document.addEventListener('visibilitychange', checkForUpdate);
        window.addEventListener('online', checkForUpdate);
        window.addEventListener('focus', checkForUpdate);
        window.setInterval(checkForUpdate, 15 * 60_000);
        checkForUpdate();
      } catch (err) {
        console.warn('[PWA] Service Worker registration failed:', err);
      }
    };
    if (document.readyState === 'complete') registerWorker();
    else window.addEventListener('load', registerWorker, { once: true });
  }

  window.addEventListener('vite:preloadError', () => { updateAvailable.value = true; });

  // Intercept beforeinstallprompt event (Android / Chromium)
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt.value = e;
    isInstallable.value = true;
    console.log('[PWA] beforeinstallprompt captured, ready to install');
  });

  // Track appinstalled event
  window.addEventListener('appinstalled', () => {
    isInstalled.value = true;
    isInstallable.value = false;
    deferredPrompt.value = null;
    console.log('[PWA] App was installed successfully');
  });
}

export function reloadForUpdate() {
  window.location.reload();
}

export async function promptInstall() {
  if (!deferredPrompt.value) {
    if (isIOS.value) {
      alert('Untuk menginstall di iPhone/iPad: Ketuk tombol Share (Bagikan) di Safari, lalu pilih "Add to Home Screen" (Tambah ke Layar Utama).');
    }
    return false;
  }

  deferredPrompt.value.prompt();
  const choiceResult = await deferredPrompt.value.userChoice;
  deferredPrompt.value = null;
  isInstallable.value = false;

  return choiceResult.outcome === 'accepted';
}

export function dismissInstallPrompt() {
  isDismissed.value = true;
  sessionStorage.setItem('pwa_prompt_dismissed', 'true');
}

export async function requestNotificationPermission() {
  if (!('Notification' in window)) {
    alert('Peramban web ini tidak mendukung Web Push Notification.');
    return 'unsupported';
  }

  try {
    const permission = await Notification.requestPermission();
    notificationPermission.value = permission;
    return permission;
  } catch (err) {
    console.error('[PWA] Error requesting notification permission:', err);
    return 'denied';
  }
}

export async function sendTestPushNotification(title = 'Smart Dryer Hanjeli: Uji Notifikasi', body = 'Sistem Web Push Notification aktif & berjalan normal!', targetUrl = '/monitoring') {
  if (!('Notification' in window)) {
    alert('Browser tidak mendukung notifikasi.');
    return;
  }

  if (Notification.permission !== 'granted') {
    const perm = await requestNotificationPermission();
    if (perm !== 'granted') {
      alert('Izin notifikasi belum diaktifkan.');
      return;
    }
  }

  if ('serviceWorker' in navigator && swRegistration) {
    swRegistration.showNotification(title, {
      body,
      icon: '/pwa-192x192.png',
      badge: '/pwa-192x192.png',
      tag: 'smart-dryer-test',
      vibrate: [200, 100, 200],
      data: { url: targetUrl },
      actions: [
        { action: 'open', title: 'Buka Monitoring' },
        { action: 'close', title: 'Tutup' }
      ]
    });
  } else {
    new Notification(title, {
      body,
      icon: '/pwa-192x192.png',
      data: { url: targetUrl }
    });
  }
}
