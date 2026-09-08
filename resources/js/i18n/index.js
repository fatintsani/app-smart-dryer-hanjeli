import { ref, computed } from 'vue';
import id from './locales/id';
import en from './locales/en';

const messages = {
  id,
  en,
};

const savedLang = localStorage.getItem('lang') || 'id';
export const currentLang = ref(savedLang);

export function setLanguage(lang) {
  if (messages[lang]) {
    currentLang.value = lang;
    localStorage.setItem('lang', lang);
    document.documentElement.setAttribute('lang', lang);
  }
}

export function t(path, fallback = '') {
  const lang = currentLang.value;
  const dict = messages[lang] || messages.id;

  if (!path) return fallback;

  const keys = path.split('.');
  let result = dict;

  for (const key of keys) {
    if (result && typeof result === 'object' && key in result) {
      result = result[key];
    } else {
      // Try fallback to ID dictionary if not found in current lang
      let fallbackResult = messages.id;
      for (const fKey of keys) {
        if (fallbackResult && typeof fallbackResult === 'object' && fKey in fallbackResult) {
          fallbackResult = fallbackResult[fKey];
        } else {
          fallbackResult = null;
          break;
        }
      }
      return fallbackResult || fallback || path;
    }
  }

  return result || fallback || path;
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
