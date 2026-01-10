import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";

// Import Bootstrap
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// Import Toastify
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";

// Using inline Bootstrap Icons classes (no external dependencies)

// Import custom CSS
import "./assets/main.css";

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
