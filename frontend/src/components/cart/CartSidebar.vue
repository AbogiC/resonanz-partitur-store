<template>
  <div
    v-if="isCartOpen"
    class="cart-sidebar position-fixed top-0 end-0 h-100 bg-white shadow-lg z-index-1050"
    :class="{ show: isCartOpen }"
    style="width: 400px; z-index: 1050"
  >
    <div
      class="cart-header d-flex justify-content-between align-items-center p-3 border-bottom"
    >
      <h5 class="mb-0">Shopping Cart</h5>
      <button
        @click="toggleCart"
        class="btn btn-link text-dark p-0"
        type="button"
      >
        <i class="bi bi-x-lg fs-4"></i>
      </button>
    </div>

    <div
      class="cart-body flex-grow-1 overflow-auto"
      style="height: calc(100% - 250px)"
    >
      <div v-if="cartItemCount === 0" class="text-center py-5">
        <i class="bi bi-cart fs-1 text-muted mb-3"></i>
        <p class="text-muted">Your cart is empty</p>
        <router-link to="/products" @click="toggleCart" class="btn btn-primary">
          Continue Shopping
        </router-link>
      </div>

      <div v-else>
        <CartItem
          v-for="item in cartItems"
          :key="item.id"
          :item="item"
          @update-quantity="(id, qty) => updateQuantity(id, qty)"
          @remove="(id) => removeFromCart(id)"
        />
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
  <div
    v-if="isCartOpen"
    class="cart-overlay position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
    style="z-index: 1040"
    @click="toggleCart"
  ></div>

  <CheckoutModal
    :is-open="isCheckoutModalOpen"
    :loading-profile="loadingProfile"
    :cart-items="cartItems"
    :cart-total="cartTotal"
    :user-profile="userProfile"
    :has-shipping-address="hasShippingAddress"
    :has-billing-address="hasBillingAddress"
    :format-address="formatAddress"
    @close="closeCheckoutModal"
    @confirm="confirmCheckout"
  />
</template>

<script setup>
import { computed, ref } from "vue";
import { useCartStore } from "@/stores/cart";
import { useAuthStore } from "@/stores/auth";
import CartItem from "./CartItem.vue";
import CheckoutModal from "@/components/shared/CheckoutModal.vue";

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

const hasShippingAddress = computed(() => {
  const addr = userProfile.value.shipping_address;
  return (
    addr &&
    (addr.street || addr.city || addr.state || addr.postal_code || addr.country)
  );
});

const hasBillingAddress = computed(() => {
  const addr = userProfile.value.billing_address;
  return (
    addr &&
    (addr.street || addr.city || addr.state || addr.postal_code || addr.country)
  );
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
        billing_address:
          authStore.user.billing_address || userProfile.value.billing_address,
        shipping_address:
          authStore.user.shipping_address || userProfile.value.shipping_address,
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
    shipping_address: formatAddress(userProfile.value.shipping_address),
    billing_address: formatAddress(userProfile.value.billing_address),
    payment_method: "stripe",
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
