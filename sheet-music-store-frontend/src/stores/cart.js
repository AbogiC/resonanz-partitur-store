import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useCartStore = defineStore("cart", () => {
  const cartItems = ref(JSON.parse(localStorage.getItem("cart") || "[]"));
  const isCartOpen = ref(false);

  const cartItemCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0);
  });

  const cartTotal = computed(() => {
    return cartItems.value.reduce(
      (total, item) => total + item.price * item.quantity,
      0,
    );
  });

  const addToCart = (product) => {
    const existingItem = cartItems.value.find((item) => item.id === product.id);
    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cartItems.value.push({ ...product, quantity: 1 });
    }
    saveCart();
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
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    toggleCart,
  };
});
