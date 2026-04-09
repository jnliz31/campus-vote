import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router/index.js";
import { initializeWebSocket } from "./services/websocket.js";

// Import root component
import App from "./components/App.vue";

// Initialize Pinia store
const pinia = createPinia();

// Initialize WebSocket connection
initializeWebSocket();

// Create and mount the Vue app
const app = createApp(App);

app.use(pinia);
app.use(router);
app.mount("#app");
