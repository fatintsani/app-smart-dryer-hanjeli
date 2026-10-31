import { ref } from 'vue'

// Permanent Light Mode (Dark mode completely disabled)
export const isDark = ref(false)

/**
 * Toggle theme disabled (no-op)
 */
export function toggleTheme() {
  // Permanently disabled
}

/**
 * Force light theme
 */
export function setTheme() {
  isDark.value = false
  try {
    localStorage.removeItem('theme')
    if (typeof document !== 'undefined') {
      document.documentElement.classList.remove('dark-theme')
      if (document.body) {
        document.body.classList.remove('dark-theme')
      }
    }
  } catch (e) {
    // Ignore storage errors
  }
}

// Clean up any stale dark-theme classes from DOM and localStorage
if (typeof document !== 'undefined') {
  try {
    localStorage.removeItem('theme')
    document.documentElement.classList.remove('dark-theme')
    if (document.body) {
      document.body.classList.remove('dark-theme')
    }
  } catch (e) {
    // Ignore storage errors
  }
}

export default {
  isDark,
  toggleTheme,
  setTheme,
}
