<template>
  <div class="product-card h-100">
    <!-- Product Image -->
    <div class="position-relative overflow-hidden bg-light">
      <router-link :to="`/products/${product.id}`" class="text-decoration-none">
        <img
          :src="product.image_url || '/placeholder-image.jpg'"
          :alt="product.name"
          class="img-fluid w-100 product-image"
          @error="handleImageError"
        />
      </router-link>

      <!-- Product Type Badge -->
      <span
        class="position-absolute top-0 start-0 m-3 badge rounded-pill text-white fw-semibold"
        :class="typeBadgeClass"
      >
        {{ productTypeLabel }}
      </span>

      <!-- Stock Status -->
      <div
        v-if="!product.is_digital && product.stock_quantity <= 5"
        class="position-absolute top-0 end-0 m-3"
      >
        <span class="badge bg-warning text-dark">
          {{
            product.stock_quantity > 0
              ? `Only ${product.stock_quantity} left`
              : "Out of Stock"
          }}
        </span>
      </div>

      <!-- Digital Badge -->
      <div
        v-if="product.is_digital"
        class="position-absolute bottom-0 end-0 m-3"
      >
        <span class="badge bg-info text-white d-flex align-items-center gap-1">
          <i class="bi bi-download"></i>
          Digital
        </span>
      </div>
    </div>

    <!-- Product Info -->
    <div class="card-body d-flex flex-column">
      <!-- Category -->
      <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-uppercase text-muted fw-semibold">
          {{ product.category }}
        </small>
        <span v-if="product.instrument" class="badge bg-light text-dark border">
          {{ product.instrument }}
        </span>
      </div>

      <!-- Product Name -->
      <router-link :to="`/products/${product.id}`" class="text-decoration-none">
        <h5 class="card-title text-dark mb-2 fw-semibold product-title">
          {{ product.name }}
        </h5>
      </router-link>

      <!-- Composer -->
      <p v-if="product.composer" class="text-muted mb-2 fst-italic">
        By {{ product.composer }}
      </p>

      <!-- Description -->
      <p class="card-text text-muted mb-3 flex-grow-1 product-description">
        {{ truncateDescription(product.description) }}
      </p>

      <!-- Difficulty & Duration -->
      <div
        v-if="product.difficulty_level || product.duration_minutes"
        class="d-flex gap-3 mb-3 text-muted small"
      >
        <div v-if="product.difficulty_level" class="d-flex align-items-center">
          <i class="bi bi-graph-up me-1"></i>
          {{ product.difficulty_level }}
        </div>
        <div v-if="product.duration_minutes" class="d-flex align-items-center">
          <i class="bi bi-clock me-1"></i>
          {{ product.duration_minutes }} min
        </div>
      </div>

      <!-- Price & Actions -->
      <div
        class="d-flex justify-content-between align-items-center pt-3 border-top"
      >
        <div>
          <span class="fs-4 fw-bold text-dark">
            ${{ product.price.toFixed(2) }}
          </span>
        </div>

        <div class="d-flex gap-2">
          <!-- Quick View -->
          <button
            @click="emit('quickView', product)"
            class="btn btn-outline-secondary btn-sm d-none d-lg-flex align-items-center"
            title="Quick View"
          >
            <i class="bi bi-eye"></i>
          </button>

          <!-- Add to Cart Button -->
          <button
            @click="addToCart"
            :disabled="!canAddToCart"
            class="btn btn-sm d-flex align-items-center gap-1"
            :class="addToCartButtonClass"
          >
            <i :class="isInCart ? 'bi bi-check-lg' : 'bi bi-cart-plus'"></i>
            {{ buttonText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useCartStore } from "@/stores/cart";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["quickView"]);

const cartStore = useCartStore();

// Computed properties
const isInCart = computed(() => {
  return cartStore.cartItems.some((item) => item.id === props.product.id);
});

const canAddToCart = computed(() => {
  if (props.product.is_digital) return true;
  return props.product.stock_quantity > 0;
});

const productTypeLabel = computed(() => {
  return props.product.type === "sheet_music" ? "Sheet Music" : "Merchandise";
});

const typeBadgeClass = computed(() => {
  return props.product.type === "sheet_music" ? "bg-primary" : "bg-success";
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
    cartStore.addToCart(props.product);
  } catch (error) {
    console.error("Failed to add to cart:", error);
  }
};

const handleImageError = (event) => {
  event.target.src =
    "https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";
};

const truncateDescription = (text) => {
  if (!text) return "";
  return text.length > 100 ? text.substring(0, 100) + "..." : text;
};
</script>

<style scoped>
.product-image {
  height: 200px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 3rem;
}

.product-description {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 4.5rem;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
}
</style>
