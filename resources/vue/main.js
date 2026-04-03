import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import '../css/app.css';
import { useAuthStore } from './stores/auth.js';

const app = createApp(App);
const pinia = createPinia();
app.use(pinia);
app.use(router);
useAuthStore();
app.mount('#app');
