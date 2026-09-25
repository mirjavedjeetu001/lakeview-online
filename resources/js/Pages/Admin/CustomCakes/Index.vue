<template>
    <AdminLayout active-menu="custom-cakes" page-title="Custom Cake Orders">
        <div class="mx-auto max-w-6xl space-y-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Made just for you</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Custom cake orders</h2>
                    <p class="mt-2 text-sm text-brand-500">Review each request, cake details and requested delivery date.</p>
                </div>
                <label class="field-label w-full sm:max-w-56">Filter by status
                    <select v-model="statusFilter" class="field-input" @change="doFilter">
                        <option value="">All statuses</option>
                        <option v-for="s in statuses" :key="s" :value="s">{{ s.replace(/_/g, ' ') }}</option>
                    </select>
                </label>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
                <div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-900">Cake request list</h3>
                        <p class="mt-1 text-sm text-brand-400">Use View to review a request and update its progress.</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gold-50 px-3 py-1.5 text-xs font-bold text-gold-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                        {{ orders.total || orders.data.length }} {{ (orders.total || orders.data.length) === 1 ? 'order' : 'orders' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px]">
                        <thead class="bg-brand-50">
                            <tr>
                                <th class="table-head">Order</th>
                                <th class="table-head">Customer</th>
                                <th class="table-head">Cake details</th>
                                <th class="table-head">Delivery date</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="order in orders.data" :key="order.id" class="transition hover:bg-cream-50/80">
                                <td class="table-cell"><span class="inline-flex rounded-lg border border-brand-200 bg-brand-50 px-3 py-1.5 font-mono text-xs font-bold text-brand-800">{{ order.order_number }}</span></td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-100 font-serif text-lg font-bold text-brand-700">{{ order.customer_name?.charAt(0)?.toUpperCase() }}</span>
                                        <div class="min-w-0"><p class="truncate font-semibold text-brand-900">{{ order.customer_name }}</p><a :href="'tel:' + order.customer_phone" class="mt-0.5 block text-xs text-brand-500 transition hover:text-gold-600">{{ order.customer_phone }}</a></div>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <p class="font-semibold text-brand-800">{{ order.cake_type || 'Custom cake' }}</p>
                                    <p class="mt-1 text-xs text-brand-400">{{ [order.cake_size, order.cake_flavor].filter(Boolean).join(' · ') || 'Details not provided' }}</p>
                                </td>
                                <td class="table-cell">
                                    <span class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-cream-50 px-3 py-2 text-sm font-semibold text-brand-700">
                                        <svg class="h-4 w-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z" /></svg>
                                        {{ order.delivery_date || 'Not set' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold capitalize" :class="statusClass(order.status)">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(order.status)"></span>{{ order.status.replace(/_/g, ' ') }}
                                    </span>
                                </td>
                                <td class="table-cell whitespace-nowrap"><Link :href="route('admin.custom-cakes.show', order.id)" class="text-brand-600">View</Link></td>
                            </tr>
                            <tr v-if="!orders.data.length">
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 15.5c-.5 0-1 .15-1.5.5a2.7 2.7 0 0 1-3 0 2.7 2.7 0 0 0-3 0 2.7 2.7 0 0 1-3 0 2.7 2.7 0 0 0-3 0 2.7 2.7 0 0 1-3 0A2.7 2.7 0 0 0 2 15.5M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v7h18Z" /></svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-brand-800">No custom cake orders found</p>
                                    <p class="mt-1 text-sm text-brand-400">Try another status filter or check back later.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="orders.links && orders.links.length > 1" class="flex justify-center gap-2 border-t border-brand-100 p-4">
                    <Link v-for="(link, i) in orders.links" :key="i" :href="link.url || '#'" :class="link.active ? 'border-brand-700 bg-brand-700 text-white shadow-sm' : 'border-brand-200 bg-white text-brand-700 hover:border-brand-300 hover:bg-brand-50'" class="rounded-xl border px-3 py-2 text-sm font-bold transition" v-html="link.label" :preserve-scroll="true"></Link>
                </div>
            </section>
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
const statusClass = (status) => ({ pending: 'border-gold-200 bg-gold-50 text-gold-700', confirmed: 'border-brand-200 bg-brand-50 text-brand-700', preparing: 'border-purple-200 bg-purple-50 text-purple-700', ready: 'border-sage-200 bg-sage-50 text-sage-700', delivered: 'border-green-200 bg-green-50 text-green-700', cancelled: 'border-red-200 bg-red-50 text-red-700' }[status] || 'border-gray-200 bg-gray-50 text-gray-700');
const statusDotClass = (status) => ({ pending: 'bg-gold-500', confirmed: 'bg-brand-500', preparing: 'bg-purple-500', ready: 'bg-sage-600', delivered: 'bg-green-500', cancelled: 'bg-red-500' }[status] || 'bg-gray-500');
</script>
