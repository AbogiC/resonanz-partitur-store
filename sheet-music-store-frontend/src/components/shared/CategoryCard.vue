<template>
  <router-link
    :to="to"
    class="category-card text-decoration-none h-100 d-block"
  >
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="category-icon" :class="color">
          <span class="fs-1">{{ getIconSymbol(icon) }}</span>
        </div>
        <span class="display-6 fw-bold text-dark">{{ count }}+</span>
      </div>
      <h3 class="h4 fw-bold text-dark mb-2">{{ title }}</h3>
      <p class="text-muted mb-4">{{ description }}</p>
      <div class="d-flex align-items-center text-primary fw-medium">
        <span>Browse Collection</span>
        <span
          class="ms-2 transition-transform"
          style="transition: transform 0.3s ease"
          >→</span
        >
      </div>
    </div>
  </router-link>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    required: true,
  },
  count: {
    type: Number,
    default: 0,
  },
  to: {
    type: String,
    required: true,
  },
});

const getIconSymbol = (iconName) => {
  const iconMap = {
    "mdi:piano": "🎹",
    "mdi:guitar-acoustic": "🎸",
    "mdi:violin": "🎻",
    "mdi:tshirt-crew": "👕",
  };
  return iconMap[iconName] || "🎵";
};
</script>

<style scoped>
.category-card:hover .category-icon {
  transform: scale(1.1);
}

.category-card:hover .fa-arrow-right {
  transform: translateX(5px);
}
</style>
