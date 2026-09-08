import api, { setAuthToken } from './api';

// WebAuthn Buffer & Base64url Helpers
function bufferToBase64Url(buffer) {
  const bytes = new Uint8Array(buffer);
  let str = '';
  for (let i = 0; i < bytes.length; i++) {
    str += String.fromCharCode(bytes[i]);
  }
  return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function base64UrlToBuffer(base64url) {
  if (!base64url) return new Uint8Array(0).buffer;
  const padding = '='.repeat((4 - (base64url.length % 4)) % 4);
  const base64 = (base64url + padding).replace(/-/g, '+').replace(/_/g, '/');
  const raw = atob(base64);
  const buffer = new Uint8Array(raw.length);
  for (let i = 0; i < raw.length; i++) {
    buffer[i] = raw.charCodeAt(i);
  }
  return buffer.buffer;
}

export function formatAuthError(err) {
  if (!err) return 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
  
  const msg = (err.message || String(err)).toLowerCase();

  if (msg.includes('invalid email or password') || msg.includes('salah') || msg.includes('unauthorized') || msg.includes('401')) {
    return 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.';
  }
  if (msg.includes('already registered') || msg.includes('sudah terdaftar') || msg.includes('conflict') || msg.includes('409') || msg.includes('unique')) {
    return 'Alamat email ini sudah terdaftar. Silakan masuk menggunakan akun tersebut atau gunakan email lain.';
  }
  if (msg.includes('password must be') || msg.includes('minimal 6') || msg.includes('longer than or equal to 6')) {
    return 'Kata sandi harus terdiri dari minimal 6 karakter demi keamanan akun Anda.';
  }
  if (msg.includes('must be an email') || msg.includes('email must be') || msg.includes('format email')) {
    return 'Format alamat email tidak valid. Gunakan format seperti nama@email.com.';
  }
  if (msg.includes('not found') || msg.includes('tidak ditemukan') || msg.includes('404')) {
    return 'Akun pengguna atau passkey tidak ditemukan di database.';
  }
  if (msg.includes('expired') || msg.includes('kedaluwarsa') || msg.includes('token')) {
    return 'Kode OTP atau sesi reset kata sandi telah kedaluwarsa. Silakan minta kode baru.';
  }
  if (msg.includes('user cancelled') || msg.includes('notallowederror') || msg.includes('cancelled') || msg.includes('abort')) {
    return 'Verifikasi Passkey / Biometrik dibatalkan oleh pengguna.';
  }
  if (msg.includes('failed to fetch') || msg.includes('network') || msg.includes('econnrefused')) {
    return 'Tidak dapat terhubung ke server backend Laravel. Pastikan server aktif di http://localhost:8000.';
  }

  return err.message || 'Terjadi kendala saat memproses permintaan. Silakan coba sesaat lagi.';
}

export const authService = {
  async login(email, password) {
    const res = await api.post('/auth/login', { email, password });
    if (res.accessToken) {
      setAuthToken(res.accessToken);
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }
    return res;
  },

  async register(data) {
    const res = await api.post('/auth/register', data);
    if (res.accessToken) {
      setAuthToken(res.accessToken);
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }
    return res;
  },

  async googleAuth(googleData) {
    const res = await api.post('/auth/google', googleData);
    if (res.accessToken) {
      setAuthToken(res.accessToken);
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }
    return res;
  },

  isPasskeySupported() {
    return !!(window.PublicKeyCredential && navigator.credentials);
  },

  async passkeyLogin(email = '') {
    if (!this.isPasskeySupported()) {
      throw new Error('Perangkat atau browser ini belum mendukung WebAuthn / Passkey.');
    }

    // 1. Get challenge & options from server
    const options = await api.post('/auth/passkey/options', { email });
    const challenge = base64UrlToBuffer(options.challenge);

    const allowCredentials = (options.allowCredentials || []).map(c => ({
      id: base64UrlToBuffer(c.id),
      type: 'public-key',
      transports: c.transports || ['internal'],
    }));

    const publicKeyCredentialRequestOptions = {
      challenge,
      timeout: options.timeout || 60000,
      rpId: window.location.hostname || 'localhost',
      userVerification: 'preferred',
    };

    if (allowCredentials.length > 0) {
      publicKeyCredentialRequestOptions.allowCredentials = allowCredentials;
    }

    // 2. Prompt browser authenticator (Windows Hello, Touch ID, PIN, Face ID)
    const assertion = await navigator.credentials.get({
      publicKey: publicKeyCredentialRequestOptions,
    });

    if (!assertion) {
      throw new Error('Tidak ada kredensial passkey yang dipilih.');
    }

    const credentialId = bufferToBase64Url(assertion.rawId);
    const clientDataJSON = bufferToBase64Url(assertion.response.clientDataJSON);
    const authenticatorData = bufferToBase64Url(assertion.response.authenticatorData);
    const signature = bufferToBase64Url(assertion.response.signature);

    // 3. Verify on server
    const res = await api.post('/auth/passkey/verify', {
      credentialId,
      clientDataJSON,
      authenticatorData,
      signature,
      email: email || undefined,
    });

    if (res.accessToken) {
      setAuthToken(res.accessToken);
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }

    return res;
  },

  async passkeyRegister(email, name) {
    if (!this.isPasskeySupported()) {
      throw new Error('Perangkat atau browser ini belum mendukung WebAuthn / Passkey.');
    }

    const options = await api.post('/auth/passkey/options', { email });
    const challenge = base64UrlToBuffer(options.challenge);
    const userId = new TextEncoder().encode(email);

    const publicKeyCredentialCreationOptions = {
      challenge,
      rp: {
        name: 'Smart Dryer Hanjeli',
        id: window.location.hostname || 'localhost',
      },
      user: {
        id: userId,
        name: email,
        displayName: name || email.split('@')[0],
      },
      pubKeyCredParams: [
        { alg: -7, type: 'public-key' },  // ES256
        { alg: -257, type: 'public-key' }, // RS256
      ],
      authenticatorSelection: {
        authenticatorAttachment: 'platform',
        userVerification: 'preferred',
        residentKey: 'preferred',
      },
      timeout: 60000,
      attestation: 'none',
    };

    const credential = await navigator.credentials.create({
      publicKey: publicKeyCredentialCreationOptions,
    });

    if (!credential) {
      throw new Error('Pembuatan passkey dibatalkan.');
    }

    const credentialId = bufferToBase64Url(credential.rawId);
    const clientDataJSON = bufferToBase64Url(credential.response.clientDataJSON);
    const attestationObject = bufferToBase64Url(credential.response.attestationObject);

    const res = await api.post('/auth/passkey/register', {
      email,
      credentialId,
      publicKey: attestationObject,
      deviceName: navigator.userAgent.includes('Windows') ? 'Windows Hello / PC' : navigator.userAgent.includes('Mac') ? 'Mac Touch ID' : 'Mobile Biometrik',
    });

    if (res.accessToken) {
      setAuthToken(res.accessToken);
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }

    return res;
  },

  async getProfile() {
    return api.get('/auth/profile');
  },

  async updateProfile(data) {
    const res = await api.put('/auth/profile', data);
    if (res.user) {
      localStorage.setItem('smart_dryer_user', JSON.stringify(res.user));
    }
    return res;
  },

  async forgotPassword(email) {
    return api.post('/auth/forgot-password', { email });
  },

  async verifyOtp(email, otp) {
    return api.post('/auth/verify-otp', { email, otp });
  },

  async resetPassword({ token, email, otp, newPassword }) {
    return api.post('/auth/reset-password', { token, email, otp, newPassword });
  },

  async getAllUsers() {
    try {
      const res = await api.get('/auth/users');
      let list = null;
      if (res && res.users && Array.isArray(res.users)) {
        list = res.users;
      } else if (Array.isArray(res)) {
        list = res;
      } else if (res && res.data && Array.isArray(res.data)) {
        list = res.data;
      }
      if (list !== null) {
        return list;
      }
    } catch (err) {
      console.warn('API /auth/users request failed:', err);
    }

    const current = this.getCurrentUser();
    return current ? [current] : [];
  },

  async createUser(data) {
    return api.post('/auth/users', data);
  },

  async updateUser(userId, data) {
    return api.put(`/auth/users/${userId}`, data);
  },

  async updateUserRole(userId, role) {
    return api.put(`/auth/users/${userId}/role`, { role });
  },

  async deleteUser(userId) {
    return api.delete(`/auth/users/${userId}`);
  },

  async logout() {
    // 1. Immediately wipe local auth credentials synchronously
    setAuthToken(null);
    localStorage.removeItem('smart_dryer_user');
    localStorage.removeItem('smart_dryer_token');
    sessionStorage.clear();

    // 2. Notify backend in background (best effort)
    try {
      await api.post('/auth/logout');
    } catch {
      // ignore network logout errors
    }
  },

  getCurrentUser() {
    try {
      const saved = localStorage.getItem('smart_dryer_user') || localStorage.getItem('user');
      return saved ? JSON.parse(saved) : null;
    } catch {
      return null;
    }
  },

  isAuthenticated() {
    const user = this.getCurrentUser();
    const token = localStorage.getItem('smart_dryer_token') || localStorage.getItem('auth_token');
    return !!(user || token);
  },

  isAdmin() {
    const user = this.getCurrentUser();
    const role = user?.role || localStorage.getItem('user_role');
    return role === 'ADMIN';
  },

  isOperator() {
    const user = this.getCurrentUser();
    const role = user?.role || localStorage.getItem('user_role');
    return role === 'OPERATOR';
  },
};

export default authService;
