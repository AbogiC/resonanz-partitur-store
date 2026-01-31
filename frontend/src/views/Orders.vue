<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-md-10 mx-auto">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="mb-4">My Orders</h2>

            <!-- Empty State -->
            <div v-if="orders.length === 0" class="text-center text-muted py-5">
              No orders yet.
            </div>

            <!-- Orders Table -->
            <div v-else class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in orders" :key="order.id">
                    <td>#{{ order.order_number }}</td>
                    <td>{{ formatDate(order.created_at) }}</td>
                    <td>{{ formatCurrency(order.total_amount) }}</td>
                    <td>
                      <span class="badge" :class="statusClass(order.status)">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="text-end">
                      <!-- Pending Actions -->
                      <div class="d-flex justify-content-end">
                        <template v-if="order.status === 'pending'">
                          <button class="btn btn-sm btn-primary me-2" @click="continuePayment(order.id)">
                            Continue Payment
                          </button>
                          <button class="btn btn-sm btn-outline-danger" @click="cancelOrder(order)">
                            Cancel
                          </button>
                        </template>

                        <!-- Paid -->
                        <span v-else-if="order.status === 'paid'" class="text-success">
                          ✔ Completed
                        </span>

                        <!-- Canceled -->
                        <span v-else class="text-muted"> — No action </span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <CheckoutModal :is-open="isCheckoutModalOpen" :loading-profile="loadingProfile" :cart-items="cartItems"
    :cart-total="cartTotal" :user-profile="userProfile" :has-shipping-address="hasShippingAddress"
    :has-billing-address="hasBillingAddress" :format-address="formatAddress" @close="closeCheckoutModal"
    @confirm="confirmCheckout" />
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import axios from "axios";
import CheckoutModal from "@/components/shared/CheckoutModal.vue";
import { useAuthStore } from "@/stores/auth";
import { getUserProfile } from "@/services/api.js";
import { useOrderStore } from "@/stores/order";

defineOptions({
  name: "OrdersView",
});

onMounted(() => {
  fetchOrders();
});

/**
 * Mock order data
 * Replace this with API result later
 */
const orders = ref([]);
const isCheckoutModalOpen = ref(false);
const loadingProfile = ref(false);
const userProfile = ref({
  billing_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: "",
  },
  shipping_address: {
    street: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    phone: "",
  },
});

const authStore = useAuthStore();
const orderStore = useOrderStore();

const orderStoreData = computed(() => orderStore.orderItems);
const cartItems = computed(() => orderStoreData.value.items);
const cartTotal = computed(() => orderStoreData.value.total_amount);

const hasShippingAddress = computed(() => {
  const addr = userProfile.value.shipping_address;
  return (
    addr &&
    (addr.street || addr.city || addr.state || addr.postal_code || addr.country)
  );
});

const hasBillingAddress = computed(() => {
  const addr = userProfile.value.billing_address;
  return (
    addr &&
    (addr.street || addr.city || addr.state || addr.postal_code || addr.country)
  );
});

const formatAddress = (address) => {
  if (!address) return "No address provided";

  const parts = [];
  if (address.street) parts.push(address.street);
  if (address.city) parts.push(address.city);
  if (address.state) parts.push(address.state);
  if (address.postal_code) parts.push(address.postal_code);
  if (address.country) parts.push(address.country);

  return parts.length > 0 ? parts.join(", ") : "No address provided";
};

const loadUserProfile = async () => {
  loadingProfile.value = true;
  try {
    const profile = await getUserProfile();
    userProfile.value = profile;
  } catch (error) {
    console.error("Error loading user profile:", error);
    // If API fails, try to get from auth store
    if (authStore.user) {
      userProfile.value = {
        billing_address:
          authStore.user.billing_address || userProfile.value.billing_address,
        shipping_address:
          authStore.user.shipping_address || userProfile.value.shipping_address,
      };
    }
  } finally {
    loadingProfile.value = false;
  }
};

const showCheckoutModal = async (orderId) => {
  isCheckoutModalOpen.value = true;
  await loadUserProfile();
  await orderStore.getOrderItems(orderId);
};

const closeCheckoutModal = () => {
  isCheckoutModalOpen.value = false;
};

const confirmCheckout = () => {
  // Close the modal
  closeCheckoutModal();
};

const fetchOrders = async () => {
  const response = await axios.get("/api/orders", {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
      "Content-Type": "application/json",
    },
  });

  orders.value = response.data.records;
  console.log("Fetched orders:", orders.value);
};

/* Helpers */
const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(value);

const formatDate = (date) => new Date(date).toLocaleDateString("id-ID");

const statusClass = (status) => {
  return {
    "bg-warning text-dark": status === "pending",
    "bg-success": status === "paid",
    "bg-secondary": status === "cancelled",
  };
};

/* Actions */
const continuePayment = (orderId) => {
  // redirect to payment page
  showCheckoutModal(orderId);
  // example:
  // router.push(`/payment/${order.id}`)
};

const cancelOrder = (order) => {
  if (!confirm(`Cancel order #${order.id}?`)) return;
  order.status = "Canceled";
};
</script>
