<template>
  <footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
      <div class="row">
        <!-- Brand & Description -->
        <div class="col-lg-4">
          <router-link to="/" class="d-flex align-items-center text-decoration-none">
            <div class="logo-icon me-2">
              <i class="bi bi-music-note text-white fs-4"></i>
            </div>
            <span class="brand-text fs-3 text-white">The Resonanz Music Studio</span>
          </router-link>
        </div>
      </div>

      <hr class="border-secondary my-4" />

      <!-- Bottom Footer -->
      <div class="row">
        <div class="d-flex justify-content-center align-items-center">
          <p class="mb-0 text-muted small" style="color: whitesmoke !important;">
            &copy; {{ new Date().getFullYear() }} The Resonanz. All rights
            reserved.
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
defineOptions({
  name: "FooterView",
});
</script>

<style scoped>
.hover-text-white:hover {
  color: white !important;
}

.hover-opacity-100:hover {
  opacity: 1 !important;
}
</style>
