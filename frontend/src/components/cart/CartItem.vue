<template>
  <div class="cart-item p-4">
    <div class="row">
      <!-- Product Image -->
      <div class="col-auto">
        <router-link :to="`/products/${item.id}`" class="text-decoration-none">
          <img
            :src="item.image_url"
            :alt="item.name"
            class="img-thumbnail"
            style="width: 80px; height: 80px; object-fit: cover"
          />
        </router-link>
      </div>

      <!-- Product Info -->
      <div class="col">
        <div class="d-flex justify-content-between align-items-start">
          <router-link
            :to="`/products/${item.id}`"
            class="text-decoration-none"
          >
            <h6 class="mb-1 text-dark fw-semibold">{{ item.name }}</h6>
          </router-link>
          <button
            @click="removeItem"
            class="btn btn-link btn-sm text-muted p-0"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2">
          <span
            class="badge"
            :class="item.type === 'sheet_music' ? 'bg-primary' : 'bg-success'"
          >
            {{ item.type === "sheet_music" ? "Sheet Music" : "Merchandise" }}
          </span>
          <span v-if="item.composer" class="text-muted fst-italic small">
            {{ item.composer }}
          </span>
        </div>

        <!-- Stock Warning -->
        <div
          v-if="!item.is_digital && item.stock_quantity < item.quantity"
          class="alert alert-warning py-1 mb-2"
          role="alert"
        >
          <i class="bi bi-exclamation-triangle me-1"></i>
          Only {{ item.stock_quantity }} left in stock
        </div>

        <!-- Digital Badge -->
        <div
          v-if="item.is_digital"
          class="badge bg-info text-white d-inline-flex align-items-center gap-1 mb-2"
        >
          <i class="bi bi-download"></i>
          Digital Download
        </div>
      </div>
    </div>

    <!-- Quantity & Price -->
    <div class="row align-items-center mt-3">
      <div class="col">
        <!-- Quantity Selector -->
        <div class="d-flex align-items-center">
          <button
            @click="decreaseQuantity"
            :disabled="item.quantity <= 1"
            class="btn btn-outline-secondary btn-sm"
            style="width: 32px; height: 32px"
          >
            <i class="bi bi-dash"></i>
          </button>
          <span class="mx-3">{{ item.quantity }}</span>
          <button
            @click="increaseQuantity"
            :disabled="!item.is_digital && item.quantity >= item.stock_quantity"
            class="btn btn-outline-secondary btn-sm"
            style="width: 32px; height: 32px"
          >
            <i class="bi bi-plus"></i>
          </button>
        </div>
      </div>
      <div class="col-auto text-end">
        <div class="h5 mb-0 fw-bold">${{ item.item_total }}</div>
        <div class="text-muted small">${{ item.price }} each</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";
import { useCartStore } from "@/stores/cart";

const cartStore = useCartStore();

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const removeItem = async () => {
  cartStore.removeFromCart(props.item.cart_id);
  await cartStore.getCartItemCount();
  await cartStore.getCartItems();
};

const decreaseQuantity = async () => {
  cartStore.updateQuantity(props.item.cart_id, false);
  await cartStore.getCartItemCount();
  await cartStore.getCartItems();
};

const increaseQuantity = async () => {
  cartStore.updateQuantity(props.item.cart_id, true);
  await cartStore.getCartItemCount();
  await cartStore.getCartItems();
};
</script>

<style scoped>
.cart-item:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.cart-item:not(:last-child) {
  border-bottom: 1px solid #dee2e6;
}
</style>
