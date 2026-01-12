import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";

export const useProductStore = defineStore("product", () => {
  const products = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const totalProducts = ref(0);
  const productCountsByType = ref({});
  const categories = ref([]);
  const categoryCounts = ref({});

  const fetchProducts = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get("/api/products", { params });
      const data = response.data;

      // Assuming the API returns { records: [...], total?: number, counts?: {...} }
      // But currently, the backend returns { records: [...] }
      // We need to adjust based on actual response

      products.value = data && data.records ? data.records : [];
      totalProducts.value = products.value.length;

      // Calculate counts from products
      const counts = {};
      const catCounts = {};
      const catSet = new Set();

      products.value.forEach((product) => {
        counts[product.type] = (counts[product.type] || 0) + 1;
        if (product.category) {
          catSet.add(product.category);
          catCounts[product.category] = (catCounts[product.category] || 0) + 1;
        }
      });

      productCountsByType.value = counts;
      categories.value = Array.from(catSet);
      categoryCounts.value = catCounts;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getProducts = async (params = {}) => {
    const response = await axios.get("/api/products", { params });
    return response.data;
  };

  const getProductById = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get(`/api/products/${id}`);
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    products,
    loading,
    error,
    totalProducts,
    productCountsByType,
    categories,
    categoryCounts,
    fetchProducts,
    getProducts,
    getProductById,
  };
});
