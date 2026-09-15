import { request } from './api';

export const aiService = {
  /**
   * Send chat prompt to AI Copilot
   * @param {string} prompt 
   * @param {Array<{sender: string, text: string}>} history 
   * @param {string} lang
   */
  async sendMessage(prompt, history = [], lang = 'id') {
    return await request('/ai/chat', {
      method: 'POST',
      body: { prompt, history, lang },
      timeout: 35000,
    });
  },

  /**
   * Get dynamic context-based suggestions
   * @param {string} lang
   */
  async getSuggestions(lang = 'id') {
    return await request(`/ai/suggestions?lang=${encodeURIComponent(lang)}`, {
      method: 'GET',
      timeout: 15000,
    });
  },

  /**
   * Get real-time sensor & batch context snapshot
   */
  async getContextSnapshot() {
    return await request('/ai/context', {
      method: 'GET',
      timeout: 15000,
    });
  },
};
