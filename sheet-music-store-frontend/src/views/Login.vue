<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body p-5">
            <h2 class="text-center mb-4">Login</h2>
            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  id="email"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  id="password"
                  required
                />
              </div>
              <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="loading"
              >
                {{ loading ? "Logging in..." : "Login" }}
              </button>
            </form>
            <div class="text-center mt-3">
              <router-link to="/register"
                >Don't have an account? Sign up</router-link
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: "",
  password: "",
});
const loading = ref(false);

const login = async () => {
  loading.value = true;
  try {
    const response = await axios.post("/api/login", form.value);
    authStore.setToken(response.data.token);
    authStore.setUser(response.data.user);
    router.push("/");
  } catch (error) {
    console.error("Login error:", error);
    alert(error.response?.data?.message || "Login failed");
  } finally {
    loading.value = false;
  }
};
</script>
