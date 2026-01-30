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
      <button class="btn btn-primary w-100 mb-2" @click="checkout">
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
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "@/stores/cart";
import CartItem from "./CartItem.vue";

const router = useRouter();
const cartStore = useCartStore();

const cartItemDB = computed(() => cartStore.cartItemDB);
const cartItemCount = computed(() => cartItemDB.value.item_count);

console.log("Cart Sidebar Rendered " + JSON.stringify(cartItemDB.value));

const isCartOpen = computed(() => cartStore.isCartOpen);
const cartItems = computed(() => cartItemDB.value.items);
const cartTotal = computed(() => cartItemDB.value.total);

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

const checkout = () => {
  // TODO: Implement checkout logic
  toggleCart();
  router.push("/checkout");
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
</style>
