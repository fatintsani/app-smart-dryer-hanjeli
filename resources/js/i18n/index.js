import { ref, computed } from 'vue';
import id from './locales/id.js';
import en from './locales/en.js';

const messages = {
  id,
  en,
};

const savedLang = (typeof localStorage !== 'undefined' ? localStorage.getItem('lang') : null) || 'id';
export const currentLang = ref(savedLang);

export function setLanguage(lang) {
  if (messages[lang]) {
    currentLang.value = lang;
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('lang', lang);
    }
    if (typeof document !== 'undefined') {
      document.documentElement.setAttribute('lang', lang);
    }
  }
}

export function t(path, paramsOrFallback = {}, fallback = '') {
  const lang = currentLang.value;
  const dict = messages[lang] || messages.id;

  let params = {};
  let defaultFallback = '';

  if (typeof paramsOrFallback === 'string') {
    defaultFallback = paramsOrFallback;
  } else if (paramsOrFallback && typeof paramsOrFallback === 'object') {
    params = paramsOrFallback;
    defaultFallback = fallback;
  }

  if (!path) return defaultFallback;

  const keys = path.split('.');
  let result = dict;
  let found = true;

  for (const key of keys) {
    if (result && typeof result === 'object' && key in result) {
      result = result[key];
    } else {
      found = false;
      break;
    }
  }

  if (!found || typeof result !== 'string') {
    // Try fallback to ID dictionary if not found in current lang
    let fallbackResult = messages.id;
    let fallbackFound = true;
    for (const fKey of keys) {
      if (fallbackResult && typeof fallbackResult === 'object' && fKey in fallbackResult) {
        fallbackResult = fallbackResult[fKey];
      } else {
        fallbackFound = false;
        break;
      }
    }
    result = (fallbackFound && typeof fallbackResult === 'string') ? fallbackResult : (defaultFallback || path);
  }

  if (typeof result === 'string') {
    // Interpolate {key} or { key } placeholders with values from params object
    result = result.replace(/\{(\s*[\w.-]+\s*)\}/g, (match, p1) => {
      const trimmed = p1.trim();
      return (params && trimmed in params) ? params[trimmed] : match;
    });
  }

  return result || defaultFallback || path;
}

export function useI18n() {
  return {
    currentLang,
    setLanguage,
    t,
    isIndonesian: computed(() => currentLang.value === 'id'),
    isEnglish: computed(() => currentLang.value === 'en'),
  };
}

export default {
  currentLang,
  setLanguage,
  t,
  useI18n,
};
