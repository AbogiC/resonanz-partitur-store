<template>
  <!-- Checkout Confirmation Modal -->
  <div
    v-if="isOpen"
    class="modal fade show d-block"
    tabindex="-1"
    style="z-index: 1060"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Checkout</h5>
          <button type="button" class="btn-close" @click="close"></button>
        </div>

        <div class="modal-body">
          <!-- Loading State -->
          <div v-if="loadingProfile" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading your information...</p>
          </div>

          <!-- Content -->
          <div v-else>
            <h6 class="mb-3">Order Summary</h6>

            <div class="mb-3">
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="d-flex justify-content-between mb-2"
              >
                <span>{{ item.title }} (x{{ item.quantity }})</span>
                <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
              </div>
            </div>

            <hr />

            <div class="d-flex justify-content-between mb-3">
              <strong>Total:</strong>
              <strong class="text-primary">${{ cartTotal }}</strong>
            </div>

            <!-- Shipping Address -->
            <div class="mb-3">
              <h6 class="mb-2">
                <i class="bi bi-truck me-2"></i>Shipping Address
              </h6>

              <div v-if="hasShippingAddress" class="p-3 bg-light rounded">
                <p class="mb-1">
                  {{ formatAddress(userProfile.shipping_address) }}
                </p>
                <small class="text-muted">
                  <i class="bi bi-telephone me-1"></i>
                  {{ userProfile.shipping_address.phone || "No phone" }}
                </small>
              </div>

              <div v-else class="alert alert-warning mb-0">
                <small>No shipping address found.</small>
              </div>
            </div>

            <!-- Billing Address -->
            <div class="mb-3">
              <h6 class="mb-2">
                <i class="bi bi-credit-card me-2"></i>Billing Address
              </h6>

              <div v-if="hasBillingAddress" class="p-3 bg-light rounded">
                <p class="mb-1">
                  {{ formatAddress(userProfile.billing_address) }}
                </p>
                <small class="text-muted">
                  <i class="bi bi-telephone me-1"></i>
                  {{ userProfile.billing_address.phone || "No phone" }}
                </small>
              </div>

              <div v-else class="alert alert-warning mb-0">
                <small>No billing address found.</small>
              </div>
            </div>

            <div
              v-if="!hasShippingAddress || !hasBillingAddress"
              class="text-center mb-3"
            >
              <router-link
                to="/profile"
                class="btn btn-sm btn-outline-primary"
                @click="close"
              >
                Update Profile
              </router-link>
            </div>

            <div class="alert alert-info mb-0">
              <small>Please verify your addresses.</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="close">Cancel</button>

          <button
            class="btn btn-primary"
            @click="confirm"
            :disabled="
              loadingProfile || !hasShippingAddress || !hasBillingAddress
            "
          >
            Confirm & Proceed
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Backdrop -->
  <div
    v-if="isOpen"
    class="modal-backdrop fade show"
    style="z-index: 1055"
  ></div>
</template>

<script setup>
defineProps({
  isOpen: Boolean,
  loadingProfile: Boolean,
  cartItems: Array,
  cartTotal: String,
  userProfile: Object,
  hasShippingAddress: Boolean,
  hasBillingAddress: Boolean,
  formatAddress: Function,
});

const emit = defineEmits(["close", "confirm"]);

const close = () => emit("close");
const confirm = () => emit("confirm");
</script>
