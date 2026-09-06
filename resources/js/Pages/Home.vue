<template>
    <CustomerLayout>
        <section class="relative overflow-hidden bg-cream-100 border-b border-brand-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 lg:py-20">
                <div class="grid lg:grid-cols-[1fr_1.15fr] items-center gap-10 lg:gap-14">
                    <div class="relative z-10 text-center lg:text-left">
                        <p class="eyebrow justify-center lg:justify-start">Fresh from {{ selectedBranch?.name || 'our bakery' }}</p>
                        <h1 class="font-serif text-[clamp(2.8rem,7vw,5.8rem)] leading-[.94] tracking-[-.04em] font-bold text-brand-900 mt-5">Sweet moments,<br><span class="text-brand-500 italic">baked fresh.</span></h1>
                        <p class="max-w-lg mx-auto lg:mx-0 text-base sm:text-lg text-brand-600 leading-8 mt-6">{{ settings.hero_subtitle || 'From everyday breads to celebration cakes, discover something delicious for every kind of day.' }}</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mt-8">
                            <Link :href="route('products.index')" class="btn-primary">Explore the bakery <span>→</span></Link>
                            <Link :href="route('custom-cake.index')" class="btn-outline">Make a custom cake</Link>
                        </div>
                        <div class="flex flex-wrap justify-center lg:justify-start gap-5 mt-8 text-xs text-brand-500 font-medium">
                            <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-sage-400"></span>Freshly baked daily</span>
                            <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-gold-500"></span>Pickup & delivery</span>
                        </div>
                    </div>

                    <div class="relative min-h-[360px] sm:min-h-[500px] lg:min-h-[580px]">
                        <div class="absolute top-0 right-0 w-[54%] h-[58%] rounded-[2rem_2rem_2rem_5rem] overflow-hidden bg-brand-200 shadow-card rotate-2">
                            <img v-if="heroImage(0)" :src="heroImage(0)" class="w-full h-full object-cover" alt="Fresh bakery selection" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-brand-200 text-7xl">🥐</div>
                        </div>
                        <div class="absolute bottom-0 left-0 w-[50%] h-[48%] rounded-[2rem_5rem_2rem_2rem] overflow-hidden bg-gold-100 shadow-card -rotate-2">
                            <img v-if="heroImage(1)" :src="heroImage(1)" class="w-full h-full object-cover" alt="Lake View bakery product" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gold-100 text-7xl">🍰</div>
                        </div>
                        <div class="absolute left-[30%] top-[28%] w-[42%] h-[48%] rounded-[5rem_0_5rem_0] overflow-hidden border-[10px] border-cream-100 bg-brand-100 shadow-card z-10">
                            <img v-if="heroImage(2)" :src="heroImage(2)" class="w-full h-full object-cover" alt="Baked fresh at Lake View" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-brand-100 text-7xl">🍞</div>
                        </div>
                        <div class="absolute -bottom-1 right-[2%] z-20 rounded-2xl bg-white px-4 py-3 shadow-card border border-brand-100"><div class="text-[10px] uppercase tracking-[.18em] text-brand-500">Shopping from</div><div class="mt-1 max-w-[140px] truncate text-sm font-bold text-brand-800">{{ selectedBranch?.name }}</div></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="overflow-hidden bg-brand-700 text-cream-50 border-y border-brand-600"><div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap justify-center gap-x-8 gap-y-1 text-xs sm:text-sm font-semibold tracking-wide"><span>Freshly baked daily ✦</span><span>Handcrafted with care ✦</span><span>Pickup & delivery ✦</span><span>Happiness in every bite</span></div></div>

        <section class="section-shell">
            <div class="text-center max-w-xl mx-auto"><p class="eyebrow justify-center">Explore the bakery</p><h2 class="section-title">Something for every craving</h2><p class="section-copy">Browse our handcrafted selection, prepared for {{ selectedBranch?.name }}.</p></div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4 mt-10">
                <Link v-for="cat in categories" :key="cat.id" :href="route('products.index', { category: cat.slug })" class="group rounded-2xl border border-brand-100 bg-white p-3 text-center shadow-soft hover:-translate-y-1 hover:shadow-card transition">
                    <div class="aspect-square rounded-xl bg-brand-50 overflow-hidden flex items-center justify-center"><img v-if="cat.image" :src="assetUrl(cat.image)" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-105 transition"/><span v-else class="text-4xl group-hover:scale-110 transition">{{ categoryEmoji(cat.name) }}</span></div>
                    <span class="block mt-3 text-xs sm:text-sm font-semibold text-brand-700 group-hover:text-brand-500">{{ cat.name }}</span>
                </Link>
            </div>
        </section>

        <section class="bg-cream-100 border-y border-brand-100"><div class="section-shell"><div class="flex items-end justify-between gap-5"><div><p class="eyebrow">Most loved</p><h2 class="section-title text-left">Our bestsellers</h2><p class="section-copy text-left">The bakes customers keep coming back for.</p></div><Link :href="route('products.index')" class="hidden sm:inline-flex text-sm font-bold text-brand-600 hover:text-brand-500">View all <span class="ml-2">→</span></Link></div><div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-10"><ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" @add="addToCart" /></div><div class="text-center sm:hidden mt-8"><Link :href="route('products.index')" class="btn-outline">View all products</Link></div></div></section>

        <section class="section-shell"><div class="grid lg:grid-cols-2 gap-10 items-center"><div class="relative min-h-[330px] sm:min-h-[430px]"><div class="absolute inset-0 w-[72%] rounded-[3rem_0_3rem_0] overflow-hidden bg-brand-200"><img v-if="heroImage(3)" :src="heroImage(3)" class="w-full h-full object-cover" alt="Lake View bakery"/><div v-else class="w-full h-full flex items-center justify-center text-8xl">🧁</div></div><div class="absolute right-0 bottom-0 w-[45%] h-[47%] rounded-[0_2rem_0_2rem] border-8 border-cream-50 overflow-hidden bg-gold-100 shadow-card"><img v-if="heroImage(4)" :src="heroImage(4)" class="w-full h-full object-cover" alt="Lake View sweets"/><div v-else class="w-full h-full flex items-center justify-center text-6xl">🍪</div></div></div><div><p class="eyebrow">Made with intention</p><h2 class="section-title text-left">A little sweetness for every kind of day.</h2><p class="section-copy text-left mt-5">{{ settings.about_text || 'We believe the best memories often start with something warm from the oven. Our bakers prepare every bite with honest ingredients, time and a whole lot of love.' }}</p><div class="grid grid-cols-2 gap-3 mt-7"><div class="rounded-2xl bg-brand-50 border border-brand-100 p-4"><div class="font-serif text-2xl font-bold text-brand-600">7+</div><div class="text-xs text-brand-500 mt-1">local outlets</div></div><div class="rounded-2xl bg-gold-50 border border-gold-100 p-4"><div class="font-serif text-2xl font-bold text-brand-600">100+</div><div class="text-xs text-brand-500 mt-1">fresh products</div></div></div><Link :href="route('about')" class="inline-flex items-center gap-2 mt-7 text-sm font-bold text-brand-600 hover:text-brand-500">Our story <span>→</span></Link></div></div></section>

        <section class="bg-brand-950 text-cream-50"><div class="section-shell py-16 sm:py-20"><div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-5"><div><p class="eyebrow text-gold-300">Find us near you</p><h2 class="font-serif text-3xl sm:text-4xl font-bold mt-3">Our outlets</h2><p class="text-brand-200 mt-3 max-w-xl">Choose an outlet above and we’ll keep your shopping experience local.</p></div><Link :href="route('contact')" class="btn-light">See all locations</Link></div><div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10"><div v-for="branch in branches" :key="branch.id" class="rounded-2xl border border-brand-700 bg-brand-900/60 p-5"><div class="flex items-start gap-3"><span class="outlet-icon">⌖</span><div><h3 class="font-semibold text-cream-50">{{ branch.name }}</h3><p class="text-xs text-brand-200 mt-2 leading-5">{{ branch.address || 'Lake View Sweets & Bakery outlet' }}</p><a v-if="branch.phones?.[0]" :href="'tel:' + branch.phones[0]" class="inline-block mt-3 text-xs text-gold-300">{{ branch.phones[0] }}</a></div></div></div></div></div></section>

        <Transition name="toast"><div v-if="toast.show" class="fixed bottom-6 right-4 z-[80] max-w-sm rounded-2xl bg-white border border-gold-300 shadow-card p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-sage-50 text-sage-600 flex items-center justify-center">✓</span><div class="min-w-0"><div class="font-bold text-sm text-brand-900">Added to your bag</div><div class="text-xs text-brand-500 truncate">{{ toast.product?.name }}</div></div><Link :href="route('checkout.index')" class="ml-auto btn-mini">View bag</Link></div></Transition>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({ categories: Array, featuredProducts: Array, branches: Array, selectedBranch: Object });
