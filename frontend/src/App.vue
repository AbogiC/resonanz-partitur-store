<template>
  <div class="app-container d-flex flex-column min-vh-100">
    <Header />

    <main class="flex-grow-1">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <Footer />

    <!-- Cart Sidebar -->
    <CartSidebar />
  </div>
</template>

<script setup>
import Header from "@/components/layout/Header.vue";
import Footer from "@/components/layout/Footer.vue";
import CartSidebar from "@/components/cart/CartSidebar.vue";

// Check authentication on app load
import { useAuthStore } from "@/stores/auth";
import { onMounted } from "vue";

const authStore = useAuthStore();

onMounted(() => {
  // Check if user is logged in from localStorage
  const token = localStorage.getItem("token");
  if (token) {
    authStore.setToken(token);
  }
});
</script>

<style scoped>
.app-container {
  background-color: #f8f9fa;
}
</style>
