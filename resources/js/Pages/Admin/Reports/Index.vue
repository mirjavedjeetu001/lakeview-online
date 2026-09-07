<template>
    <AdminLayout active-menu="reports" page-title="Sales Reports">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Insights & exports</p><h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Sales reports</h2><p class="mt-2 text-sm text-brand-500">Understand what is selling, where it is selling and how every outlet is performing.</p></div>
                <button type="button" @click="downloadReport" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>Download CSV</button>
            </div>

            <form @submit.prevent="applyFilters" class="rounded-2xl border border-brand-100 bg-white p-4 shadow-soft sm:p-5"><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1.2fr_1.2fr_auto] lg:items-end"><label class="field-label">From<input v-model="filterForm.from" type="date" class="field-input" /></label><label class="field-label">To<input v-model="filterForm.to" type="date" class="field-input" /></label><label class="field-label">Branch<select v-model="filterForm.branch_id" class="field-input"><option value="">All branches</option><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select></label><label class="field-label">Order status<select v-model="filterForm.status" class="field-input"><option value="delivered">Completed / delivered</option><option value="all">All except cancelled</option><option v-for="status in statuses.filter(item => item !== 'delivered')" :key="status" :value="status">{{ label(status) }}</option></select></label><button type="submit" class="h-[43px] rounded-xl bg-brand-100 px-5 text-sm font-bold text-brand-700 hover:bg-brand-200">Apply</button></div><p class="mt-3 text-xs text-brand-400">Sales cards include regular orders and custom cake orders matching the selected period and status.</p></form>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div v-for="card in summaryCards" :key="card.label" class="rounded-2xl border border-brand-100 bg-white p-4 shadow-soft sm:p-5"><p class="text-xs font-bold uppercase tracking-wide text-brand-400">{{ card.label }}</p><p class="mt-2 text-2xl font-bold text-brand-900">{{ card.value }}</p><p class="mt-1 text-xs text-brand-400">{{ card.note }}</p></div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
                <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-soft sm:p-6"><div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Revenue timeline</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">Sales by day</h3></div><span class="rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-600">{{ filterForm.from }} → {{ filterForm.to }}</span></div><div v-if="salesByDay.length" class="mt-8 flex h-60 items-end gap-1 sm:gap-2"><div v-for="day in salesByDay" :key="day.date" class="group flex min-w-0 flex-1 flex-col items-center justify-end gap-2"><div class="relative flex h-48 w-full items-end justify-center"><div class="w-full max-w-6 rounded-t-lg bg-gradient-to-t from-brand-700 to-gold-400 transition-all group-hover:from-brand-600" :style="{ height: barHeight(day.sales) + '%' }" :title="money(day.sales) + ' · ' + day.orders + ' orders'"></div></div><span class="hidden truncate text-[10px] text-brand-400 sm:block">{{ day.label }}</span></div></div><div v-else class="flex h-60 items-center justify-center text-sm text-brand-400">No sales found for this period.</div></section>

                <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-soft sm:p-6"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Outlet comparison</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">Branch performance</h3></div><div v-if="branchSales.length" class="mt-6 space-y-5"><div v-for="branch in branchSales" :key="branch.branch" class="space-y-2"><div class="flex items-center justify-between gap-3 text-sm"><span class="truncate font-semibold text-brand-800">{{ branch.branch }}</span><span class="shrink-0 font-bold text-brand-700">{{ money(branch.sales) }}</span></div><div class="h-2 overflow-hidden rounded-full bg-brand-50"><div class="h-full rounded-full bg-gold-400" :style="{ width: branchWidth(branch.sales) + '%' }"></div></div><p class="text-[11px] text-brand-400">{{ branch.orders }} order{{ branch.orders === 1 ? '' : 's' }}</p></div></div><div v-else class="py-12 text-center text-sm text-brand-400">No branch sales found.</div></section>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="border-b border-brand-100 p-5"><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Product demand</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Top selling products</h3></div><div v-if="topProducts.length" class="divide-y divide-brand-100"><div v-for="(product, index) in topProducts" :key="product.name" class="flex items-center gap-3 px-5 py-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">{{ index + 1 }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-brand-800">{{ product.name }}</p><p class="text-xs text-brand-400">{{ product.units }} units sold</p></div><span class="text-sm font-bold text-brand-700">{{ money(product.sales) }}</span></div></div><div v-else class="p-8 text-center text-sm text-brand-400">No product sales found.</div></section>
                <section class="rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="border-b border-brand-100 p-5"><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Order mix</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Regular vs custom cake</h3></div><div class="grid grid-cols-2 gap-4 p-5"><div class="rounded-2xl bg-brand-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-brand-400">Regular orders</p><p class="mt-2 text-2xl font-bold text-brand-900">{{ summary.regular_orders }}</p><p class="mt-1 text-sm font-bold text-brand-600">{{ money(summary.regular_sales) }}</p></div><div class="rounded-2xl bg-gold-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-brand-500">Custom cakes</p><p class="mt-2 text-2xl font-bold text-brand-900">{{ summary.cake_orders }}</p><p class="mt-1 text-sm font-bold text-gold-600">{{ money(summary.cake_sales) }}</p></div></div></section>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">Detailed ledger</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Sales transactions</h3></div><span class="text-xs text-brand-400">Showing latest {{ recentSales.length }} matching records</span></div><div class="overflow-x-auto"><table class="w-full min-w-[900px]"><thead class="bg-brand-50"><tr><th class="table-head">Order</th><th class="table-head">Date</th><th class="table-head">Customer</th><th class="table-head">Branch</th><th class="table-head">Type</th><th class="table-head">Status</th><th class="table-head text-right">Total</th></tr></thead><tbody class="divide-y divide-brand-100"><tr v-for="row in recentSales" :key="row.type + row.order_number" class="hover:bg-cream-50"><td class="table-cell font-semibold text-brand-800">{{ row.order_number }}</td><td class="table-cell text-brand-500">{{ formatDate(row.date) }}</td><td class="table-cell text-brand-500">{{ row.customer }}</td><td class="table-cell text-brand-500">{{ row.branch }}</td><td class="table-cell"><span class="rounded-full px-2.5 py-1 text-[11px] font-bold" :class="row.type === 'Custom cake' ? 'bg-gold-50 text-gold-600' : 'bg-brand-50 text-brand-600'">{{ row.type }}</span></td><td class="table-cell"><span class="text-xs font-bold capitalize" :class="statusClass(row.status)">{{ label(row.status) }}</span></td><td class="table-cell text-right font-bold text-brand-700">{{ money(row.total) }}</td></tr><tr v-if="!recentSales.length"><td colspan="7" class="p-12 text-center text-sm text-brand-400">No sales transactions found.</td></tr></tbody></table></div></section>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({ summary: Object, salesByDay: Array, branchSales: Array, topProducts: Array, recentSales: Array, branches: Array, filters: Object, statuses: Array });
const summary = computed(() => props.summary || {});
const salesByDay = computed(() => props.salesByDay || []);
const branchSales = computed(() => props.branchSales || []);
const topProducts = computed(() => props.topProducts || []);
const recentSales = computed(() => props.recentSales || []);
const filterForm = reactive({ from: props.filters?.from || '', to: props.filters?.to || '', branch_id: props.filters?.branch_id || '', status: props.filters?.status || 'delivered' });
const maxDaySales = computed(() => Math.max(...salesByDay.value.map(day => Number(day.sales || 0)), 1));
const maxBranchSales = computed(() => Math.max(...branchSales.value.map(branch => Number(branch.sales || 0)), 1));

const money = (value) => '৳' + Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const label = (value) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
const formatDate = (value) => new Date(value + 'T00:00:00').toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' });
const barHeight = (value) => Math.max(Number(value || 0) ? (Number(value || 0) / maxDaySales.value) * 100 : 3, 3);
const branchWidth = (value) => Math.max((Number(value || 0) / maxBranchSales.value) * 100, 4);
const statusClass = (status) => ({ pending: 'text-yellow-600', confirmed: 'text-blue-600', preparing: 'text-purple-600', ready: 'text-indigo-600', out_for_delivery: 'text-orange-600', delivered: 'text-green-600', cancelled: 'text-red-600' }[status] || 'text-brand-500');
const summaryCards = computed(() => [
    { label: 'Total sales', value: money(summary.value.sales), note: 'Regular + custom cake' },
    { label: 'Orders', value: summary.value.orders || 0, note: `${summary.value.regular_orders || 0} regular · ${summary.value.cake_orders || 0} cakes` },
    { label: 'Average order', value: money(summary.value.average_order), note: 'Per matching transaction' },
    { label: 'Discounts', value: money(summary.value.discounts), note: 'Regular order discounts' },
]);
const applyFilters = () => router.get(route('admin.reports.index'), { ...filterForm }, { preserveState: true, preserveScroll: true });
const downloadReport = () => { window.location.href = route('admin.reports.export', { ...filterForm }); };
</script>
