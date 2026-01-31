<template>
  <div v-if="isCartOpen" class="cart-sidebar position-fixed top-0 end-0 h-100 bg-white shadow-lg z-index-1050"
    :class="{ show: isCartOpen }" style="width: 400px; z-index: 1050">
    <div class="cart-header d-flex justify-content-between align-items-center p-3 border-bottom">
      <h5 class="mb-0">Shopping Cart</h5>
      <button @click="toggleCart" class="btn btn-link text-dark p-0" type="button">
        <i class="bi bi-x-lg fs-4"></i>
      </button>
    </div>

    <div class="cart-body flex-grow-1 overflow-auto" style="height: calc(100% - 250px)">
      <div v-if="cartItemCount === 0" class="text-center py-5">
        <i class="bi bi-cart fs-1 text-muted mb-3"></i>
        <p class="text-muted">Your cart is empty</p>
        <router-link to="/products" @click="toggleCart" class="btn btn-primary">
          Continue Shopping
        </router-link>
      </div>

      <div v-else>
        <CartItem v-for="item in cartItems" :key="item.id" :item="item"
          @update-quantity="(id, qty) => updateQuantity(id, qty)" @remove="(id) => removeFromCart(id)" />
      </div>
    </div>

    <div v-if="cartItemCount > 0" class="cart-footer p-3 border-top">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-bold">Total:</span>
        <span class="fw-bold fs-5">${{ cartTotal }}</span>
      </div>
      <button class="btn btn-primary w-100 mb-2" @click="showCheckoutModal">
        Proceed to Checkout
      </button>
      <button class="btn btn-outline-secondary w-100" @click="clearCart">
        Clear Cart
      </button>
    </div>
  </div>

  <!-- Overlay -->
  <div v-if="isCartOpen" class="cart-overlay position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
    style="z-index: 1040" @click="toggleCart"></div>

  <!-- Checkout Confirmation Modal -->
  <div v-if="isCheckoutModalOpen" class="modal fade show d-block" tabindex="-1" style="z-index: 1060">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Checkout</h5>
          <button type="button" class="btn-close" @click="closeCheckoutModal"></button>
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
              <div v-for="item in cartItems" :key="item.id" class="d-flex justify-content-between mb-2">
                <span>{{ item.title }} (x{{ item.quantity }})</span>
                <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
              <strong>Total:</strong>
              <strong class="text-primary">${{ cartTotal }}</strong>
            </div>

            <!-- Shipping Address -->
            <div class="mb-3">
              <h6 class="mb-2">
                <i class="bi bi-truck me-2"></i>Shipping Address
              </h6>
              <div v-if="hasShippingAddress" class="address-box p-3 bg-light rounded">
                <p class="mb-1">{{ formatAddress(userProfile.shipping_address) }}</p>
                <small class="text-muted">
                  <i class="bi bi-telephone me-1"></i>{{ userProfile.shipping_address.phone || 'No phone' }}
                </small>
              </div>
              <div v-else class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <small>No shipping address found. Please update your profile.</small>
              </div>
            </div>

            <!-- Billing Address -->
            <div class="mb-3">
              <h6 class="mb-2">
                <i class="bi bi-credit-card me-2"></i>Billing Address
              </h6>
              <div v-if="hasBillingAddress" class="address-box p-3 bg-light rounded">
                <p class="mb-1">{{ formatAddress(userProfile.billing_address) }}</p>
                <small class="text-muted">
                  <i class="bi bi-telephone me-1"></i>{{ userProfile.billing_address.phone || 'No phone' }}
                </small>
              </div>
              <div v-else class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <small>No billing address found. Please update your profile.</small>
              </div>
            </div>

            <!-- Update Profile Link -->
            <div v-if="!hasShippingAddress || !hasBillingAddress" class="text-center mb-3">
              <router-link to="/profile" class="btn btn-sm btn-outline-primary" @click="closeCheckoutModal">
                <i class="bi bi-person-gear me-2"></i>Update Profile
              </router-link>
            </div>

            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle me-2"></i>
              <small>Please verify your addresses before proceeding with the order.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeCheckoutModal">
            Cancel
          </button>
          <button type="button" class="btn btn-primary" @click="confirmCheckout"
            :disabled="loadingProfile || !hasShippingAddress || !hasBillingAddress">
            <i class="bi bi-check-circle me-2"></i>Confirm & Proceed
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Overlay -->
  <div v-if="isCheckoutModalOpen" class="modal-backdrop fade show" style="z-index: 1055"></div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useCartStore } from "@/stores/cart";
