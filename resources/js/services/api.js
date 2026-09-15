/**
 * Base API Service Layer for Smart Greenhouse Dryer Hanjeli
 * Connects Vue 3 frontend to Laravel 12 Backend API
 */

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api';

export class ApiError extends Error {
  constructor(message, status, data) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.data = data;
  }
}

export const getAuthToken = () => {
  return localStorage.getItem('smart_dryer_token') || null;
};

export const setAuthToken = (token) => {
  if (token) {
    localStorage.setItem('smart_dryer_token', token);
  } else {
    localStorage.removeItem('smart_dryer_token');
  }
};

export async function request(endpoint, options = {}) {
  const cleanEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
  const url = `${API_BASE_URL}${cleanEndpoint}`;
  
  const headers = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(options.headers || {}),
  };

  const token = getAuthToken();
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const controller = new AbortController();
  const timeoutMs = options.timeout || 6000;
  const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

  const config = {
    ...options,
    headers,
    signal: options.signal || controller.signal,
  };

  if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
    config.body = JSON.stringify(config.body);
  }

  try {
    const response = await fetch(url, config);
    clearTimeout(timeoutId);
    const isJson = response.headers.get('content-type')?.includes('application/json');
    const data = isJson ? await response.json() : await response.text();

    if (!response.ok) {
      let errorMessage = data?.message;
      if (!errorMessage && data?.errors) {
        // Laravel validation errors format { email: ["..."], password: ["..."] }
        const firstErrKey = Object.keys(data.errors)[0];
        if (firstErrKey && Array.isArray(data.errors[firstErrKey])) {
          errorMessage = data.errors[firstErrKey][0];
        }
      }
      if (!errorMessage) {
        errorMessage = response.statusText || 'Permintaan API gagal';
      }
      throw new ApiError(errorMessage, response.status, data);
    }

    return data;
  } catch (error) {
    if (error instanceof ApiError) {
      throw error;
    }
    // Network error or server offline
    throw new ApiError(
      error.message || 'Tidak dapat terhubung ke server backend Laravel. Pastikan server aktif di http://localhost:8000.',
      0,
      null,
    );
  }
}

export default {
  get: (endpoint, options = {}) => request(endpoint, { ...options, method: 'GET' }),
  post: (endpoint, body, options = {}) => request(endpoint, { ...options, method: 'POST', body }),
  put: (endpoint, body, options = {}) => request(endpoint, { ...options, method: 'PUT', body }),
  patch: (endpoint, body, options = {}) => request(endpoint, { ...options, method: 'PATCH', body }),
  delete: (endpoint, options = {}) => request(endpoint, { ...options, method: 'DELETE' }),
};
