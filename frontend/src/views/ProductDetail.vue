<template>
  <div class="product-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="container py-5">
      <div class="row">
        <div class="col-md-6">
          <div class="placeholder-glow">
            <div class="placeholder bg-secondary" style="height: 400px"></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="placeholder-glow mb-3">
            <span class="placeholder col-8"></span>
          </div>
          <div class="placeholder-glow mb-3">
            <span class="placeholder col-4"></span>
          </div>
          <div class="placeholder-glow">
            <span class="placeholder col-12"></span>
            <span class="placeholder col-10"></span>
            <span class="placeholder col-6"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Detail -->
    <div v-else-if="product" class="container py-4">
      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <router-link to="/" class="text-decoration-none">Home</router-link>
          </li>
          <li class="breadcrumb-item">
            <router-link to="/products" class="text-decoration-none"
              >Products</router-link
            >
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            {{ product.name }}
          </li>
        </ol>
      </nav>

      <div class="row">
        <!-- Product Image -->
        <div class="col-lg-6 mb-4">
          <div class="product-image-container">
            <img
              :src="product.image_url || '/placeholder-image.jpg'"
              :alt="product.name"
              class="img-fluid rounded shadow"
              @error="handleImageError"
            />
          </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
          <div class="product-info">
            <!-- Product Type Badge -->
            <span
              class="badge mb-3"
              :class="
                product.type === 'sheet_music' ? 'bg-primary' : 'bg-success'
              "
            >
              {{ productTypeLabel }}
            </span>

            <!-- Product Name -->
            <h1 class="display-5 fw-bold mb-3">{{ product.name }}</h1>

            <!-- Composer -->
            <p v-if="product.composer" class="text-muted mb-2 fst-italic">
              By {{ product.composer }}
            </p>

            <!-- Category & Instrument -->
            <div class="d-flex gap-3 mb-3">
              <small class="text-uppercase text-muted fw-semibold">
                {{ product.category }}
              </small>
              <span
                v-if="product.instrument"
                class="badge bg-light text-dark border"
              >
                {{ product.instrument }}
              </span>
            </div>

            <!-- Difficulty & Duration -->
            <div
              v-if="product.difficulty_level || product.duration_minutes"
              class="d-flex gap-3 mb-4 text-muted"
            >
              <div
                v-if="product.difficulty_level"
                class="d-flex align-items-center"
              >
                <i class="bi bi-graph-up me-1"></i>
                {{ product.difficulty_level }}
              </div>
              <div
                v-if="product.duration_minutes"
                class="d-flex align-items-center"
              >
                <i class="bi bi-clock me-1"></i>
                {{ product.duration_minutes }} min
              </div>
            </div>

            <!-- Price -->
            <div class="mb-4">
              <span class="display-6 fw-bold text-dark">
                ${{ product.price.toFixed(2) }}
              </span>
            </div>

            <!-- Stock Status -->
            <div class="mb-4">
              <div
                v-if="product.is_digital"
                class="d-flex align-items-center text-success"
              >
                <i class="bi bi-download me-2"></i>
                <span class="fw-semibold">Digital Download Available</span>
              </div>
              <div
                v-else-if="product.stock_quantity > 0"
                class="d-flex align-items-center text-success"
              >
                <i class="bi bi-check-circle me-2"></i>
                <span>In Stock ({{ product.stock_quantity }} available)</span>
              </div>
              <div v-else class="d-flex align-items-center text-danger">
                <i class="bi bi-x-circle me-2"></i>
                <span>Out of Stock</span>
              </div>
            </div>

            <!-- Add to Cart Button -->
            <div class="d-grid gap-3 mb-4">
              <button
                @click="addToCart"
                :disabled="!canAddToCart"
                class="btn btn-lg d-flex align-items-center justify-content-center gap-2"
                :class="addToCartButtonClass"
              >
                <i :class="isInCart ? 'bi bi-check-lg' : 'bi bi-cart-plus'"></i>
                {{ buttonText }}
              </button>

              <!-- Wishlist Button -->
              <button
                class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2"
              >
                <i class="bi bi-heart"></i>
                Add to Wishlist
              </button>
            </div>

            <!-- Product Details -->
            <div class="border-top pt-4">
              <h3 class="h5 mb-3">Product Details</h3>
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Type:</span>
                    <span>{{ productTypeLabel }}</span>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Category:</span>
                    <span class="text-capitalize">{{ product.category }}</span>
                  </div>
                </div>
                <div v-if="product.instrument" class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Instrument:</span>
                    <span>{{ product.instrument }}</span>
                  </div>
                </div>
                <div v-if="product.difficulty_level" class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Difficulty:</span>
                    <span>{{ product.difficulty_level }}</span>
                  </div>
                </div>
                <div v-if="product.duration_minutes" class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Duration:</span>
                    <span>{{ product.duration_minutes }} minutes</span>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Format:</span>
                    <span>{{
                      product.is_digital ? "Digital" : "Physical"
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Description -->
      <div class="row mt-5">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h3 class="h4 mb-4">Description</h3>
              <p class="lead">{{ product.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts.length > 0" class="row mt-5">
        <div class="col-12">
          <h3 class="h4 mb-4">You Might Also Like</h3>
          <div class="row g-4">
            <div
              v-for="relatedProduct in relatedProducts"
              :key="relatedProduct.id"
              class="col-md-6 col-lg-3"
            >
              <ProductCard :product="relatedProduct" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else class="container py-5 text-center">
      <div class="mb-4">
        <i
          class="bi bi-exclamation-triangle text-warning"
          style="font-size: 4rem"
        ></i>
      </div>
      <h2 class="h3 mb-3">Product Not Found</h2>
      <p class="text-muted mb-4">
        The product you're looking for doesn't exist or has been removed.
      </p>
      <router-link to="/products" class="btn btn-primary">
        Browse All Products
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useProductStore } from "@/stores/product";
import { useCartStore } from "@/stores/cart";
import ProductCard from "@/components/product/ProductCard.vue";

const route = useRoute();
const productStore = useProductStore();
const cartStore = useCartStore();

const loading = ref(true);
const product = ref(null);
const relatedProducts = ref([]);

const isInCart = computed(() => {
  return product.value
    ? cartStore.cartItems.some((item) => item.id === product.value.id)
    : false;
});

const canAddToCart = computed(() => {
  if (!product.value) return false;
  if (product.value.is_digital) return true;
  return product.value.stock_quantity > 0;
});

const productTypeLabel = computed(() => {
  return product.value?.type === "sheet_music" ? "Sheet Music" : "Merchandise";
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
const fetchProduct = async () => {
  loading.value = true;
  try {
    const productId = route.params.id;
    product.value = await productStore.getProductById(productId);

    // Fetch related products (same category)
    if (product.value) {
      const related = await productStore.getProducts({
        type: product.value.type,
        category: product.value.category,
        limit: 4,
      });
      relatedProducts.value = related.records
        .filter((p) => p.id !== product.value.id)
        .slice(0, 4);
    }
  } catch (error) {
    console.error("Failed to fetch product:", error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

const addToCart = async () => {
  if (!canAddToCart.value || !product.value) return;

  try {
    cartStore.addToCart(product.value);
  } catch (error) {
    console.error("Failed to add to cart:", error);
  }
};

const handleImageError = (event) => {
  event.target.src =
    "https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";
};

onMounted(() => {
  fetchProduct();
});
</script>

<style scoped>
.product-detail-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.product-image-container {
  position: relative;
}

.product-image-container img {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: cover;
}

.product-info {
  position: sticky;
  top: 2rem;
}

@media (max-width: 991.98px) {
  .product-info {
    position: static;
  }
}
</style>
