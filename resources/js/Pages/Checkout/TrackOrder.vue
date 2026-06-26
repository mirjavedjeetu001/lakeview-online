<template>
    <CustomerLayout>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Track Your Order</h1>
            <form @submit.prevent="trackOrder" class="flex gap-2 mb-6">
                <input v-model="orderNumber" type="text" placeholder="Enter your order number (e.g. ORD-20240101-ABC123)" class="flex-1 rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" />
                <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-brand-700 transition">Track</button>
            </form>

            <div v-if="order" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-sm text-gray-500">Order Number</div>
                        <div class="font-bold text-lg">{{ order.order_number }}</div>
                    </div>
                    <div class="px-4 py-2 rounded-full text-sm font-medium" :class="statusClass(order.status)">{{ order.status.replace(/_/g, ' ') }}</div>
                </div>
                <div class="space-y-2 text-sm border-t pt-4">
                    <div class="flex justify-between"><span class="text-gray-500">Branch</span><span>{{ order.branch?.name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Customer</span><span>{{ order.customer_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Phone</span><span>{{ order.customer_phone }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Delivery Type</span><span>{{ order.delivery_type === 'pickup' ? 'Pickup' : 'Home Delivery' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-bold">৳{{ order.total }}</span></div>
                </div>
                <div class="border-t pt-4 mt-4">
                    <h3 class="font-bold mb-2">Items</h3>
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm py-1">
                        <span>{{ item.product_name }} x {{ item.quantity }}</span>
                        <span>৳{{ item.total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ order: Object });
const orderNumber = ref('');

const trackOrder = () => {
    if (!orderNumber.value) return;
    router.get(route('checkout.track'), { order_number: orderNumber.value }, { preserveScroll: true });
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-blue-100 text-blue-700',
        preparing: 'bg-purple-100 text-purple-700',
        ready: 'bg-indigo-100 text-indigo-700',
        out_for_delivery: 'bg-orange-100 text-orange-700',
        delivered: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};
</script>
