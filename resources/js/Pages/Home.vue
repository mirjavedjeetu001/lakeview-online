<template>
    <CustomerLayout>
        <section class="hero-stage relative overflow-hidden border-b border-brand-100">
            <div class="hero-grid absolute inset-0 pointer-events-none"></div>
            <div class="hero-orb hero-orb-one"></div><div class="hero-orb hero-orb-two"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
                <div class="grid lg:grid-cols-[.9fr_1.1fr] items-center gap-12 lg:gap-16">
                    <div class="relative z-10 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 rounded-full border border-gold-200 bg-white/70 px-3 py-2 text-[10px] font-bold uppercase tracking-[.18em] text-gold-700 shadow-soft"><span class="h-2 w-2 rounded-full bg-sage-400 animate-pulse"></span>Fresh from {{ selectedBranch?.name || 'our bakery' }}</div>
                        <h1 class="font-serif text-[clamp(3rem,7vw,6.2rem)] leading-[.9] tracking-[-.055em] font-bold text-brand-900 mt-6">Sweet moments,<br><span class="hero-title-accent">baked fresh.</span></h1>
                        <p class="max-w-lg mx-auto lg:mx-0 text-base sm:text-lg text-brand-600 leading-8 mt-7">{{ settings.hero_subtitle || 'From everyday breads to celebration cakes, discover something delicious for every kind of day.' }}</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mt-8"><Link :href="route('products.index')" class="btn-primary">Explore the bakery <span>→</span></Link><Link :href="route('custom-cake.index')" class="btn-outline">Make a custom cake</Link></div>
                        <div class="flex flex-wrap justify-center lg:justify-start gap-x-5 gap-y-2 mt-8 text-xs text-brand-500 font-medium"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-sage-400"></span>Freshly baked daily</span><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-gold-500"></span>Pickup & delivery</span><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-brand-400"></span>Local outlets</span></div>
                    </div>

                    <div class="hero-showcase relative min-h-[390px] sm:min-h-[500px] lg:min-h-[560px]">
                        <div class="hero-showcase-glow"></div>
                        <div class="hero-showcase-panel absolute inset-x-[8%] top-[8%] bottom-[5%] rounded-[2.5rem] border border-white/20 bg-brand-950 p-5 sm:p-7 shadow-2xl rotate-2 overflow-hidden"><div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-gold-500/20 blur-2xl"></div><div class="absolute -left-20 bottom-0 h-56 w-56 rounded-full bg-brand-500/30 blur-3xl"></div><div class="relative flex items-center justify-between text-cream-100"><div><p class="text-[10px] uppercase tracking-[.22em] text-gold-300">Lake View bakery</p><p class="mt-2 font-serif text-2xl sm:text-3xl font-bold">Made for your<br><span class="text-gold-300 italic">sweetest days.</span></p></div><div class="hero-lv-seal">LV</div></div><div class="relative mt-7 grid grid-cols-2 gap-3"><div v-for="(item, index) in showcaseProducts" :key="item?.id || index" class="hero-product-tile" :class="index === 0 ? 'hero-product-featured' : ''"><img v-if="heroImage(index)" :src="heroImage(index)" :alt="item?.name || 'Fresh bakery item'" :loading="index === 0 ? 'eager' : 'lazy'" /><div v-else class="hero-product-fallback"><span>{{ productMark(item, index) }}</span></div><div class="hero-product-caption"><span class="truncate">{{ item?.name || ['Fresh bakes', 'Celebration cakes', 'Local favourites', 'Sweet treats'][index] }}</span><small v-if="item">৳{{ Number(item.effective_price || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 }) }}</small></div></div></div><div class="relative mt-5 flex items-center justify-between rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-xs text-cream-100"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-sage-400"></span>Prepared fresh every day</span><span class="text-gold-300">Since 2023</span></div></div>
                        <div class="hero-floating-note absolute -left-1 bottom-[10%] rounded-2xl border border-brand-100 bg-white px-4 py-3 shadow-card"><div class="text-[10px] uppercase tracking-[.18em] text-brand-400">Shopping from</div><div class="mt-1 max-w-[150px] truncate text-sm font-bold text-brand-800">{{ selectedBranch?.name || 'Your local outlet' }}</div></div>
                        <div class="hero-floating-badge absolute right-[2%] top-[2%] flex h-20 w-20 rotate-12 items-center justify-center rounded-full border border-gold-200 bg-gold-100 text-center text-[10px] font-bold uppercase leading-4 tracking-[.12em] text-brand-800 shadow-card">Fresh<br>daily</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="overflow-hidden bg-brand-700 text-cream-50 border-y border-brand-600"><div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap justify-center gap-x-8 gap-y-1 text-xs sm:text-sm font-semibold tracking-wide"><span>Freshly baked daily ✦</span><span>Handcrafted with care ✦</span><span>Pickup & delivery ✦</span><span>Happiness in every bite</span></div></div>

        <section v-if="categories.length" class="section-shell"><div class="text-center max-w-xl mx-auto"><p class="eyebrow justify-center">Explore the bakery</p><h2 class="section-title">Something for every craving</h2><p class="section-copy">Browse our handcrafted selection, prepared for {{ selectedBranch?.name || 'your local outlet' }}.</p></div><div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4 mt-10"><Link v-for="cat in categories" :key="cat.id" :href="route('products.index', { category: cat.slug })" class="group rounded-2xl border border-brand-100 bg-white p-3 text-center shadow-soft hover:-translate-y-1 hover:shadow-card transition"><div class="aspect-square rounded-xl bg-brand-50 overflow-hidden flex items-center justify-center"><img v-if="cat.image" :src="assetUrl(cat.image)" :alt="cat.name" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition"/><span v-else class="text-3xl font-serif font-bold text-brand-400">{{ categoryMark(cat.name) }}</span></div><span class="block mt-3 text-xs sm:text-sm font-semibold text-brand-700 group-hover:text-brand-500">{{ cat.name }}</span></Link></div></section>

        <section v-if="bestSellingProducts.length" class="bg-cream-100 border-y border-brand-100"><div class="section-shell"><div class="flex items-end justify-between gap-5"><div><p class="eyebrow">Most loved</p><h2 class="section-title text-left">Our bestsellers</h2><p class="section-copy text-left">The bakes customers keep coming back for.</p></div><Link :href="route('products.index')" class="hidden sm:inline-flex text-sm font-bold text-brand-600 hover:text-brand-500">View all <span class="ml-2">→</span></Link></div><div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-10"><ProductCard v-for="product in bestSellingProducts" :key="product.id" :product="product" @add="addToCart" /></div><div class="text-center sm:hidden mt-8"><Link :href="route('products.index')" class="btn-outline">View all products</Link></div></div></section>

        <section v-if="allProducts.length" class="section-shell"><div class="flex items-end justify-between gap-5"><div><p class="eyebrow">Fresh counter</p><h2 class="section-title text-left">All products</h2><p class="section-copy text-left">Explore the full selection available at your outlet.</p></div><Link :href="route('products.index')" class="text-sm font-bold text-brand-600 hover:text-brand-500">Shop all <span class="ml-2">→</span></Link></div><div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-10"><ProductCard v-for="product in allProducts" :key="product.id" :product="product" @add="addToCart" /></div></section>

        <section class="section-shell"><div class="grid lg:grid-cols-2 gap-10 items-center"><div class="relative min-h-[330px] sm:min-h-[430px]"><div class="absolute inset-0 w-[72%] rounded-[3rem_0_3rem_0] overflow-hidden bg-brand-200"><img v-if="heroImage(3)" :src="heroImage(3)" loading="lazy" class="w-full h-full object-cover" alt="Lake View bakery"/><div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-700 to-brand-950 text-5xl font-serif font-bold text-gold-200">LV</div></div><div class="absolute right-0 bottom-0 w-[45%] h-[47%] rounded-[0_2rem_0_2rem] border-8 border-cream-50 overflow-hidden bg-gold-100 shadow-card"><img v-if="heroImage(4)" :src="heroImage(4)" loading="lazy" class="w-full h-full object-cover" alt="Lake View sweets"/><div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gold-300 to-brand-500 text-4xl font-serif font-bold text-brand-950">LV</div></div></div><div><p class="eyebrow">Made with intention</p><h2 class="section-title text-left">A little sweetness for every kind of day.</h2><p class="section-copy text-left mt-5">{{ settings.about_text || 'We believe the best memories often start with something warm from the oven. Our bakers prepare every bite with honest ingredients, time and a whole lot of love.' }}</p><div class="grid grid-cols-2 gap-3 mt-7"><div class="rounded-2xl bg-brand-50 border border-brand-100 p-4"><div class="font-serif text-2xl font-bold text-brand-600">7+</div><div class="text-xs text-brand-500 mt-1">local outlets</div></div><div class="rounded-2xl bg-gold-50 border border-gold-100 p-4"><div class="font-serif text-2xl font-bold text-brand-600">100+</div><div class="text-xs text-brand-500 mt-1">fresh products</div></div></div><Link :href="route('about')" class="inline-flex items-center gap-2 mt-7 text-sm font-bold text-brand-600 hover:text-brand-500">Our story <span>→</span></Link></div></div></section>

        <section class="bg-brand-950 text-cream-50"><div class="section-shell py-16 sm:py-20"><div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-5"><div><p class="eyebrow text-gold-300">Find us near you</p><h2 class="font-serif text-3xl sm:text-4xl font-bold mt-3">Our outlets</h2><p class="text-brand-200 mt-3 max-w-xl">Choose an outlet above and we’ll keep your shopping experience local.</p></div><Link :href="route('contact')" class="btn-light">See all locations</Link></div><div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10"><div v-for="branch in branches" :key="branch.id" class="rounded-2xl border border-brand-700 bg-brand-900/60 p-5"><div class="flex items-start gap-3"><span class="outlet-icon">⌖</span><div><h3 class="font-semibold text-cream-50">{{ branch.name }}</h3><p class="text-xs text-brand-200 mt-2 leading-5">{{ branch.address || 'Lake View Sweets & Bakery outlet' }}</p><a v-if="branch.phones?.[0]" :href="'tel:' + branch.phones[0]" class="inline-block mt-3 text-xs text-gold-300">{{ branch.phones[0] }}</a></div></div></div></div></div></section>

        <Transition name="toast"><div v-if="toast.show" class="fixed bottom-6 right-4 z-[80] max-w-sm rounded-2xl bg-white border border-gold-300 shadow-card p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-sage-50 text-sage-600 flex items-center justify-center">✓</span><div class="min-w-0"><div class="font-bold text-sm text-brand-900">Added to your bag</div><div class="text-xs text-brand-500 truncate">{{ toast.product?.name }}</div></div><Link :href="route('checkout.index')" class="ml-auto btn-mini">View bag</Link></div></Transition>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({ categories: Array, featuredProducts: Array, bestSellingProducts: Array, allProducts: Array, branches: Array, selectedBranch: Object });
