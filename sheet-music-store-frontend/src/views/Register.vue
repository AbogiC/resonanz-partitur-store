<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body p-5">
            <h2 class="text-center mb-4">Sign Up</h2>
            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input
                  v-model="form.first_name"
                  type="text"
                  class="form-control"
                  id="firstName"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input
                  v-model="form.last_name"
                  type="text"
                  class="form-control"
                  id="lastName"
                  required
                />
              </div>
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
                {{ loading ? "Signing up..." : "Sign Up" }}
              </button>
            </form>
            <div class="text-center mt-3">
              <router-link to="/login"
                >Already have an account? Login</router-link
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
import axios from "axios";

const router = useRouter();

const form = ref({
  first_name: "",
  last_name: "",
  email: "",
  password: "",
});
const loading = ref(false);

const register = async () => {
  loading.value = true;
  try {
    await axios.post("/api/register", form.value);
    alert("Registration successful! Please login.");
    router.push("/login");
  } catch (error) {
    console.error("Register error:", error);
    alert(error.response?.data?.message || "Registration failed");
  } finally {
    loading.value = false;
  }
};
</script>
