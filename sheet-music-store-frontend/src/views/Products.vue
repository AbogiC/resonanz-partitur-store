<template>
  <div class="products-page">
    <!-- Page Header -->
    <div class="bg-white border-bottom">
      <div class="container py-4">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h1 class="display-5 fw-bold mb-2">
              {{ pageTitle }}
            </h1>
            <p class="text-muted mb-0">
              {{ pageDescription }}
            </p>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-center justify-content-md-end gap-3">
              <!-- Sort -->
              <select v-model="sortBy" class="form-select w-auto">
                <option value="newest">Newest First</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="name">Name A-Z</option>
              </select>

              <!-- View Toggle -->
              <div class="btn-group" role="group">
                <input
                  type="radio"
                  class="btn-check"
                  name="viewMode"
                  id="gridView"
                  autocomplete="off"
                  :value="'grid'"
                  v-model="viewMode"
                />
                <label class="btn btn-outline-secondary" for="gridView">
                  <Icon icon="mdi:view-grid" />
                </label>

                <input
                  type="radio"
                  class="btn-check"
                  name="viewMode"
                  id="listView"
                  autocomplete="off"
                  :value="'list'"
                  v-model="viewMode"
                />
                <label class="btn btn-outline-secondary" for="listView">
                  <Icon icon="mdi:view-list" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mt-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <router-link to="/" class="text-decoration-none"
                >Home</router-link
              >
            </li>
            <li class="breadcrumb-item">
              <router-link to="/products" class="text-decoration-none"
                >Products</router-link
              >
            </li>
            <li
              v-if="activeFilter"
              class="breadcrumb-item active"
              aria-current="page"
            >
              {{ activeFilter }}
            </li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="container py-4">
      <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <!-- Filter Header -->
              <div
                class="d-flex justify-content-between align-items-center mb-4"
              >
                <h3 class="h5 mb-0">Filters</h3>
                <button
                  v-if="hasActiveFilters"
                  @click="clearFilters"
                  class="btn btn-link btn-sm text-decoration-none p-0"
                >
                  Clear All
                </button>
              </div>

              <!-- Product Type -->
              <div class="mb-4">
                <h4 class="h6 mb-3">Product Type</h4>
                <div class="form-check mb-2">
                  <input
                    class="form-check-input"
                    type="radio"
                    v-model="filters.type"
                    value=""
                    id="typeAll"
                  />
                  <label class="form-check-label" for="typeAll">
                    All Products
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input
                    class="form-check-input"
                    type="radio"
                    v-model="filters.type"
                    value="sheet_music"
                    id="typeSheetMusic"
                  />
                  <label
                    class="form-check-label d-flex justify-content-between"
                    for="typeSheetMusic"
                  >
                    <span>Sheet Music</span>
                    <span class="text-muted small">{{
                      productCounts.sheet_music
                    }}</span>
                  </label>
                </div>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    v-model="filters.type"
                    value="merchandise"
                    id="typeMerchandise"
                  />
                  <label
                    class="form-check-label d-flex justify-content-between"
                    for="typeMerchandise"
                  >
                    <span>Merchandise</span>
                    <span class="text-muted small">{{
                      productCounts.merchandise
                    }}</span>
                  </label>
                </div>
              </div>

              <!-- Categories -->
              <div v-if="categories.length > 0" class="mb-4">
                <h4 class="h6 mb-3">Category</h4>
                <div class="form-scrollable">
                  <div
                    v-for="category in categories"
                    :key="category"
                    class="form-check mb-2"
                  >
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :value="category"
                      v-model="filters.categories"
                      :id="`category-${category}`"
                    />
                    <label
                      class="form-check-label d-flex justify-content-between"
                      :for="`category-${category}`"
                    >
                      <span class="text-capitalize">{{ category }}</span>
                      <span class="text-muted small">
                        {{ categoryCounts[category] || 0 }}
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Price Range -->
              <div class="mb-4">
                <h4 class="h6 mb-3">Price Range</h4>
                <div class="row g-2 mb-3">
                  <div class="col">
                    <input
                      type="number"
                      v-model="filters.minPrice"
                      placeholder="Min"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-auto align-self-center">
                    <span class="text-muted">to</span>
                  </div>
                  <div class="col">
                    <input
                      type="number"
                      v-model="filters.maxPrice"
                      placeholder="Max"
                      class="form-control form-control-sm"
                    />
                  </div>
                </div>
                <button
                  @click="applyPriceRange"
                  class="btn btn-primary btn-sm w-100"
                >
                  Apply Price
                </button>
              </div>

              <!-- Digital Only -->
              <div class="mb-3">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="filters.digitalOnly"
                    id="digitalOnly"
                  />
                  <label class="form-check-label" for="digitalOnly">
                    Digital Only
                  </label>
                </div>
              </div>

              <!-- In Stock Only -->
              <div>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="filters.inStockOnly"
                    id="inStockOnly"
                  />
                  <label class="form-check-label" for="inStockOnly">
                    In Stock Only
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
          <!-- Search Results Info -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                  <span class="text-muted">
                    Showing
                    <span class="fw-semibold">{{
                      filteredProducts.length
                    }}</span>
                    of
                    <span class="fw-semibold">{{ totalProducts }}</span>
                    products
                  </span>
                </div>

                <!-- Active Filters -->
                <div v-if="hasActiveFilters" class="col-md-6">
                  <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <span
                      v-for="filter in activeFilterTags"
                      :key="filter.key"
                      class="badge bg-primary d-flex align-items-center gap-1"
                    >
                      {{ filter.label }}
                      <button
                        @click="removeFilter(filter.key)"
                        class="btn btn-link btn-sm p-0 text-white opacity-75 hover-opacity-100"
                      >
                        <Icon icon="mdi:close" class="fs-6" />
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Products Grid/List -->
          <div v-if="loading" class="row g-4">
            <div v-for="n in 8" :key="n" class="col-md-6 col-xl-6">
              <div class="product-card">
                <div class="placeholder-glow">
                  <div
                    class="placeholder placeholder-lg bg-secondary"
                    style="height: 200px"
                  ></div>
                  <div class="card-body">
                    <h5 class="card-title placeholder-glow">
                      <span class="placeholder col-8"></span>
                    </h5>
                    <p class="card-text placeholder-glow">
                      <span class="placeholder col-6"></span>
                      <span class="placeholder col-7"></span>
                      <span class="placeholder col-4"></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else-if="viewMode === 'grid' && filteredProducts.length > 0"
            class="row g-4"
          >
            <div
              v-for="product in paginatedProducts"
              :key="product.id"
              class="col-md-6 col-xl-6"
            >
              <ProductCard :product="product" />
            </div>
          </div>

          <div
            v-else-if="viewMode === 'list' && filteredProducts.length > 0"
            class="row"
          >
            <div class="col-12">
              <div class="list-group">
                <ProductListItem
                  v-for="product in paginatedProducts"
                  :key="product.id"
                  :product="product"
                />
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div
            v-else-if="!loading && filteredProducts.length === 0"
            class="text-center py-5"
          >
            <div class="mb-4 text-muted">
              <Icon icon="mdi:music-off" style="font-size: 4rem" />
            </div>
            <h3 class="h4 mb-2">No products found</h3>
            <p class="text-muted mb-4">
              Try adjusting your filters or search term
            </p>
            <button @click="clearFilters" class="btn btn-primary">
              Clear All Filters
            </button>
          </div>

          <!-- Pagination -->
          <nav v-if="totalPages > 1" aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button
                  class="page-link"
                  @click="handlePageChange(currentPage - 1)"
                  :disabled="currentPage === 1"
                >
                  Previous
                </button>
              </li>

              <li
                v-for="page in visiblePages"
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
              >
                <button class="page-link" @click="handlePageChange(page)">
                  {{ page }}
                </button>
              </li>

              <li
                class="page-item"
                :class="{ disabled: currentPage === totalPages }"
              >
                <button
                  class="page-link"
                  @click="handlePageChange(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                >
                  Next
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useProductStore } from "@/stores/product";
import ProductCard from "@/components/product/ProductCard.vue";
import ProductListItem from "@/components/product/ProductListItem.vue";
import { Icon } from "@iconify/vue";

