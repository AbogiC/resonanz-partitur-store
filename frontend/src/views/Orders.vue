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
                    <td>#{{ order.id }}</td>
                    <td>{{ formatDate(order.date) }}</td>
                    <td>Rp {{ formatCurrency(order.total) }}</td>
                    <td>
                      <span class="badge" :class="statusClass(order.status)">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="text-end">
                      <!-- Pending Actions -->
                      <template v-if="order.status === 'Pending'">
                        <button class="btn btn-sm btn-primary me-2" @click="continuePayment(order)">
                          Continue Payment
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="cancelOrder(order)">
                          Cancel
                        </button>
                      </template>

                      <!-- Paid -->
                      <span v-else-if="order.status === 'Paid'" class="text-success">
                        ✔ Completed
                      </span>

                      <!-- Canceled -->
                      <span v-else class="text-muted">
                        — No action
                      </span>
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
import { ref } from 'vue'

defineOptions({
  name: 'OrdersView'
})

/**
 * Mock order data
 * Replace this with API result later
 */
const orders = ref([
  {
    id: 1001,
    date: '2026-01-20',
    total: 250000,
    status: 'Pending'
  },
  {
    id: 1002,
    date: '2026-01-18',
    total: 180000,
    status: 'Paid'
  },
  {
    id: 1003,
    date: '2026-01-15',
    total: 320000,
    status: 'Canceled'
  }
])

/* Helpers */
const formatCurrency = (value) =>
  value.toLocaleString('id-ID')

const formatDate = (date) =>
  new Date(date).toLocaleDateString('id-ID')

const statusClass = (status) => {
  return {
    'bg-warning text-dark': status === 'Pending',
    'bg-success': status === 'Paid',
    'bg-secondary': status === 'Canceled'
  }
}

/* Actions */
const continuePayment = (order) => {
  // redirect to payment page
  console.log('Continue payment for order:', order.id)
  // example:
  // router.push(`/payment/${order.id}`)
}

const cancelOrder = (order) => {
  if (!confirm(`Cancel order #${order.id}?`)) return
  order.status = 'Canceled'
}
</script>
