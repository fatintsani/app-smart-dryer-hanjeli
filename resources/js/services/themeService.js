import { ref } from 'vue'

// Read stored theme preference (default to 'light' if not set)
const savedTheme = localStorage.getItem('theme') || 'light'
export const isDark = ref(savedTheme === 'dark')

/**
 * Toggle between light and dark themes globally
 */
export function toggleTheme() {
  setTheme(!isDark.value ? 'dark' : 'light')
}

/**
 * Set theme explicitly to 'dark' or 'light'
 * @param {'dark' | 'light'} theme 
 */
export function setTheme(theme) {
  isDark.value = theme === 'dark'
  try {
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    if (typeof document !== 'undefined') {
      if (isDark.value) {
        document.documentElement.classList.add('dark-theme')
      } else {
        document.documentElement.classList.remove('dark-theme')
      }
    }
  } catch (e) {
    console.warn('Storage theme sync warning:', e)
  }
}

// Initialize on load
if (typeof document !== 'undefined') {
  if (isDark.value) {
    document.documentElement.classList.add('dark-theme')
  } else {
    document.documentElement.classList.remove('dark-theme')
  }
}

export default {
  isDark,
  toggleTheme,
  setTheme,
}