const page = usePage();
const settings = computed(() => page.props.settings || {});
const selectedBranch = computed(() => page.props.selectedBranch || null);
const featuredProducts = computed(() => page.props.featuredProducts || []);
const bestSellingProducts = computed(() => page.props.bestSellingProducts || featuredProducts.value);
const allProducts = computed(() => page.props.allProducts || []);
const categories = computed(() => page.props.categories || []);
const branches = computed(() => page.props.branches || []);
const showcaseProducts = computed(() => [featuredProducts.value[0], featuredProducts.value[1], featuredProducts.value[2], featuredProducts.value[3]]);
const toast = ref({ show: false, product: null });
let toastTimer;

const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const fallbackHeroImages = ['/images/lakeview-hero.jpg', '/images/lakeview-sweets.jpg', '/images/lakeview-cake.jpg'];
const heroImages = computed(() => { try { const configured = JSON.parse(settings.value.hero_images || '[]').filter(Boolean); return configured.length ? configured : fallbackHeroImages; } catch { return fallbackHeroImages; } });
const heroImage = (index) => heroImages.value[index % heroImages.value.length] || (featuredProducts.value.length ? assetUrl(featuredProducts.value[index % featuredProducts.value.length]?.image) : '');
const productMark = (product, index) => product?.category?.name?.slice(0, 2).toUpperCase() || ['FB', 'CC', 'LF', 'LV'][index] || 'LV';
const categoryMark = (name) => ({ Cake: 'CK', Bread: 'BR', Cookies: 'CO', Sweets: 'SW', 'Fast Food': 'FF', Toast: 'TO', Dessert: 'DS', 'Order Cake': 'OC' }[name] || 'LV');
const addToCart = (product, amount = 1) => {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch { cart = []; }
    const existing = cart.find(item => item.product_id === product.id);
    if (existing) { existing.quantity += amount; existing.image = product.image || existing.image; existing.category_name = product.category?.name || existing.category_name || ''; existing.allow_pickup = product.allow_pickup !== false; existing.allow_home_delivery = product.allow_home_delivery !== false; }
    else cart.push({ product_id: product.id, name: product.name, category_name: product.category?.name || '', image: product.image || '', price: Number(product.effective_price), quantity: amount, allow_pickup: product.allow_pickup !== false, allow_home_delivery: product.allow_home_delivery !== false });
    localStorage.setItem('cart', JSON.stringify(cart)); window.dispatchEvent(new Event('cart-updated'));
    toast.value = { show: true, product };
    clearTimeout(toastTimer); toastTimer = window.setTimeout(() => { toast.value.show = false; }, 3200);
};
</script>
