<template>
    <AdminLayout active-menu="orders" page-title="Order Details">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Order Items</h3>
                    <table class="w-full">
                        <thead class="bg-cream-50">
                            <tr>
                                <th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Product</th>
                                <th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Qty</th>
                                <th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Price</th>
                                <th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-50">
                            <tr v-for="item in order.items" :key="item.id">
                                <td class="px-3 py-3 text-sm font-medium text-brand-900">{{ item.product_name }}</td>
                                <td class="px-3 py-3 text-sm text-brand-600">{{ item.quantity }}</td>
                                <td class="px-3 py-3 text-sm text-brand-600">৳{{ item.price }}</td>
                                <td class="px-3 py-3 text-sm font-medium text-brand-900">৳{{ item.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 space-y-1.5 text-sm border-t border-brand-100 pt-4">
                        <div class="flex justify-between"><span class="text-brand-500">Subtotal</span><span class="font-medium text-brand-900">৳{{ order.subtotal }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-500">Delivery Charge</span><span class="font-medium text-brand-900">৳{{ order.delivery_charge }}</span></div>
                        <div v-if="order.discount > 0" class="flex justify-between text-green-600"><span>Discount</span><span class="font-medium">-৳{{ order.discount }}</span></div>
                        <div class="flex justify-between font-bold text-lg border-t border-brand-100 pt-2"><span class="text-brand-900">Total</span><span class="text-gold-600 font-serif">৳{{ order.total }}</span></div>
                    </div>
                </div>
                <div v-if="order.notes" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-2">Customer Notes</h3>
                    <p class="text-sm text-brand-600">{{ order.notes }}</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Customer Info</h3>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><span class="text-brand-400">Name</span><span class="font-medium text-brand-900">{{ order.customer_name }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Phone</span><span class="font-medium text-brand-900">{{ order.customer_phone }}</span></div>
                        <div v-if="order.customer_address" class="flex justify-between"><span class="text-brand-400">Address</span><span class="font-medium text-brand-900 text-right">{{ order.customer_address }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Delivery</span><span class="font-medium text-brand-900">{{ order.delivery_type === 'pickup' ? 'Pickup' : 'Home Delivery' }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Branch</span><span class="font-medium text-brand-900">{{ order.branch?.name }}</span></div>
                        <div v-if="order.delivery_area" class="flex justify-between"><span class="text-brand-400">Area</span><span class="font-medium text-brand-900">{{ order.delivery_area?.name }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Payment</span><span class="font-medium text-brand-900">{{ order.payment_method === 'advance_payment' ? 'Advance Payment' : 'Cash on Delivery' }}</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Order Status</h3>
                    <select v-model="status" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none mb-3">
                        <option v-for="s in statuses" :key="s" :value="s">{{ s.replace(/_/g, ' ') }}</option>
                    </select>
                    <button @click="updateStatus" class="w-full bg-brand-700 text-white py-3 rounded-xl font-bold hover:bg-brand-800 transition">Update Status</button>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Payment Status</h3>
                    <!-- Advance Payment Info -->
                    <div v-if="order.payment_method === 'advance_payment'" class="bg-gold-50 border border-gold-200 rounded-xl p-3 mb-3 space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-brand-400">Method</span><span class="font-bold text-brand-900">Advance Payment</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Advance Amount</span><span class="font-bold text-green-600">৳{{ order.advance_amount || 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Remaining (COD)</span><span class="font-bold text-brand-900">৳{{ Math.max(0, (order.total || 0) - (order.advance_amount || 0)) }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Transaction ID</span><span class="font-mono font-bold text-brand-900">{{ order.transaction_id || 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Verified</span><span :class="order.payment_verified ? 'text-green-600' : 'text-red-600'" class="font-bold">{{ order.payment_verified ? 'Yes' : 'No' }}</span></div>
                    </div>
                    <div v-if="order.payment_method === 'advance_payment' && !order.payment_verified" class="mb-3">
                        <button @click="verifyPayment" class="w-full bg-green-500 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-green-600 transition">Verify Payment</button>
                    </div>
                    <div class="flex gap-2 mb-3">
                        <button @click="updatePayment('unpaid')" :class="order.payment_status === 'unpaid' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600'" class="flex-1 py-2.5 rounded-xl font-bold text-sm transition">Unpaid</button>
                        <button @click="updatePayment('paid')" :class="order.payment_status === 'paid' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-600'" class="flex-1 py-2.5 rounded-xl font-bold text-sm transition">Paid</button>
                    </div>
                    <p class="text-xs text-brand-400 text-center">Current: <span :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-red-600'" class="font-bold capitalize">{{ order.payment_status }}</span></p>
                </div>
                <div v-if="order.delivery_type === 'home_delivery'" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Assign Delivery Man</h3>
                    <select v-model="deliveryManId" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none mb-3">
                        <option value="">Select delivery man...</option>
                        <option v-for="man in deliveryMen" :key="man.id" :value="man.id">{{ man.name }} — {{ man.phone }} ({{ man.branch?.name || 'Any' }})</option>
                    </select>
                    <button @click="assignDeliveryMan" class="w-full bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition">Assign</button>
                    <div v-if="order.delivery_man" class="mt-3 bg-cream-100 rounded-xl px-4 py-3 text-sm">
                        <div class="text-brand-400 text-xs mb-1">Assigned to:</div>
                        <div class="font-bold text-brand-900">{{ order.delivery_man.name }}</div>
                        <div class="text-brand-600">{{ order.delivery_man.phone }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ order: Object, deliveryMen: Array });
const statuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
const status = ref(props.order.status);
const deliveryManId = ref(props.order.delivery_man_id || '');

const updateStatus = () => router.patch(route('admin.orders.status', props.order.id), { status: status.value });
const updatePayment = (paymentStatus) => {
    router.patch(route('admin.orders.payment', props.order.id), { payment_status: paymentStatus }, {
        onSuccess: () => { props.order.payment_status = paymentStatus; },
    });
};
const assignDeliveryMan = () => router.patch(route('admin.orders.delivery-man', props.order.id), { delivery_man_id: deliveryManId.value });
const verifyPayment = () => router.patch(route('admin.orders.verify-payment', props.order.id), {}, {
    onSuccess: () => { props.order.payment_verified = true; props.order.payment_status = 'paid'; },
});
</script>