defineOptions({
  name: "ProductsPage",
});

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();

// State
const loading = ref(false);
const viewMode = ref("grid");
const sortBy = ref("newest");
const currentPage = ref(1);
const itemsPerPage = 12;

// Filters
const filters = ref({
  type: "",
  categories: [],
  instruments: [],
  minPrice: null,
  maxPrice: null,
  digitalOnly: false,
  inStockOnly: false,
  search: "",
});

// Computed properties
const pageTitle = computed(() => {
  if (filters.value.search) return `Search: "${filters.value.search}"`;
  if (filters.value.type === "sheet_music") return "Sheet Music";
  if (filters.value.type === "merchandise") return "Merchandise";
  if (filters.value.categories.length === 1) {
    return (
      filters.value.categories[0].charAt(0).toUpperCase() +
      filters.value.categories[0].slice(1)
    );
  }
  return "All Products";
});

const pageDescription = computed(() => {
  if (filters.value.search)
    return `Search results for "${filters.value.search}"`;
  if (filters.value.type === "sheet_music")
    return "Browse our collection of sheet music for all instruments and skill levels";
  if (filters.value.type === "merchandise")
    return "Discover musical merchandise, apparel, and accessories";
  return "Browse our complete collection of sheet music and musical merchandise";
});

const activeFilter = computed(() => {
  if (filters.value.type && filters.value.categories.length === 1) {
    const category = filters.value.categories[0];
    return `${filters.value.type === "sheet_music" ? "Sheet Music" : "Merchandise"} / ${category}`;
  }
  if (filters.value.type) {
    return filters.value.type === "sheet_music" ? "Sheet Music" : "Merchandise";
  }
  return "";
});

