import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

// Import Bootstrap
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// Import Toastify
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";

// Using inline Bootstrap Icons classes (no external dependencies)

// Import custom CSS
import "./assets/main.css";

// Configure axios
axios.defaults.baseURL = "http://localhost:8000";

// Add request interceptor to include auth token
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Vue3Toastify, {
  autoClose: 3000,
  position: "top-right",
  theme: "colored",
});

// Note: Using Bootstrap Icons instead of Iconify for offline compatibility

app.mount("#app");