const page = usePage();
const settings = computed(() => page.props.settings || {});
const selectedBranch = computed(() => page.props.selectedBranch || null);
const featuredProducts = computed(() => page.props.featuredProducts || []);
const categories = computed(() => page.props.categories || []);
const branches = computed(() => page.props.branches || []);
const toast = ref({ show: false, product: null });
let toastTimer;

const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const heroImages = computed(() => {
    try { return JSON.parse(settings.value.hero_images || '[]').filter(Boolean); } catch { return []; }
});
const heroImage = (index) => heroImages.value[index] || featuredProducts.value[index % Math.max(featuredProducts.value.length, 1)]?.image && assetUrl(featuredProducts.value[index % featuredProducts.value.length].image);
const categoryEmoji = (name) => ({ Cake: '🎂', Bread: '🍞', Cookies: '🍪', Sweets: '🍬', 'Fast Food': '🥪', Toast: '🥨', Dessert: '🍮', 'Order Cake': '🎂' }[name] || '🍰');
const addToCart = (product) => {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch { cart = []; }
    const existing = cart.find(item => item.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
        existing.allow_pickup = product.allow_pickup !== false;
        existing.allow_home_delivery = product.allow_home_delivery !== false;
    } else cart.push({ product_id: product.id, name: product.name, price: Number(product.effective_price), quantity: 1, allow_pickup: product.allow_pickup !== false, allow_home_delivery: product.allow_home_delivery !== false });
    localStorage.setItem('cart', JSON.stringify(cart)); window.dispatchEvent(new Event('cart-updated'));
    toast.value = { show: true, product }; clearTimeout(toastTimer); toastTimer = setTimeout(() => toast.value.show = false, 2800);
};
</script>
