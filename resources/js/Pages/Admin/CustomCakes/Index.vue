<template>
    <AdminLayout active-menu="custom-cakes" page-title="Custom Cake Orders">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="font-bold text-gray-900">Custom Cake Orders</h2>
                <select v-model="statusFilter" class="rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 text-sm" @change="doFilter">
                    <option value="">All Status</option>
                    <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                </select>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cake Details</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ order.order_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ order.customer_name }}<br><span class="text-xs">{{ order.customer_phone }}</span></td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ order.cake_type || '-' }} / {{ order.cake_size || '-' }} / {{ order.cake_flavor || '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ order.delivery_date }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs capitalize" :class="statusClass(order.status)">{{ order.status }}</span></td>
                        <td class="px-4 py-3 text-sm"><Link :href="route('admin.custom-cakes.show', order.id)" class="text-brand-600 hover:text-brand-700">View</Link></td>
                    </tr>
                </tbody>
            </table>
            <div v-if="orders.links && orders.links.length > 1" class="p-4 flex justify-center gap-2">
                <Link v-for="(link, i) in orders.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-brand-600 text-white' : 'bg-white text-gray-700 border'" class="px-3 py-1.5 rounded-lg text-sm" v-html="link.label" :preserve-scroll="true"></Link>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ orders: Object, filters: Object });
const statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
const statusFilter = ref(props.filters?.status || '');
const doFilter = () => router.get(route('admin.custom-cakes.index'), { status: statusFilter.value }, { preserveState: true, preserveScroll: true });
const statusClass = (status) => ({ pending: 'bg-yellow-100 text-yellow-700', confirmed: 'bg-blue-100 text-blue-700', preparing: 'bg-purple-100 text-purple-700', ready: 'bg-indigo-100 text-indigo-700', delivered: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700' }[status] || 'bg-gray-100 text-gray-700');
</script>