const hasActiveFilters = computed(() => {
  return Object.values(filters.value).some((filter) => {
    if (Array.isArray(filter)) return filter.length > 0;
    if (typeof filter === "string") return filter !== "";
    if (typeof filter === "number") return filter !== null;
    if (typeof filter === "boolean") return filter;
    return false;
  });
});

const activeFilterTags = computed(() => {
  const tags = [];

  if (filters.value.type) {
    tags.push({
      key: "type",
      label:
        filters.value.type === "sheet_music" ? "Sheet Music" : "Merchandise",
    });
  }

  filters.value.categories.forEach((category) => {
    tags.push({
      key: `category-${category}`,
      label: `Category: ${category}`,
    });
  });

  if (filters.value.digitalOnly) {
    tags.push({ key: "digitalOnly", label: "Digital Only" });
  }

  if (filters.value.inStockOnly) {
    tags.push({ key: "inStockOnly", label: "In Stock Only" });
  }

  if (filters.value.minPrice || filters.value.maxPrice) {
    let priceLabel = "Price: ";
    if (filters.value.minPrice) priceLabel += `$${filters.value.minPrice}+`;
    if (filters.value.maxPrice)
      priceLabel += ` up to $${filters.value.maxPrice}`;
    tags.push({ key: "priceRange", label: priceLabel });
  }

  if (filters.value.search) {
    tags.push({ key: "search", label: `Search: "${filters.value.search}"` });
  }

  return tags;
});

// Product data
const allProducts = computed(() => productStore.products);
const totalProducts = computed(() => productStore.totalProducts);
const productCounts = computed(() => productStore.productCountsByType);
const categories = computed(() => productStore.categories);
const categoryCounts = computed(() => productStore.categoryCounts);

