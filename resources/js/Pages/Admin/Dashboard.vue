<template>
    <AdminLayout active-menu="dashboard" page-title="Dashboard">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Business overview</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Good morning, {{ $page.props.auth?.user?.name || 'Admin' }}</h2>
                    <p class="mt-2 text-sm text-brand-500">A live view of your bakery sales, outlets and inventory.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('admin.reports.index')" class="rounded-full border border-brand-200 bg-white px-4 py-2.5 text-sm font-bold text-brand-700 hover:border-brand-400">View reports</Link>
                    <Link :href="route('admin.stock.index')" class="rounded-full bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-600">Manage stock</Link>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div v-for="card in statCards" :key="card.label" class="rounded-2xl border border-brand-100 bg-white p-4 shadow-soft sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-xs font-bold uppercase tracking-wide text-brand-400">{{ card.label }}</p><p class="mt-2 text-xl font-bold text-brand-900 sm:text-2xl">{{ card.value }}</p></div>
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :d="card.icon"/></svg></span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
                <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-soft sm:p-6">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Last 30 days</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">Sales performance</h3></div><span class="rounded-full bg-sage-50 px-3 py-1.5 text-xs font-bold text-sage-700">Delivered sales</span></div>
                    <div v-if="salesByDay.length" class="mt-8 flex h-56 items-end gap-1.5 sm:gap-2">
                        <div v-for="day in salesByDay" :key="day.date" class="group flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                            <div class="relative flex h-44 w-full items-end justify-center"><div class="w-full max-w-5 rounded-t-lg bg-gradient-to-t from-brand-700 to-gold-400 transition-all group-hover:from-brand-600" :style="{ height: barHeight(day.sales) + '%' }" :title="money(day.sales) + ' · ' + day.orders + ' orders'"></div></div>
                            <span class="hidden truncate text-[10px] text-brand-400 sm:block">{{ day.label }}</span>
                        </div>
                    </div>
                    <div v-else class="flex h-56 items-center justify-center text-sm text-brand-400">No sales in this period.</div>
                    <div class="mt-5 flex items-center justify-between border-t border-brand-100 pt-4 text-xs text-brand-400"><span>Daily revenue trend</span><span>{{ money(stats.period_sales) }} total</span></div>
                </section>

                <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-soft sm:p-6">
                    <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">By outlet</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">Branch sales</h3></div><Link :href="route('admin.branches.index')" class="text-xs font-bold text-brand-600">Manage</Link></div>
                    <div v-if="branchSales.length" class="mt-6 space-y-5"><div v-for="branch in branchSales.slice(0, 5)" :key="branch.branch" class="space-y-2"><div class="flex items-center justify-between gap-3 text-sm"><span class="truncate font-semibold text-brand-800">{{ branch.branch }}</span><span class="shrink-0 font-bold text-brand-700">{{ money(branch.sales) }}</span></div><div class="h-2 overflow-hidden rounded-full bg-brand-50"><div class="h-full rounded-full bg-gold-400" :style="{ width: branchWidth(branch.sales) + '%' }"></div></div><p class="text-[11px] text-brand-400">{{ branch.orders }} completed order{{ branch.orders === 1 ? '' : 's' }}</p></div></div>
                    <div v-else class="py-12 text-center text-sm text-brand-400">No branch sales yet.</div>
                </section>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft">
                    <div class="flex items-center justify-between border-b border-brand-100 p-5"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Best performers</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Top selling products</h3></div><Link :href="route('admin.reports.index')" class="text-xs font-bold text-brand-600">Full report</Link></div>
                    <div v-if="topProducts.length" class="divide-y divide-brand-100"><div v-for="(product, index) in topProducts" :key="product.name" class="flex items-center gap-3 px-5 py-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">{{ index + 1 }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-brand-800">{{ product.name }}</p><p class="text-xs text-brand-400">{{ product.units }} units sold</p></div><span class="text-sm font-bold text-brand-700">{{ money(product.sales) }}</span></div></div>
                    <div v-else class="p-8 text-center text-sm text-brand-400">No product sales yet.</div>
                </section>

                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft">
                    <div class="flex items-center justify-between border-b border-brand-100 p-5"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Inventory watch</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Low stock alerts</h3></div><Link :href="route('admin.stock.index', { stock_status: 'low' })" class="text-xs font-bold text-brand-600">Open stock</Link></div>
                    <div v-if="lowStockProducts.length" class="divide-y divide-brand-100"><div v-for="product in lowStockProducts" :key="product.id + '-' + product.branch_name" class="flex items-center gap-3 px-5 py-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" :class="product.stock === 0 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2 1 21h22L12 2Zm0 5.2 6.7 11.6H5.3L12 7.2ZM11 10v4h2v-4h-2Zm0 5v2h2v-2h-2Z"/></svg></span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-brand-800">{{ product.name }}</p><p class="truncate text-xs text-brand-400">{{ product.branch_name }}</p></div><span class="rounded-full px-2.5 py-1 text-xs font-bold" :class="product.stock === 0 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-700'">{{ product.stock === 0 ? 'Out' : product.stock + ' left' }}</span></div></div>
                    <div v-else class="p-8 text-center text-sm text-sage-700">All tracked products are healthy.</div>
                </section>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="flex items-center justify-between border-b border-brand-100 p-5"><h3 class="font-serif text-xl font-bold text-brand-900">Recent orders</h3><Link :href="route('admin.orders.index')" class="text-xs font-bold text-brand-600">View all</Link></div><div v-if="recentOrders.length" class="divide-y divide-brand-100"><div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between gap-3 px-5 py-3"><div class="min-w-0"><p class="truncate text-sm font-semibold text-brand-800">{{ order.order_number }}</p><p class="truncate text-xs text-brand-400">{{ order.customer_name }} · {{ order.branch?.name || 'Main branch' }}</p></div><div class="shrink-0 text-right"><p class="text-sm font-bold text-brand-700">{{ money(order.total) }}</p><span class="text-[10px] font-bold uppercase" :class="statusClass(order.status)">{{ order.status.replace(/_/g, ' ') }}</span></div></div></div><div v-else class="p-8 text-center text-sm text-brand-400">No orders yet.</div></section>
                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="flex items-center justify-between border-b border-brand-100 p-5"><h3 class="font-serif text-xl font-bold text-brand-900">Custom cake requests</h3><Link :href="route('admin.custom-cakes.index')" class="text-xs font-bold text-brand-600">View all</Link></div><div v-if="recentCustomCakes.length" class="divide-y divide-brand-100"><div v-for="cake in recentCustomCakes" :key="cake.id" class="flex items-center justify-between gap-3 px-5 py-3"><div class="min-w-0"><p class="truncate text-sm font-semibold text-brand-800">{{ cake.order_number }}</p><p class="truncate text-xs text-brand-400">{{ cake.customer_name }} · {{ cake.branch?.name || 'Main branch' }}</p></div><span class="shrink-0 text-[10px] font-bold uppercase" :class="statusClass(cake.status)">{{ cake.status }}</span></div></div><div v-else class="p-8 text-center text-sm text-brand-400">No cake requests yet.</div></section>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    recentCustomCakes: Array,
    salesByDay: Array,
    branchSales: Array,
    topProducts: Array,
    lowStockProducts: Array,
});

