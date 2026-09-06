<template>
    <CustomerLayout>
        <section class="bg-cream-100 border-b border-brand-100"><div class="section-shell py-12 sm:py-16"><p class="eyebrow">Shopping from {{ selectedBranch?.name }}</p><div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5"><div><h1 class="section-title">The bakery shop</h1><p class="section-copy max-w-xl">Freshly made favourites, available at your selected outlet.</p></div><div class="rounded-2xl bg-white border border-brand-100 px-4 py-3 text-sm text-brand-600 shadow-soft"><span class="text-brand-400">Outlet</span><span class="mx-2 text-brand-300">/</span><strong>{{ selectedBranch?.name }}</strong></div></div></div></section>
        <section class="section-shell pt-8 sm:pt-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="relative w-full lg:max-w-sm"><svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg><input v-model="search" @input="debouncedSearch" type="search" placeholder="Search the bakery..." class="w-full rounded-full border border-brand-200 bg-white py-3 pl-11 pr-4 text-sm text-brand-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100" /></div>
                <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-1"><button @click="selectCategory('')" :class="!filters.category ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-brand-700 border-brand-200'" class="rounded-full border px-4 py-2 text-xs font-bold whitespace-nowrap transition">Everything</button><button v-for="cat in categories" :key="cat.id" @click="selectCategory(cat.slug)" :class="filters.category === cat.slug ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-brand-700 border-brand-200'" class="rounded-full border px-4 py-2 text-xs font-bold whitespace-nowrap transition">{{ cat.name }}</button></div>
            </div>
            <div v-if="products.data?.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6"><ProductCard v-for="product in products.data" :key="product.id" :product="product" @add="addToCart" /></div>
            <div v-else class="rounded-[2rem] border border-brand-200 bg-white px-6 py-20 text-center shadow-soft"><div class="text-6xl">🍰</div><h2 class="font-serif text-2xl font-bold text-brand-900 mt-4">Nothing on the counter</h2><p class="text-sm text-brand-500 mt-2">Try a different search or category for this outlet.</p></div>
            <div v-if="products.links?.length > 1" class="mt-10 flex justify-center gap-2"><Link v-for="(link, i) in products.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-brand-700 border-brand-200'" class="rounded-full border px-3.5 py-2 text-xs font-bold" v-html="link.label" :preserve-scroll="true" /></div>
        </section>
        <Transition name="toast"><div v-if="toast.show" class="fixed bottom-6 right-4 z-[80] max-w-sm rounded-2xl bg-white border border-gold-300 shadow-card p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-sage-50 text-sage-600 flex items-center justify-center">✓</span><div class="min-w-0"><div class="font-bold text-sm text-brand-900">Added to your bag</div><div class="text-xs text-brand-500 truncate">{{ toast.product?.name }}</div></div><Link :href="route('checkout.index')" class="ml-auto btn-mini">View bag</Link></div></Transition>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ products: Object, categories: Array, filters: Object });
const page = usePage();
const selectedBranch = computed(() => page.props.selectedBranch || null);
const search = ref(props.filters?.search || '');
const filters = computed(() => props.filters || {});
const categories = computed(() => props.categories || []);
const toast = ref({ show: false, product: null });
let debounceTimer;
let toastTimer;
const debouncedSearch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => router.get(route('products.index'), { search: search.value, category: filters.value.category }, { preserveState: true, preserveScroll: true }), 350); };
const selectCategory = (category) => router.get(route('products.index'), { category, search: search.value }, { preserveState: true, preserveScroll: true });
const addToCart = (product) => { let cart = []; try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch {} const existing = cart.find(i => i.product_id === product.id); if (existing) existing.quantity += 1; else cart.push({ product_id: product.id, name: product.name, price: Number(product.effective_price), quantity: 1 }); localStorage.setItem('cart', JSON.stringify(cart)); window.dispatchEvent(new Event('cart-updated')); toast.value = { show: true, product }; clearTimeout(toastTimer); toastTimer = setTimeout(() => toast.value.show = false, 2800); };
</script>
