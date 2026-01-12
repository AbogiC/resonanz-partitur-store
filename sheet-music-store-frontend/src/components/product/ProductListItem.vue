<template>
  <div class="list-group-item">
    <div class="row align-items-center">
      <!-- Product Image -->
      <div class="col-auto">
        <router-link :to="`/products/${product.id}`">
          <img
            :src="product.image_url"
            :alt="product.name"
            class="img-fluid rounded"
            style="width: 120px; height: 120px; object-fit: cover"
          />
        </router-link>
      </div>

      <!-- Product Info -->
      <div class="col">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <router-link
              :to="`/products/${product.id}`"
              class="text-decoration-none"
            >
              <h5 class="mb-1">{{ product.name }}</h5>
            </router-link>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span
                class="badge"
                :class="
                  product.type === 'sheet_music' ? 'bg-primary' : 'bg-success'
                "
              >
                {{
                  product.type === "sheet_music" ? "Sheet Music" : "Merchandise"
                }}
              </span>
              <span v-if="product.category" class="text-muted">
                {{ product.category }}
              </span>
              <span v-if="product.composer" class="text-muted fst-italic">
                • {{ product.composer }}
              </span>
            </div>
            <p class="text-muted mb-2">
              {{ truncateDescription(product.description) }}
            </p>

            <!-- Additional Info -->
            <div class="d-flex gap-3 small text-muted">
              <div v-if="product.instrument" class="d-flex align-items-center">
                <Icon icon="mdi:music" class="me-1" />
                {{ product.instrument }}
              </div>
              <div
                v-if="product.difficulty_level"
                class="d-flex align-items-center"
              >
                <Icon icon="mdi:signal" class="me-1" />
                {{ product.difficulty_level }}
              </div>
              <div
                v-if="product.duration_minutes"
                class="d-flex align-items-center"
              >
                <Icon icon="mdi:clock-outline" class="me-1" />
                {{ product.duration_minutes }} min
              </div>
            </div>
          </div>

          <div class="text-end">
            <div class="h4 mb-2">${{ product.price.toFixed(2) }}</div>
            <button
              @click="addToCart"
              :disabled="!canAddToCart"
              class="btn btn-sm"
              :class="addToCartButtonClass"
            >
              <Icon
                :icon="isInCart ? 'mdi:check' : 'mdi:cart-plus'"
                class="me-1"
              />
              {{ buttonText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useCartStore } from "@/stores/cart";
import { Icon } from "@iconify/vue";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const cartStore = useCartStore();

// Computed properties
const isInCart = computed(() => {
  return cartStore.items.some((item) => item.product_id === props.product.id);
});

const canAddToCart = computed(() => {
  if (props.product.is_digital) return true;
  return props.product.stock_quantity > 0;
});

const addToCartButtonClass = computed(() => {
  if (!canAddToCart.value) {
    return "btn-secondary disabled";
  }
  if (isInCart.value) {
    return "btn-success";
  }
  return "btn-primary";
});

const buttonText = computed(() => {
  if (!canAddToCart.value) return "Out of Stock";
  if (isInCart.value) return "In Cart";
  return "Add to Cart";
});

// Methods
const addToCart = async () => {
  if (!canAddToCart.value) return;

  try {
    await cartStore.addItem(props.product);
  } catch (error) {
    console.error("Failed to add to cart:", error);
  }
};

const truncateDescription = (text) => {
  if (!text) return "";
  return text.length > 200 ? text.substring(0, 200) + "..." : text;
};
</script>
