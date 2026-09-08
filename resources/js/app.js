import { createApp } from 'vue';
import '../css/style.css';
import App from './App.vue';
import router from './router';
import { t, currentLang, setLanguage } from './i18n';

const app = createApp(App);

// Provide global $t translation helper & currentLang to all Vue templates
app.config.globalProperties.$t = t;
app.config.globalProperties.$currentLang = currentLang;
app.config.globalProperties.$setLanguage = setLanguage;

app.use(router);
app.mount('#app');
