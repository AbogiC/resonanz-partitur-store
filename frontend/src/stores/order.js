import { defineStore } from "pinia";
import { ref } from "vue";
import { useAuthStore } from "./auth";
import axios from "axios";

export const useOrderStore = defineStore("order", () => {
  const orderItems = ref([]);

  const authStore = useAuthStore();

  const getOrderItems = async (orderId) => {
    try {
      const response = await axios.get(`/api/orders/${orderId}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          "Content-Type": "application/json",
        },
      });
      orderItems.value = response.data;
    } catch (error) {
      console.error("Failed to fetch order items:", error);
      orderItems.value = [];
    }
  };
  return {
    orderItems,
    getOrderItems,
  };
});
