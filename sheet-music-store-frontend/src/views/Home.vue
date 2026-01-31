<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container position-relative">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div class="hero-content">
              <h1 class="display-3 fw-bold mb-4">
                Your Music,<br />
                <span>Your Store</span>
              </h1>
              <p class="lead mb-5 opacity-75">
                Discover premium sheet music, instruments, and musical
                merchandise. From classical masterpieces to modern hits, we have
                everything for the musician in you.
              </p>
              <div class="d-flex flex-column flex-sm-row gap-3">
                <router-link to="/products?type=sheet_music"
                  class="btn bg-white btn-lg d-flex align-items-center justify-content-center gap-2">
                  <i class="bi bi-music-note"></i>
                  Browse Sheet Music
                </router-link>
                <router-link to="/products?type=merchandise"
                  class="btn btn-outline-light btn-lg d-flex align-items-center justify-content-center gap-2">
                  <i class="bi bi-bag"></i>
                  Shop Merchandise
                </router-link>
              </div>
            </div>
          </div>
          <div class="col-lg-4 d-none d-lg-block">
            <div class="position-relative">
              <div class="position-absolute top-50 start-0 translate-middle-y">
                <i class="bi bi-music-note text-white opacity-25" style="font-size: 15rem"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="display-5 fw-bold mb-3">Browse by Category</h2>
          <p class="lead text-muted">Find exactly what you're looking for</p>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <CategoryCard title="Piano" description="Classical to contemporary" icon="mdi:piano"
              color="bg-primary bg-opacity-10 text-primary" :count="pianoCount"
              to="/products?type=sheet_music&category=piano" />
          </div>
          <div class="col-md-6 col-lg-3">
            <CategoryCard title="Guitar" description="Acoustic to electric" icon="mdi:guitar-acoustic"
              color="bg-success bg-opacity-10 text-success" :count="guitarCount"
              to="/products?type=sheet_music&category=guitar" />
          </div>
          <div class="col-md-6 col-lg-3">
            <CategoryCard title="Violin" description="Classical strings" icon="mdi:violin"
              color="bg-purple bg-opacity-10 text-purple" :count="violinCount"
              to="/products?type=sheet_music&category=violin" />
          </div>
          <div class="col-md-6 col-lg-3">
            <CategoryCard title="Merchandise" description="Apparel & accessories" icon="mdi:tshirt-crew"
              color="bg-warning bg-opacity-10 text-warning" :count="merchandiseCount" to="/products?type=merchandise" />
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Sheet Music -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
          <div>
            <h2 class="display-5 fw-bold">Featured Sheet Music</h2>
            <p class="text-muted mb-0">Popular pieces loved by musicians</p>
          </div>
          <router-link to="/products?type=sheet_music"
            class="btn btn-link text-decoration-none d-flex align-items-center gap-1">
            View All
            <i class="bi bi-chevron-right"></i>
          </router-link>
        </div>

        <div v-if="loadingFeatured" class="row g-4">
          <div v-for="n in 4" :key="n" class="col-md-6 col-lg-3">
            <div class="product-card">
              <div class="placeholder-glow">
                <div class="placeholder placeholder-lg bg-secondary" style="height: 200px"></div>
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

        <div v-else-if="featuredSheetMusic.length > 0" class="row g-4">
          <div v-for="product in featuredSheetMusic" :key="product.id" class="col-md-6 col-lg-4 col-xl-3">
            <ProductCard :product="product" />
          </div>
        </div>

        <div v-else class="text-center py-5">
          <i class="bi bi-music-note text-muted mb-3" style="font-size: 4rem"></i>
          <h3 class="h4 mb-2">No featured sheet music available</h3>
          <p class="text-muted">Check back soon for new additions!</p>
        </div>
      </div>
    </section>

    <!-- Featured Merchandise -->
    <section class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
          <div>
            <h2 class="display-5 fw-bold">Popular Merchandise</h2>
            <p class="text-muted mb-0">Gear up with our best-selling items</p>
          </div>
          <router-link to="/products?type=merchandise"
            class="btn btn-link text-decoration-none d-flex align-items-center gap-1">
            View All
            <i class="bi bi-chevron-right"></i>
          </router-link>
        </div>

        <div v-if="loadingFeatured" class="row g-4">
          <div v-for="n in 4" :key="n" class="col-md-6 col-lg-3">
            <div class="product-card">
              <div class="placeholder-glow">
                <div class="placeholder placeholder-lg bg-secondary" style="height: 200px"></div>
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

        <div v-else-if="featuredMerchandise.length > 0" class="row g-4">
          <div v-for="product in featuredMerchandise" :key="product.id" class="col-md-6 col-lg-4 col-xl-3">
            <ProductCard :product="product" />
          </div>
        </div>

        <div v-else class="text-center py-5">
          <i class="bi bi-bag text-muted mb-3" style="font-size: 4rem"></i>
          <h3 class="h4 mb-2">No featured merchandise available</h3>
          <p class="text-muted">Check back soon for new arrivals!</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
      <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Ready to Make Music?</h2>
        <p class="lead mb-5 opacity-75 max-w-2xl mx-auto">
          Join thousands of musicians who trust us for their musical needs.
          Digital downloads available instantly!
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
          <router-link to="/register" class="btn btn-light btn-lg text-primary">
            Create Free Account
          </router-link>
          <router-link to="/products" class="btn btn-outline-light btn-lg">
            Browse All Products
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useProductStore } from "@/stores/product";
import CategoryCard from "@/components/shared/CategoryCard.vue";
import ProductCard from "@/components/product/ProductCard.vue";

defineOptions({
  name: "HomePage",
});

const productStore = useProductStore();

const loadingFeatured = ref(true);
const featuredSheetMusic = ref([]);
const featuredMerchandise = ref([]);
const pianoCount = ref(0);
const guitarCount = ref(0);
const violinCount = ref(0);
const merchandiseCount = ref(0);

onMounted(async () => {
  try {
    // Fetch featured products
    const [sheetMusic, merchandise] = await Promise.all([
      productStore.getProducts({ type: "sheet_music", limit: 4 }),
      productStore.getProducts({ type: "merchandise", limit: 4 }),
    ]);

    featuredSheetMusic.value = sheetMusic.records || [];
    featuredMerchandise.value = merchandise.records || [];

    // Get counts
    pianoCount.value = featuredSheetMusic.value.filter(
      (p) =>
        p.category === "piano" || p.instrument?.toLowerCase().includes("piano"),
    ).length;

    guitarCount.value = featuredSheetMusic.value.filter(
      (p) =>
        p.category === "guitar" ||
        p.instrument?.toLowerCase().includes("guitar"),
    ).length;

    violinCount.value = featuredSheetMusic.value.filter(
      (p) =>
        p.category === "violin" ||
        p.instrument?.toLowerCase().includes("violin"),
    ).length;

    merchandiseCount.value = featuredMerchandise.value.length;
  } catch (error) {
    console.error("Failed to load featured products:", error);
  } finally {
    loadingFeatured.value = false;
  }
});
</script>

<style scoped>
.home-page {
  background-color: #f8f9fa;
}

.max-w-2xl {
  max-width: 600px;
}

.bg-purple {
  background-color: #6f42c1;
}

.text-purple {
  color: #6f42c1;
}
</style>