import { useAuthStore } from "@/stores/auth";
import CartItem from "./CartItem.vue";

import { processInputOrder, getUserProfile } from "@/services/api.js";

const cartStore = useCartStore();
const authStore = useAuthStore();

const cartItemDB = computed(() => cartStore.cartItemDB);
const cartItemCount = computed(() => cartItemDB.value.item_count);

const isCartOpen = computed(() => cartStore.isCartOpen);
const cartItems = computed(() => cartItemDB.value.items);
const cartTotal = computed(() => cartItemDB.value.total);

const isCheckoutModalOpen = ref(false);
const loadingProfile = ref(false);
const userProfile = ref({
  billing_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: ""
  },
  shipping_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: ""
  }
});

const hasShippingAddress = computed(() => {
  const addr = userProfile.value.shipping_address;
  return addr && (addr.street || addr.city || addr.state || addr.postal_code || addr.country);
});

const hasBillingAddress = computed(() => {
  const addr = userProfile.value.billing_address;
  return addr && (addr.street || addr.city || addr.state || addr.postal_code || addr.country);
});

const toggleCart = () => {
  cartStore.toggleCart();
};

const updateQuantity = (productId, quantity) => {
  cartStore.updateQuantity(productId, quantity);
};

const removeFromCart = (productId) => {
  cartStore.removeFromCart(productId);
};

const clearCart = () => {
  cartStore.clearCart();
};

const formatAddress = (address) => {
  if (!address) return "No address provided";

  const parts = [];
  if (address.street) parts.push(address.street);
  if (address.city) parts.push(address.city);
  if (address.state) parts.push(address.state);
  if (address.postal_code) parts.push(address.postal_code);
  if (address.country) parts.push(address.country);

  return parts.length > 0 ? parts.join(", ") : "No address provided";
};

const loadUserProfile = async () => {
  loadingProfile.value = true;
  try {
    const profile = await getUserProfile();
    userProfile.value = profile;
  } catch (error) {
    console.error("Error loading user profile:", error);
    // If API fails, try to get from auth store
    if (authStore.user) {
      userProfile.value = {
        billing_address: authStore.user.billing_address || userProfile.value.billing_address,
        shipping_address: authStore.user.shipping_address || userProfile.value.shipping_address
      };
    }
  } finally {
    loadingProfile.value = false;
  }
};

const showCheckoutModal = async () => {
  isCheckoutModalOpen.value = true;
  await loadUserProfile();
};

const closeCheckoutModal = () => {
  isCheckoutModalOpen.value = false;
};

const confirmCheckout = () => {
  // Close the modal
  closeCheckoutModal();

  // Process checkout with user's actual addresses
  const customerDetails = {
    "shipping_address": formatAddress(userProfile.value.shipping_address),
    "billing_address": formatAddress(userProfile.value.billing_address),
    "payment_method": "stripe",
  };
  processInputOrder(customerDetails);
};
</script>

<style scoped>
.cart-sidebar {
  transform: translateX(100%);
  transition: transform 0.3s ease-in-out;
}

.cart-sidebar.show {
  transform: translateX(0);
}

.cart-overlay {
  transition: opacity 0.3s ease-in-out;
}

.modal.show {
  display: block;
  animation: fadeIn 0.3s ease-in-out;
}

.modal-backdrop.show {
  opacity: 0.5;
}

.address-box {
  border: 1px solid #dee2e6;
  font-size: 0.9rem;
}

.address-box p {
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

.modal-body h6 {
  color: #495057;
  font-weight: 600;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}
</style>
