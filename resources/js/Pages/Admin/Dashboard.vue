<template>
    <AdminLayout active-menu="dashboard" page-title="Dashboard">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div v-for="(stat, key) in statCards" :key="key" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">{{ stat.label }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ stat.value }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="stat.bg">
                        <span class="text-2xl">{{ stat.icon }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">Recent Orders</h3>
                <div v-if="recentOrders.length" class="space-y-3">
                    <div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <div class="font-medium text-sm">{{ order.order_number }}</div>
                            <div class="text-xs text-gray-500">{{ order.customer_name }} - ৳{{ order.total }}</div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusClass(order.status)">{{ order.status }}</span>
                    </div>
                </div>
                <div v-else class="text-gray-500 text-sm py-4 text-center">No orders yet</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">Recent Custom Cake Requests</h3>
                <div v-if="recentCustomCakes.length" class="space-y-3">
                    <div v-for="cake in recentCustomCakes" :key="cake.id" class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <div class="font-medium text-sm">{{ cake.order_number }}</div>
                            <div class="text-xs text-gray-500">{{ cake.customer_name }} - {{ cake.delivery_date }}</div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusClass(cake.status)">{{ cake.status }}</span>
                    </div>
                </div>
                <div v-else class="text-gray-500 text-sm py-4 text-center">No custom cake requests yet</div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({ stats: Object, recentOrders: Array, recentCustomCakes: Array });

const statCards = computed(() => [
    { label: 'Total Orders', value: props.stats.total_orders, icon: '📦', bg: 'bg-blue-100' },
    { label: 'Pending Orders', value: props.stats.pending_orders, icon: '⏳', bg: 'bg-yellow-100' },
    { label: 'Total Revenue', value: '৳' + props.stats.total_revenue, icon: '💰', bg: 'bg-green-100' },
    { label: 'Products', value: props.stats.total_products, icon: '🍰', bg: 'bg-brand-100' },
    { label: 'Categories', value: props.stats.total_categories, icon: '📂', bg: 'bg-purple-100' },
    { label: 'Branches', value: props.stats.total_branches, icon: '🏪', bg: 'bg-indigo-100' },
    { label: 'Active Coupons', value: props.stats.active_coupons, icon: '🎟️', bg: 'bg-pink-100' },
    { label: 'Cake Requests', value: props.stats.custom_cake_requests, icon: '🎂', bg: 'bg-red-100' },
]);

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
