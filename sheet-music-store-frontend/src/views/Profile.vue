<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="mb-4">My Profile</h2>

            <!-- Success/Error Messages -->
            <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ successMessage }}
              <button type="button" class="btn-close" @click="successMessage = ''"></button>
            </div>
            <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ errorMessage }}
              <button type="button" class="btn-close" @click="errorMessage = ''"></button>
            </div>

            <form @submit.prevent="updateProfile">
              <!-- Personal Information Section -->
              <div class="mb-4">
                <h4 class="border-bottom pb-2 mb-3">Personal Information</h4>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input v-model="form.name" type="text" class="form-control" id="name" required
                      placeholder="Enter your full name" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input v-model="form.email" type="email" class="form-control" id="email" required
                      placeholder="Enter your email" />
                  </div>
                </div>
              </div>

              <!-- Billing Address Section -->
              <div class="mb-4">
                <h4 class="border-bottom pb-2 mb-3">Billing Address</h4>
                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="billing_street" class="form-label">Street Address</label>
                    <input v-model="form.billing_address.street" type="text" class="form-control" id="billing_street"
                      placeholder="Enter street address" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="billing_city" class="form-label">City</label>
                    <input v-model="form.billing_address.city" type="text" class="form-control" id="billing_city"
                      placeholder="Enter city" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="billing_state" class="form-label">State/Province</label>
                    <input v-model="form.billing_address.state" type="text" class="form-control" id="billing_state"
                      placeholder="Enter state or province" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="billing_postal" class="form-label">Postal Code</label>
                    <input v-model="form.billing_address.postal_code" type="text" class="form-control"
                      id="billing_postal" placeholder="Enter postal code" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="billing_country" class="form-label">Country</label>
                    <input v-model="form.billing_address.country" type="text" class="form-control" id="billing_country"
                      placeholder="Enter country" />
                  </div>
                  <div class="col-12 mb-3">
                    <label for="billing_phone" class="form-label">Phone Number</label>
                    <input v-model="form.billing_address.phone" type="tel" class="form-control" id="billing_phone"
                      placeholder="Enter phone number" />
                  </div>
                </div>
              </div>

              <!-- Shipping Address Section -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                  <h4 class="mb-0">Shipping Address</h4>
                  <div class="form-check">
                    <input v-model="sameAsBilling" type="checkbox" class="form-check-input" id="sameAsBilling"
                      @change="copyBillingToShipping" />
                    <label class="form-check-label" for="sameAsBilling">
                      Same as billing address
                    </label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="shipping_street" class="form-label">Street Address</label>
                    <input v-model="form.shipping_address.street" type="text" class="form-control" id="shipping_street"
                      placeholder="Enter street address" :disabled="sameAsBilling" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="shipping_city" class="form-label">City</label>
                    <input v-model="form.shipping_address.city" type="text" class="form-control" id="shipping_city"
                      placeholder="Enter city" :disabled="sameAsBilling" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="shipping_state" class="form-label">State/Province</label>
                    <input v-model="form.shipping_address.state" type="text" class="form-control" id="shipping_state"
                      placeholder="Enter state or province" :disabled="sameAsBilling" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="shipping_postal" class="form-label">Postal Code</label>
                    <input v-model="form.shipping_address.postal_code" type="text" class="form-control"
                      id="shipping_postal" placeholder="Enter postal code" :disabled="sameAsBilling" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="shipping_country" class="form-label">Country</label>
                    <input v-model="form.shipping_address.country" type="text" class="form-control"
                      id="shipping_country" placeholder="Enter country" :disabled="sameAsBilling" />
                  </div>
                  <div class="col-12 mb-3">
                    <label for="shipping_phone" class="form-label">Phone Number</label>
                    <input v-model="form.shipping_address.phone" type="tel" class="form-control" id="shipping_phone"
                      placeholder="Enter phone number" :disabled="sameAsBilling" />
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" @click="resetForm" :disabled="loading">
                  Reset
                </button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ loading ? "Updating..." : "Update Profile" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

defineOptions({
  name: "ProfilePage",
});

const authStore = useAuthStore();

const form = ref({
  name: "",
  email: "",
  billing_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: "",
  },
  shipping_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: "",
  },
});

const sameAsBilling = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

// Load user profile data on mount
onMounted(() => {
  loadUserProfile();
});

const loadUserProfile = async () => {
  try {
    // If user data exists in store, populate the form
    if (authStore.user) {
      form.value.name = authStore.user.name || "";
      form.value.email = authStore.user.email || "";

      // Load addresses if they exist
      if (authStore.user.billing_address) {
        form.value.billing_address = { ...authStore.user.billing_address };
      }
      if (authStore.user.shipping_address) {
        form.value.shipping_address = { ...authStore.user.shipping_address };
      }
    }

    // Optionally fetch fresh data from API
    // const response = await axios.get("/api/profile", {
    //   headers: {
    //     Authorization: `Bearer ${authStore.token}`,
    //   },
    // });
    // form.value = response.data;
  } catch (error) {
    console.error("Error loading profile:", error);
    errorMessage.value = "Failed to load profile data";
  }
};

const copyBillingToShipping = () => {
  if (sameAsBilling.value) {
    form.value.shipping_address = { ...form.value.billing_address };
  }
};

const updateProfile = async () => {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const response = await axios.put("/api/profile", form.value, {
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
    });

    // Update user data in store
    authStore.setUser(response.data.user || form.value);

    successMessage.value = "Profile updated successfully!";

    // Scroll to top to show success message
    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (error) {
    console.error("Error updating profile:", error);
    errorMessage.value = error.response?.data?.message || "Failed to update profile. Please try again.";

    // Scroll to top to show error message
    window.scrollTo({ top: 0, behavior: "smooth" });
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  loadUserProfile();
  sameAsBilling.value = false;
  successMessage.value = "";
  errorMessage.value = "";
};
</script>

<style scoped>
.form-label {
  font-weight: 500;
}

.border-bottom {
  border-color: #dee2e6 !important;
}

h4 {
  color: #495057;
  font-size: 1.25rem;
}

.btn-close {
  font-size: 0.875rem;
}

.form-control:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
}

.gap-2 {
  gap: 0.5rem;
}
</style>
