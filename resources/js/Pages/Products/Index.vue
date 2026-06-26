<template>
    <CustomerLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Shop</span>
                <h1 class="font-serif text-3xl font-bold text-brand-900 mt-2">All Products</h1>
                <div class="w-20 h-1 bg-gold-500 mt-4 rounded-full"></div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-5 h-5 text-brand-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input v-model="search" type="text" placeholder="Search products..." class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 pl-12 pr-4 py-3 text-brand-900 transition" @input="debouncedSearch" />
                    </div>
                </div>
                <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                    <button @click="selectCategory('')" :class="!filters.category ? 'bg-gold-500 text-brand-950' : 'bg-white text-brand-700 border-2 border-brand-100'" class="px-4 py-2.5 rounded-xl text-sm font-medium whitespace-nowrap transition">All</button>
                    <button v-for="cat in categories" :key="cat.id" @click="selectCategory(cat.slug)" :class="filters.category === cat.slug ? 'bg-gold-500 text-brand-950' : 'bg-white text-brand-700 border-2 border-brand-100'" class="px-4 py-2.5 rounded-xl text-sm font-medium whitespace-nowrap transition">{{ cat.name }}</button>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-if="products.data.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                <div v-for="product in products.data" :key="product.id" class="bg-white rounded-2xl card-shadow card-shadow-hover transition overflow-hidden group">
                    <Link :href="route('products.show', product.slug)">
                        <div class="aspect-square bg-brand-50 flex items-center justify-center overflow-hidden relative">
                            <img v-if="product.image" :src="'/storage/' + product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                            <div v-else class="text-5xl text-brand-200 group-hover:scale-110 transition duration-300">{{ categoryEmoji(product.category?.name) }}</div>
                            <span v-if="product.discount_price" class="absolute top-3 left-3 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold">SALE</span>
                        </div>
                    </Link>
                    <div class="p-4">
                        <Link :href="route('products.show', product.slug)">
                            <h3 class="font-medium text-brand-900 text-sm mb-1 line-clamp-1 group-hover:text-gold-600 transition">{{ product.name }}</h3>
                        </Link>
                        <div class="text-xs text-brand-400 mb-3">{{ product.category?.name }}</div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-brand-700 font-bold text-lg font-serif">৳{{ product.effective_price }}</span>
                                <span v-if="product.discount_price" class="text-xs text-brand-300 line-through ml-1">৳{{ product.price }}</span>
                            </div>
                            <button @click="addToCart(product)" class="bg-brand-700 text-white px-3 py-2 rounded-lg text-sm hover:bg-gold-500 transition font-medium">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-16">
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-brand-500 text-lg">No products found.</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.links && products.links.length > 1" class="mt-8 flex justify-center gap-2">
                <Link v-for="(link, i) in products.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-gold-500 text-brand-950' : 'bg-white text-brand-700 border-2 border-brand-100'" class="px-4 py-2 rounded-xl text-sm transition" v-html="link.label" :preserve-scroll="true"></Link>
            </div>

            <!-- Add to Cart Toast -->
            <Transition name="toast">
                <div v-if="toast.show" class="fixed bottom-6 right-6 z-[100] bg-white rounded-2xl shadow-2xl border-2 border-gold-400 p-4 flex items-center gap-4 max-w-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-brand-900 text-sm">{{ toast.message }}</div>
                        <div class="text-xs text-brand-500">{{ toast.product?.name }} - ৳{{ toast.product?.effective_price }}</div>
                    </div>
                    <Link :href="route('checkout.index')" class="ml-2 bg-gold-500 text-brand-950 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gold-400 transition whitespace-nowrap">View Cart</Link>
                </div>
            </Transition>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ products: Object, categories: Array, filters: Object });

const search = ref(props.filters?.search || '');
let debounceTimer = null;

const categoryEmoji = (name) => {
    const map = { 'Cake': '🎂', 'Bread': '🍞', 'Cookies': '🍪', 'Sweets': '🍬', 'Fast Food': '🍔', 'Toast': '🥪', 'Dessert': '🍮', 'Order Cake': '🎂' };
    return map[name] || '🍰';
};

const toast = ref({ show: false, message: '', product: null });
let toastTimer = null;
const showToast = (product) => {
    toast.value = { show: true, message: 'Added to cart!', product };
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toast.value.show = false; }, 3000);
};

const debouncedSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('products.index'), { search: search.value, category: props.filters?.category }, { preserveState: true, preserveScroll: true });
    }, 300);
};

const selectCategory = (slug) => {
    router.get(route('products.index'), { category: slug, search: search.value }, { preserveState: true, preserveScroll: true });
};

const addToCart = (product) => {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch { cart = []; }
    const existing = cart.find(item => item.product_id === product.id);
    if (existing) { existing.quantity += 1; }
    else { cart.push({ product_id: product.id, name: product.name, price: product.effective_price, quantity: 1 }); }
    localStorage.setItem('cart', JSON.stringify(cart));
    window.dispatchEvent(new Event('cart-updated'));
    showToast(product);
};
</script>
