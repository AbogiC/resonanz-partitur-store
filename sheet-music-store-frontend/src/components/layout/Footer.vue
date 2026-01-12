<template>
  <footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
      <div class="row">
        <!-- Brand & Description -->
        <div class="col-lg-4 mb-4">
          <router-link
            to="/"
            class="d-flex align-items-center mb-3 text-decoration-none"
          >
            <div class="logo-icon me-2">
              <i class="bi bi-music-note text-white fs-4"></i>
            </div>
            <span class="brand-text fs-3 text-white">The Resonanz</span>
          </router-link>
          <p class="text-muted">
            Your premier destination for sheet music and musical merchandise.
            Quality products for musicians of all levels.
          </p>
          <div class="d-flex gap-3 mt-3">
            <a href="#" class="text-white opacity-75 hover-opacity-100">
              <i class="bi bi-facebook fs-4"></i>
            </a>
            <a href="#" class="text-white opacity-75 hover-opacity-100">
              <i class="bi bi-twitter fs-4"></i>
            </a>
            <a href="#" class="text-white opacity-75 hover-opacity-100">
              <i class="bi bi-instagram fs-4"></i>
            </a>
            <a href="#" class="text-white opacity-75 hover-opacity-100">
              <i class="bi bi-youtube fs-4"></i>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-4 mb-4">
          <h5 class="mb-3">Shop</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <router-link
                to="/products?type=sheet_music"
                class="text-muted text-decoration-none hover-text-white"
              >
                Sheet Music
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/products?type=merchandise"
                class="text-muted text-decoration-none hover-text-white"
              >
                Merchandise
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/products"
                class="text-muted text-decoration-none hover-text-white"
              >
                All Products
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/products?type=sheet_music&category=piano"
                class="text-muted text-decoration-none hover-text-white"
              >
                Piano Music
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/products?type=sheet_music&category=guitar"
                class="text-muted text-decoration-none hover-text-white"
              >
                Guitar Music
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div class="col-lg-2 col-md-4 mb-4">
          <h5 class="mb-3">Support</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <router-link
                to="/contact"
                class="text-muted text-decoration-none hover-text-white"
              >
                Contact Us
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/faq"
                class="text-muted text-decoration-none hover-text-white"
              >
                FAQ
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/shipping"
                class="text-muted text-decoration-none hover-text-white"
              >
                Shipping Info
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/returns"
                class="text-muted text-decoration-none hover-text-white"
              >
                Returns & Exchanges
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/privacy"
                class="text-muted text-decoration-none hover-text-white"
              >
                Privacy Policy
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Account -->
        <div class="col-lg-2 col-md-4 mb-4">
          <h5 class="mb-3">Account</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <router-link
                to="/login"
                class="text-muted text-decoration-none hover-text-white"
              >
                Login
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/register"
                class="text-muted text-decoration-none hover-text-white"
              >
                Register
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/profile"
                class="text-muted text-decoration-none hover-text-white"
              >
                My Profile
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/orders"
                class="text-muted text-decoration-none hover-text-white"
              >
                Order History
              </router-link>
            </li>
            <li class="mb-2">
              <router-link
                to="/wishlist"
                class="text-muted text-decoration-none hover-text-white"
              >
                Wishlist
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="col-lg-2 mb-4">
          <h5 class="mb-3">Newsletter</h5>
          <p class="text-muted small mb-3">
            Subscribe for updates, new releases, and exclusive offers.
          </p>
          <div class="input-group mb-3">
            <input
              type="email"
              class="form-control form-control-sm"
              placeholder="Your email"
            />
            <button class="btn btn-primary btn-sm" type="button">
              <i class="bi bi-send"></i>
            </button>
          </div>
        </div>
      </div>

      <hr class="border-secondary my-4" />

      <!-- Bottom Footer -->
      <div class="row">
        <div class="col-md-6">
          <p class="mb-0 text-muted small">
            &copy; {{ new Date().getFullYear() }} The Resonanz. All rights
            reserved.
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="mb-0 text-muted small">Secure payments powered by Stripe</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No JavaScript needed for basic footer
</script>

<style scoped>
.hover-text-white:hover {
  color: white !important;
}

.hover-opacity-100:hover {
  opacity: 1 !important;
}
</style>