// Filtered products
const filteredProducts = computed(() => {
  let products = [...allProducts.value];

  // Apply type filter
  if (filters.value.type) {
    products = products.filter((p) => p.type === filters.value.type);
  }

  // Apply category filters
  if (filters.value.categories.length > 0) {
    products = products.filter((p) =>
      filters.value.categories.includes(p.category),
    );
  }

  // Apply price filter
  if (filters.value.minPrice) {
    products = products.filter((p) => p.price >= filters.value.minPrice);
  }

  if (filters.value.maxPrice) {
    products = products.filter((p) => p.price <= filters.value.maxPrice);
  }

  // Apply digital filter
  if (filters.value.digitalOnly) {
    products = products.filter((p) => p.is_digital);
  }

  // Apply stock filter
  if (filters.value.inStockOnly) {
    products = products.filter((p) => p.stock_quantity > 0 || p.is_digital);
  }

  // Apply search filter
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    products = products.filter(
      (p) =>
        p.name.toLowerCase().includes(searchTerm) ||
        p.description.toLowerCase().includes(searchTerm) ||
        (p.composer && p.composer.toLowerCase().includes(searchTerm)) ||
        (p.instrument && p.instrument.toLowerCase().includes(searchTerm)),
    );
  }

  // Apply sorting
  switch (sortBy.value) {
    case "price_low":
      products.sort((a, b) => a.price - b.price);
      break;
    case "price_high":
      products.sort((a, b) => b.price - a.price);
      break;
    case "name":
      products.sort((a, b) => a.name.localeCompare(b.name));
      break;
    case "newest":
    default:
      // Already sorted by newest from API
      break;
  }

  return products;
});

// Pagination
const totalPages = computed(() =>
  Math.ceil(filteredProducts.value.length / itemsPerPage),
);

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredProducts.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

// Methods
const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filters.value.type) params.type = filters.value.type;
    if (filters.value.search) params.search = filters.value.search;

    await productStore.fetchProducts(params);
  } catch (error) {
    console.error("Failed to fetch products:", error);
  } finally {
    loading.value = false;
  }
};

const applyPriceRange = () => {
  currentPage.value = 1;
};

const clearFilters = () => {
  filters.value = {
    type: "",
    categories: [],
    instruments: [],
    minPrice: null,
    maxPrice: null,
    digitalOnly: false,
    inStockOnly: false,
    search: "",
  };
  currentPage.value = 1;
  updateURL();
};

const removeFilter = (key) => {
  if (key === "type") {
    filters.value.type = "";
  } else if (key.startsWith("category-")) {
    const category = key.replace("category-", "");
    filters.value.categories = filters.value.categories.filter(
      (c) => c !== category,
    );
  } else if (key === "digitalOnly") {
    filters.value.digitalOnly = false;
  } else if (key === "inStockOnly") {
    filters.value.inStockOnly = false;
  } else if (key === "priceRange") {
    filters.value.minPrice = null;
    filters.value.maxPrice = null;
  } else if (key === "search") {
    filters.value.search = "";
  }
  currentPage.value = 1;
  updateURL();
};

const handlePageChange = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
};

const updateURL = () => {
  const query = {};
  if (filters.value.type) query.type = filters.value.type;
  if (filters.value.search) query.search = filters.value.search;
  if (filters.value.categories.length > 0)
    query.category = filters.value.categories[0];

  router.push({
    path: "/products",
    query,
  });
};

// Watch for filter changes
watch(
  filters.value,
  () => {
    currentPage.value = 1;
    fetchProducts();
  },
  { deep: true },
);

watch(sortBy, () => {
  currentPage.value = 1;
});

// Watch route changes
watch(
  () => route.query,
  (query) => {
    if (route.name === "Products") {
      filters.value.type = query.type || "";
      filters.value.search = query.search || "";
      filters.value.categories = query.category ? [query.category] : [];

      fetchProducts();
    }
  },
  { immediate: true },
);

onMounted(() => {
  // Initialize from route query
  const query = route.query;
  filters.value.type = query.type || "";
  filters.value.search = query.search || "";
  filters.value.categories = query.category ? [query.category] : [];

  fetchProducts();
});
</script>

<style scoped>
.products-page {
  background-color: #f8f9fa;
}

.form-scrollable {
  max-height: 200px;
  overflow-y: auto;
  padding-right: 8px;
}

.form-scrollable::-webkit-scrollbar {
  width: 6px;
}

.form-scrollable::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.form-scrollable::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}
</style>
