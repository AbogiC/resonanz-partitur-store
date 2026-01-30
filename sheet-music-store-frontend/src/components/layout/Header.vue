<template>
  <header
    class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top"
  >
    <div class="container">
      <!-- Logo -->
      <router-link to="/" class="navbar-brand d-flex align-items-center">
        <div>
          <img
            src="/logo_resonanz.png"
            alt="The Resonanz Logo"
            class="img-fluid"
            style="max-height: 50px"
          />
        </div>
      </router-link>

      <!-- Mobile Toggle -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarContent"
        aria-controls="navbarContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Content -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <!-- Left Navigation -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link
              to="/"
              class="nav-link"
              active-class="active"
              exact-active-class="active"
            >
              Home
            </router-link>
          </li>

          <!-- Sheet Music Dropdown -->
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="sheetMusicDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              Sheet Music
            </a>
            <ul class="dropdown-menu" aria-labelledby="sheetMusicDropdown">
              <li>
                <router-link
                  to="/products?type=sheet_music&category=piano"
                  class="dropdown-item"
                >
                  Piano
                </router-link>
              </li>
              <li>
                <router-link
                  to="/products?type=sheet_music&category=guitar"
                  class="dropdown-item"
                >
                  Guitar
                </router-link>
              </li>
              <li>
                <router-link
                  to="/products?type=sheet_music&category=violin"
                  class="dropdown-item"
                >
                  Violin
                </router-link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <router-link
                  to="/products?type=sheet_music"
                  class="dropdown-item"
                >
                  All Sheet Music
                </router-link>
              </li>
            </ul>
          </li>

          <!-- Merchandise Dropdown -->
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="merchandiseDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              Merchandise
            </a>
            <ul class="dropdown-menu" aria-labelledby="merchandiseDropdown">
              <li>
                <router-link
                  to="/products?type=merchandise&category=apparel"
                  class="dropdown-item"
                >
                  Apparel
                </router-link>
              </li>
              <li>
                <router-link
                  to="/products?type=merchandise&category=accessories"
                  class="dropdown-item"
                >
                  Accessories
                </router-link>
              </li>
              <li>
                <router-link
                  to="/products?type=merchandise&category=equipment"
                  class="dropdown-item"
                >
                  Equipment
                </router-link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <router-link
                  to="/products?type=merchandise"
                  class="dropdown-item"
                >
                  All Merchandise
                </router-link>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <router-link to="/products" class="nav-link" active-class="active">
              All Products
            </router-link>
          </li>
        </ul>

        <!-- Right Navigation -->
        <div class="d-flex align-items-center gap-3">
          <!-- Search -->
          <button
            class="btn btn-link text-dark p-0"
            @click="toggleSearch"
            type="button"
          >
            <span class="fs-4">🔍</span>
          </button>

          <!-- Cart -->
          <div class="position-relative">
            <button
              class="btn btn-link text-dark p-0 position-relative"
              @click="toggleCart"
              type="button"
            >
              <span class="fs-4">🛒</span>
              <span
                v-if="cartItemCount > 0"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size: 0.6rem"
              >
                {{ cartItemCount }}
              </span>
            </button>
          </div>

          <!-- User Menu -->
          <div v-if="isAuthenticated" class="dropdown">
            <button
              class="btn btn-link text-dark p-0 dropdown-toggle d-flex align-items-center"
              type="button"
              id="userDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                <span class="text-primary">👤</span>
              </div>
              <span class="d-none d-lg-inline">{{ user?.first_name }}</span>
            </button>
            <ul
              class="dropdown-menu dropdown-menu-end"
              aria-labelledby="userDropdown"
            >
              <li>
                <router-link to="/profile" class="dropdown-item">
                  <i class="bi bi-person-gear me-2"></i>
                  Profile
                </router-link>
              </li>
              <li>
                <router-link to="/orders" class="dropdown-item">
                  <i class="bi bi-box-seam me-2"></i>
                  Orders
                </router-link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button @click="logout" class="dropdown-item text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i>
                  Logout
                </button>
              </li>
            </ul>
          </div>

          <!-- Login/Register -->
          <div v-else class="d-flex gap-2">
            <router-link to="/login" class="btn btn-outline-primary">
              Login
            </router-link>
            <router-link to="/register" class="btn btn-primary">
              Sign Up
            </router-link>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div v-if="showSearch" class="mt-3 animate__animated animate__fadeInDown">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0">
            <i class="bi bi-search"></i>
          </span>
          <input
            v-model="searchQuery"
            @keyup.enter="performSearch"
            type="text"
            class="form-control border-start-0"
            placeholder="Search for sheet music, instruments, merchandise..."
          />
          <button
            @click="clearSearch"
            class="btn btn-outline-secondary"
            type="button"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "@/stores/cart";
import { useAuthStore } from "@/stores/auth";

defineOptions({
  name: "HeaderContent",
});

const router = useRouter();
const cartStore = useCartStore();
const authStore = useAuthStore();

const showSearch = ref(false);
const searchQuery = ref("");

const cartItemCount = computed(() => cartStore.totalQuantity);
const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

const toggleSearch = () => {
  showSearch.value = !showSearch.value;
};

const toggleCart = () => {
  cartStore.toggleCart();
};

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(
      `/products?search=${encodeURIComponent(searchQuery.value.trim())}`,
    );
    showSearch.value = false;
    searchQuery.value = "";
  }
};

const clearSearch = () => {
  searchQuery.value = "";
  showSearch.value = false;
};

const logout = () => {
  authStore.logout();
  router.push("/");
};
</script>