const stats = computed(() => props.stats || {});
const salesByDay = computed(() => props.salesByDay || []);
const branchSales = computed(() => props.branchSales || []);
const topProducts = computed(() => props.topProducts || []);
const lowStockProducts = computed(() => props.lowStockProducts || []);
const maxDaySales = computed(() => Math.max(...salesByDay.value.map(day => Number(day.sales || 0)), 1));
const maxBranchSales = computed(() => Math.max(...branchSales.value.map(branch => Number(branch.sales || 0)), 1));

const money = (value) => '৳' + Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const barHeight = (value) => Math.max(Number(value || 0) ? (Number(value || 0) / maxDaySales.value) * 100 : 3, 3);
const branchWidth = (value) => Math.max((Number(value || 0) / maxBranchSales.value) * 100, 4);
const statusClass = (status) => ({ pending: 'text-yellow-600', confirmed: 'text-blue-600', preparing: 'text-purple-600', ready: 'text-indigo-600', out_for_delivery: 'text-orange-600', delivered: 'text-green-600', cancelled: 'text-red-600' }[status] || 'text-brand-500');

const statCards = computed(() => [
    { label: '30-day sales', value: money(stats.value.period_sales), icon: 'M3 12h18M12 3v18M5 7h14M5 17h14' },
    { label: 'Completed orders', value: stats.value.period_orders || 0, icon: 'M6 3h12l3 4v14H3V7l3-4Zm0 0v4h12V3M8 12h8M8 16h5' },
    { label: 'Average order', value: money(stats.value.average_order), icon: 'M12 8c-2.2 0-4 .9-4 2s1.8 2 4 2 4 .9 4 2-1.8 2-4 2m0-10V5m0 14v-3M5 5l14 14' },
    { label: 'Pending orders', value: stats.value.pending_orders || 0, icon: 'M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' },
]);
</script>
