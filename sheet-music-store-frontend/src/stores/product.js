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
      // TODO: Replace with actual API call
      // const response = await axios.get('/api/products', { params })
      // return response.data

      // Mock data for now
      const mockProducts = [
        {
          id: 1,
          name: "Beethoven Sonata No. 14",
          type: "sheet_music",
          category: "piano",
          price: 12.99,
          image_url: "/placeholder.jpg",
          composer: "Ludwig van Beethoven",
          instrument: "Piano",
          is_digital: true,
          stock_quantity: 0,
          description:
            "The famous Moonlight Sonata by Ludwig van Beethoven, arranged for solo piano.",
          difficulty_level: "Intermediate",
          duration_minutes: 15,
        },
        {
          id: 2,
          name: "Guitar T-Shirt",
          type: "merchandise",
          category: "apparel",
          price: 24.99,
          image_url: "/placeholder.jpg",
          is_digital: false,
          stock_quantity: 50,
          description:
            "Comfortable cotton t-shirt featuring guitar graphics. Perfect for music lovers.",
        },
      ];

      return {
        records: mockProducts,
        total: mockProducts.length,
      };
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
      // TODO: Replace with actual API call
      // const response = await axios.get(`/api/products/${id}`)
      // return response.data

      // Mock data
      return {
        id: parseInt(id),
        name: "Sample Product",
        type: "sheet_music",
        category: "piano",
        price: 15.99,
        image_url: "/placeholder.jpg",
        composer: "Composer Name",
        is_digital: true,
        stock_quantity: 0,
      };
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
