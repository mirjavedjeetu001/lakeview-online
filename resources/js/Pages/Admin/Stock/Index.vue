<template>
    <AdminLayout active-menu="stock" page-title="Stock Management">
        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Outlet inventory</p><h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Stock management</h2><p class="mt-2 text-sm text-brand-500">Keep every branch’s availability and current stock accurate from one place.</p></div><button type="button" @click="downloadStock" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-600"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>Download stock CSV</button></div>

            <form @submit.prevent="applyFilters" class="rounded-2xl border border-brand-100 bg-white p-4 shadow-soft sm:p-5"><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1.3fr_1.5fr_1.2fr_1.2fr_auto] lg:items-end"><label class="field-label">Branch<select v-model="filterForm.branch_id" class="field-input"><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}{{ !branch.is_active ? ' · hidden' : '' }}</option></select></label><label class="field-label">Search product<input v-model="filterForm.search" type="search" placeholder="Search by product name" class="field-input" /></label><label class="field-label">Category<select v-model="filterForm.category_id" class="field-input"><option value="">All categories</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select></label><label class="field-label">Stock status<select v-model="filterForm.stock_status" class="field-input"><option value="">All stock</option><option value="out">Out of stock</option><option value="low">Low stock (1–{{ lowStockLimit }})</option><option value="healthy">Healthy / unlimited</option></select></label><button type="submit" class="h-[43px] rounded-xl bg-brand-100 px-5 text-sm font-bold text-brand-700 hover:bg-brand-200">Apply</button></div></form>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-5"><div v-for="card in summaryCards" :key="card.label" class="rounded-2xl border border-brand-100 bg-white p-4 shadow-soft"><p class="text-xs font-bold uppercase tracking-wide text-brand-400">{{ card.label }}</p><p class="mt-2 text-2xl font-bold" :class="card.tone || 'text-brand-900'">{{ card.value }}</p></div></div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft"><div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-400">{{ selectedBranchName }}</p><h3 class="mt-1 font-serif text-xl font-bold text-brand-900">Branch stock list</h3></div><span class="text-xs text-brand-400">Blank stock means unlimited</span></div><div class="overflow-x-auto"><table class="w-full min-w-[880px]"><thead class="bg-brand-50"><tr><th class="table-head">Product</th><th class="table-head">Category</th><th class="table-head">Current stock</th><th class="table-head">Status</th><th class="table-head">Live for customers</th><th class="table-head text-right">Action</th></tr></thead><tbody class="divide-y divide-brand-100"><tr v-for="row in stockRows" :key="row.id" class="hover:bg-cream-50"><td class="table-cell"><div class="flex items-center gap-3"><div class="h-11 w-11 shrink-0 overflow-hidden rounded-xl bg-brand-50"><img v-if="row.image" :src="assetUrl(row.image)" class="h-full w-full object-cover" /><div v-else class="flex h-full items-center justify-center text-brand-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" d="m4 16 4.5-5 3 3 2.5-3 6 7M5 19h14a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/></svg></div></div><div class="min-w-0"><p class="truncate font-semibold text-brand-900">{{ row.name }}</p><p class="text-xs text-brand-400">#{{ row.id }}</p></div></div></td><td class="table-cell text-brand-500">{{ row.category }}</td><td class="table-cell"><input v-model="row.stock" type="number" min="0" placeholder="Unlimited" class="w-32 rounded-xl border border-brand-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" /></td><td class="table-cell"><span class="rounded-full px-2.5 py-1 text-[11px] font-bold" :class="statusClass(row)">{{ stockLabel(row) }}</span></td><td class="table-cell"><label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-brand-700"><input v-model="row.is_available" type="checkbox" class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200" /> Available</label></td><td class="table-cell text-right"><button type="button" @click="saveRow(row)" :disabled="savingId === row.id" class="rounded-full bg-brand-100 px-4 py-2 text-xs font-bold text-brand-700 hover:bg-brand-200 disabled:opacity-60">{{ savingId === row.id ? 'Saving…' : 'Save' }}</button></td></tr><tr v-if="!stockRows.length"><td colspan="6" class="p-14 text-center text-sm text-brand-400">No products match this stock filter.</td></tr></tbody></table></div></section>
            <p class="text-xs leading-6 text-brand-400">Tip: set a number to track branch stock. Set it to 0 to hide the item as out of stock, or leave it blank for unlimited stock. Product assignment and branch pricing remain available under Products.</p>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({ branches: Array, categories: Array, products: Array, summary: Object, filters: Object, lowStockLimit: Number });
const filterForm = reactive({ branch_id: props.filters?.branch_id || '', search: props.filters?.search || '', category_id: props.filters?.category_id || '', stock_status: props.filters?.stock_status || '' });
const stockRows = ref([]);
const savingId = ref(null);
const lowStockLimit = computed(() => props.lowStockLimit || 5);
const summary = computed(() => props.summary || {});
const selectedBranchName = computed(() => props.branches?.find(branch => Number(branch.id) === Number(filterForm.branch_id))?.name || 'Selected branch');
const summaryCards = computed(() => [
    { label: 'Assigned products', value: summary.value.assigned || 0 },
    { label: 'Live products', value: summary.value.live || 0, tone: 'text-sage-700' },
    { label: 'Tracked units', value: summary.value.units || 0 },
    { label: 'Low stock', value: summary.value.low || 0, tone: 'text-amber-600' },
    { label: 'Out of stock', value: summary.value.out || 0, tone: 'text-red-600' },
]);

watch(() => props.products, (products) => { stockRows.value = (products || []).map(row => ({ ...row })); }, { immediate: true });
const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const statusClass = (row) => { const stock = row.stock === '' || row.stock === null ? null : Number(row.stock); return stock === null ? 'bg-brand-50 text-brand-600' : stock === 0 ? 'bg-red-50 text-red-600' : stock <= lowStockLimit.value ? 'bg-amber-50 text-amber-700' : 'bg-sage-50 text-sage-700'; };
const stockLabel = (row) => { const stock = row.stock === '' || row.stock === null ? null : Number(row.stock); return stock === null ? 'Unlimited' : stock === 0 ? 'Out of stock' : stock <= lowStockLimit.value ? 'Low stock' : 'Healthy'; };
const applyFilters = () => router.get(route('admin.stock.index'), { ...filterForm }, { preserveState: true, preserveScroll: true });
const saveRow = (row) => { savingId.value = row.id; router.patch(route('admin.stock.update', row.id), { branch_id: filterForm.branch_id, stock: row.stock === '' ? null : row.stock, is_available: row.is_available ? 1 : 0 }, { preserveScroll: true, onFinish: () => savingId.value = null }); };
const downloadStock = () => { window.location.href = route('admin.stock.export', { ...filterForm }); };
</script>
