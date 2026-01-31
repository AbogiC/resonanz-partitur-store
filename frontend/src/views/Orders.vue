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
                      <div class="d-flex justify-center-end">
                        <template v-if="order.status === 'pending'">
                          <button
                            class="btn btn-sm btn-primary me-2"
                            @click="continuePayment(order)"
                          >
                            Continue Payment
                          </button>
                          <button
                            class="btn btn-sm btn-outline-danger"
                            @click="cancelOrder(order)"
                          >
                            Cancel
                          </button>
                        </template>

                        <!-- Paid -->
                        <span
                          v-else-if="order.status === 'paid'"
                          class="text-success"
                        >
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
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";

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
const continuePayment = (order) => {
  // redirect to payment page
  console.log("Continue payment for order:", order.id);
  // example:
  // router.push(`/payment/${order.id}`)
};

const cancelOrder = (order) => {
  if (!confirm(`Cancel order #${order.id}?`)) return;
  order.status = "Canceled";
};
</script>
