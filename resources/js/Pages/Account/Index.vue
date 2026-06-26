<template>
    <CustomerLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Header -->
            <div class="mb-8">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Dashboard</span>
                <h1 class="font-serif text-3xl font-bold text-brand-900 mt-2">My Account</h1>
                <div class="w-20 h-1 bg-gold-500 mt-4 rounded-full"></div>
            </div>

            <!-- User Info -->
            <div class="bg-white rounded-2xl card-shadow p-6 mb-8 border border-brand-100">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full hero-gradient flex items-center justify-center">
                        <span class="text-gold-400 font-serif font-bold text-2xl">{{ user.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div>
                        <h2 class="font-serif text-xl font-bold text-brand-900">{{ user.name }}</h2>
                        <p class="text-brand-500 text-sm">{{ user.phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl card-shadow p-6 border border-brand-100">
                    <div class="text-3xl font-serif font-bold text-brand-700">{{ stats.total_orders }}</div>
                    <div class="text-sm text-brand-400 mt-1">Total Orders</div>
                </div>
                <div class="bg-white rounded-2xl card-shadow p-6 border border-brand-100">
                    <div class="text-3xl font-serif font-bold text-gold-600">৳{{ stats.total_spent }}</div>
                    <div class="text-sm text-brand-400 mt-1">Total Spent</div>
                </div>
                <div class="bg-white rounded-2xl card-shadow p-6 border border-brand-100">
                    <div class="text-3xl font-serif font-bold text-brand-700">{{ stats.pending_orders }}</div>
                    <div class="text-sm text-brand-400 mt-1">Pending Orders</div>
                </div>
                <div class="bg-white rounded-2xl card-shadow p-6 border border-brand-100">
                    <div class="text-3xl font-serif font-bold text-brand-700">{{ stats.total_custom_cakes }}</div>
                    <div class="text-sm text-brand-400 mt-1">Custom Cakes</div>
                </div>
            </div>

            <!-- Orders -->
            <div class="bg-white rounded-2xl card-shadow border border-brand-100 mb-8">
                <div class="p-6 border-b border-brand-100">
                    <h2 class="font-serif text-xl font-bold text-brand-900">Order History</h2>
                </div>
                <div v-if="orders.length === 0" class="p-8 text-center text-brand-400">
                    No orders yet. <Link :href="route('products.index')" class="text-gold-600 hover:text-gold-500">Browse products →</Link>
                </div>
                <div v-else class="divide-y divide-brand-50">
                    <div v-for="order in orders" :key="order.id" class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-cream-50 transition">
                        <div>
                            <div class="font-bold text-brand-900">{{ order.order_number }}</div>
                            <div class="text-sm text-brand-400">{{ order.branch?.name }} · {{ order.items?.length || 0 }} items · ৳{{ order.total }}</div>
                            <div class="text-xs text-brand-300 mt-1">{{ formatDate(order.created_at) }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-medium">{{ order.status }}</span>
                            <Link :href="route('checkout.track') + '?order_number=' + order.order_number" class="text-gold-600 hover:text-gold-500 text-sm font-medium">Track →</Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Cake Orders -->
            <div v-if="customCakeOrders.length > 0" class="bg-white rounded-2xl card-shadow border border-brand-100">
                <div class="p-6 border-b border-brand-100">
                    <h2 class="font-serif text-xl font-bold text-brand-900">Custom Cake Orders</h2>
                </div>
                <div class="divide-y divide-brand-50">
                    <div v-for="cc in customCakeOrders" :key="cc.id" class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-cream-50 transition">
                        <div>
                            <div class="font-bold text-brand-900">{{ cc.order_number }}</div>
                            <div class="text-sm text-brand-400">{{ cc.cake_type || 'Custom Cake' }} · {{ cc.cake_size || 'N/A' }}</div>
                            <div class="text-xs text-brand-300 mt-1">Delivery: {{ formatDate(cc.delivery_date) }}</div>
                        </div>
                        <span :class="statusClass(cc.status)" class="px-3 py-1 rounded-full text-xs font-medium">{{ cc.status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    orders: Array,
    customCakeOrders: Array,
    stats: Object,
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-blue-100 text-blue-700',
        preparing: 'bg-purple-100 text-purple-700',
        ready: 'bg-indigo-100 text-indigo-700',
        delivered: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};
</script>
