import './bootstrap';
import { createApp } from 'vue';
import router from './router/index.js';

// Import root component
import App from './components/App.vue';

// Create and mount the Vue app
const app = createApp(App);

app.use(router);
app.mount('#app');
