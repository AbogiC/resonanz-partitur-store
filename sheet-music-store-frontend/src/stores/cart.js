import { defineStore } from "pinia";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "./auth";
import router from "@/router";

export const useCartStore = defineStore("cart", () => {
  const cartItems = ref(JSON.parse(localStorage.getItem("cart") || "[]"));
  const cartItemDB = ref([]);
  const isCartOpen = ref(false);
  const authStore = useAuthStore();
  const cartItemCount = ref(0);
  const totalQuantity = ref(0);

  onMounted(async () => {
    await getCartItemCount();
    await getCartItems();
  });

  async function getCartItemCount() {
    try {
      const response = await axios.get("http://localhost:8000/api/cart/count", {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });
      const count = response.data.count;
      cartItemCount.value = count.item_count;
      totalQuantity.value = parseInt(count.total_quantity);
    } catch (error) {
      console.error("Error fetching cart count:", error);
      cartItemCount.value = 0;
      totalQuantity.value = 0;
    }
  }

  const getCartItems = async () => {
    if (!authStore.isAuthenticated) {
      cartItemDB.value = [];
      return;
    }
    try {
      const response = await axios.get("http://localhost:8000/api/cart", {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });
      cartItemDB.value = response.data;
    } catch (error) {
      console.error("Failed to fetch cart items:", error);
      cartItemDB.value = [];
    }
  };

  const addToCart = async (product) => {
    if (!authStore.isAuthenticated) {
      router.push("/login");
      return;
    }

    try {
      await axios.post(
        "http://localhost:8000/api/cart",
        {
          product_id: product.id,
          quantity: 1,
        },
        {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
            "Content-Type": "application/json",
          },
        },
      );

      // Update local cart state
      const existingItem = cartItems.value.find(
        (item) => item.id === product.id,
      );
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cartItems.value.push({ ...product, quantity: 1 });
      }
    } catch (error) {
      console.error("Failed to add to cart:", error);
      throw error;
    }
  };

  const removeFromCart = async (cartId) => {
    try {
      await axios.delete(`http://localhost:8000/api/cart/${cartId}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });
    } catch (error) {
      console.error("Failed to remove from cart:", error);
      throw error;
    }
  };

  const updateQuantity = async (cartId, isIncrease) => {
    console.log("Increasing quantity for item ID:", cartId);
    if (isIncrease) {
      try {
        await axios.put(`http://localhost:8000/api/cart/increase/${cartId}`, {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
            "Content-Type": "application/json",
          },
        });
      } catch (error) {
        console.error("Failed to update cart item quantity:", error);
        throw error;
      }
    } else {
      try {
        await axios.put(`http://localhost:8000/api/cart/decrease/${cartId}`, {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
            "Content-Type": "application/json",
          },
        });
      } catch (error) {
        console.error("Failed to update cart item quantity:", error);
        throw error;
      }
    }
  };

  const clearCart = () => {
    cartItems.value = [];
    saveCart();
  };

  const toggleCart = () => {
    isCartOpen.value = !isCartOpen.value;
  };

  const saveCart = () => {
    localStorage.setItem("cart", JSON.stringify(cartItems.value));
  };

  return {
    cartItems,
    cartItemDB,
    isCartOpen,
    cartItemCount,
    totalQuantity,
    getCartItems,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    toggleCart,
  };
});
