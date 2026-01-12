import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";

export const useProductStore = defineStore("product", () => {
  const products = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const getProducts = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get("/api/products", { params });
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
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
    getProducts,
    getProductById,
  };
});
