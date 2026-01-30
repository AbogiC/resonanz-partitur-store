import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";
import { useAuthStore } from "./auth";
import router from "@/router";

export const useCartStore = defineStore("cart", () => {
  const cartItems = ref(JSON.parse(localStorage.getItem("cart") || "[]"));
  const isCartOpen = ref(false);
  const authStore = useAuthStore();

  const cartItemCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0);
  });

  const cartTotal = computed(() => {
    return cartItems.value.reduce(
      (total, item) => total + item.price * item.quantity,
      0,
    );
  });

  const getCartItems = async () => {
    if (!authStore.isAuthenticated) {
      cartItems.value = [];
      return;
    } else {
      try {
        const response = await axios.get("http://localhost:8000/api/cart", {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        });
        cartItems.value = response.data;
      } catch (error) {
        console.error("Failed to fetch cart items:", error);
        cartItems.value = [];
      }
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
      saveCart();
    } catch (error) {
      console.error("Failed to add to cart:", error);
      throw error;
    }
  };

  const removeFromCart = (productId) => {
    cartItems.value = cartItems.value.filter((item) => item.id !== productId);
    saveCart();
  };

  const updateQuantity = (productId, quantity) => {
    const item = cartItems.value.find((item) => item.id === productId);
    if (item) {
      item.quantity = quantity;
      if (item.quantity <= 0) {
        removeFromCart(productId);
      } else {
        saveCart();
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
    isCartOpen,
    cartItemCount,
    cartTotal,
    getCartItems,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    toggleCart,
  };
});
