<template>
    <CustomerLayout>
        <!-- Hero Slider -->
        <section class="relative h-[400px] sm:h-[500px] overflow-hidden">
            <div v-for="(slide, i) in slides" :key="i"
                v-show="currentSlide === i"
                class="absolute inset-0 transition-opacity duration-700"
                :class="slide.bg || ''"
                :style="slide.image ? `background: linear-gradient(rgba(26,15,0,0.6), rgba(26,15,0,0.7)), url('${slide.image}') center/cover;` : ''">
                <div class="absolute inset-0 opacity-10" v-if="slide.emoji1">
                    <div class="absolute top-10 left-10 text-9xl">{{ slide.emoji1 }}</div>
                    <div class="absolute bottom-10 right-10 text-9xl">{{ slide.emoji2 }}</div>
                </div>
                <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
                    <div class="text-white max-w-2xl">
                        <span class="text-gold-400 text-sm tracking-widest uppercase font-medium">{{ slide.tag }}</span>
                        <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold mt-3 mb-4 text-cream-50 leading-tight">{{ slide.title }}</h1>
                        <p class="text-base sm:text-lg text-brand-200 mb-8 leading-relaxed">{{ slide.subtitle }}</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link :href="route('products.index')" class="bg-gold-500 text-brand-950 px-8 py-3.5 rounded-full font-bold hover:bg-gold-400 transition shadow-lg text-center">
                                {{ slide.btn1 }}
                            </Link>
                            <Link :href="route('custom-cake.index')" class="border-2 border-gold-400 text-gold-400 px-8 py-3.5 rounded-full font-bold hover:bg-gold-400 hover:text-brand-950 transition text-center">
                                {{ slide.btn2 }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slider dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                <button v-for="(slide, i) in slides" :key="i" @click="currentSlide = i"
                    :class="currentSlide === i ? 'w-8 bg-gold-500' : 'w-2 bg-white/50'"
                    class="h-2 rounded-full transition-all"></button>
            </div>
            <!-- Arrows -->
            <button @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </section>

        <!-- Categories -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-10">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Explore</span>
                <h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Our Categories</h2>
                <div class="w-20 h-1 bg-gold-500 mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                <Link v-for="cat in categories" :key="cat.id" :href="route('products.index', { category: cat.slug })"
                    class="bg-white rounded-2xl overflow-hidden card-shadow card-shadow-hover transition border border-brand-100 group">
                    <div class="aspect-square bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center relative overflow-hidden">
                        <img v-if="cat.image" :src="'/storage/' + cat.image" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" />
                        <div v-else class="text-5xl group-hover:scale-110 transition duration-300">{{ categoryEmoji(cat.name) }}</div>
                    </div>
                    <div class="p-3 text-center">
                        <div class="text-sm font-medium text-brand-800 group-hover:text-gold-600 transition">{{ cat.name }}</div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="bg-cream-100 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Bestsellers</span>
                        <h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Featured Products</h2>
                        <div class="w-20 h-1 bg-gold-500 mt-4 rounded-full"></div>
                    </div>
                    <Link :href="route('products.index')" class="text-brand-700 font-medium hover:text-gold-600 transition hidden sm:block">View All →</Link>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    <div v-for="product in featuredProducts" :key="product.id"
                        class="bg-white rounded-2xl card-shadow card-shadow-hover transition overflow-hidden group">
                        <Link :href="route('products.show', product.slug)">
                            <div class="aspect-square bg-brand-50 flex items-center justify-center overflow-hidden">
                                <img v-if="product.image" :src="'/storage/' + product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                                <div v-else class="text-5xl text-brand-200 group-hover:scale-110 transition duration-300">{{ categoryEmoji(product.category?.name) }}</div>
                            </div>
                        </Link>
                        <div class="p-4">
                            <Link :href="route('products.show', product.slug)">
                                <h3 class="font-medium text-brand-900 text-sm mb-1 line-clamp-1 group-hover:text-gold-600 transition">{{ product.name }}</h3>
                            </Link>
                            <div class="text-xs text-brand-400 mb-3">{{ product.category?.name }}</div>
                            <div class="flex items-center justify-between">
                                <span class="text-brand-700 font-bold text-lg font-serif">৳{{ product.effective_price }}</span>
                                <button @click="addToCart(product)" class="bg-brand-700 text-white px-3 py-2 rounded-lg text-sm hover:bg-gold-500 transition font-medium">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-8 sm:hidden">
                    <Link :href="route('products.index')" class="text-brand-700 font-medium hover:text-gold-600">View All →</Link>
                </div>
            </div>
        </section>

        <!-- Branches -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-10">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Visit Us</span>
                <h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Our Branches</h2>
                <div class="w-20 h-1 bg-gold-500 mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div v-for="branch in branches" :key="branch.id" class="bg-white rounded-2xl p-6 card-shadow card-shadow-hover transition border border-brand-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-brand-900 text-base mb-1">{{ branch.name }}</h3>
                            <div v-if="branch.phones" class="mt-1">
                                <a v-for="phone in branch.phones" :key="phone" :href="'tel:' + phone" class="text-sm text-gold-600 block hover:text-gold-500">{{ phone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Custom Cake Banner -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="hero-gradient rounded-3xl p-10 sm:p-14 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 text-9xl opacity-10">🎂</div>
                <div class="absolute bottom-0 left-0 text-8xl opacity-10">🍰</div>
                <div class="relative">
                    <span class="text-gold-400 text-sm tracking-widest uppercase font-medium">Made to Order</span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-bold mb-3 mt-2">Order Your Custom Cake</h2>
                    <p class="text-brand-200 mb-8 max-w-2xl mx-auto leading-relaxed">{{ settings.custom_cake_info }}</p>
                    <Link :href="route('custom-cake.index')" class="bg-gold-500 text-brand-950 px-8 py-3.5 rounded-full font-bold hover:bg-gold-400 transition inline-block shadow-lg">
                        Order Now
                    </Link>
                </div>
            </div>
        </section>

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
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    categories: Array,
    featuredProducts: Array,
    branches: Array,
});

const page = usePage();
const settings = computed(() => page.props.settings || {});

// Hero Slider - merge settings with default slides
const heroImages = computed(() => {
    try {
        const imgs = JSON.parse(settings.value.hero_images || '[]');
        return imgs.filter(u => u.trim());
    } catch { return []; }
});

const slides = computed(() => {
    const defaultSlides = [
        { bg: 'hero-gradient', tag: 'Premium Quality Since 2023', title: settings.value.hero_title || 'Lake View Sweets & Bakery', subtitle: settings.value.hero_subtitle || 'The finest sweets and bakery in Satkhira', btn1: 'Browse Products', btn2: 'Custom Cake Order', emoji1: '🍰', emoji2: '🧁' },
        { bg: 'bg-gradient-to-br from-brand-900 to-brand-700', tag: 'Fresh Daily', title: 'Baked Fresh Every Morning', subtitle: 'Bread, cookies, toast and pastries - made fresh every single day with the finest ingredients', btn1: 'Order Now', btn2: 'View Menu', emoji1: '🍞', emoji2: '🍪' },
        { bg: 'bg-gradient-to-br from-brand-800 to-brand-600', tag: 'Custom Cakes', title: 'Your Dream Cake, Our Recipe', subtitle: 'Upload your design and we will create the cake exactly as you want it. Perfect for any occasion!', btn1: 'Order Custom Cake', btn2: 'Browse Products', emoji1: '🎂', emoji2: '🎁' },
    ];
    if (heroImages.value.length > 0) {
        return heroImages.value.map((img, i) => ({
            bg: '', image: img,
            tag: i === 0 ? (settings.value.site_tagline || 'Premium Quality') : '',
            title: i === 0 ? (settings.value.hero_title || 'Lake View Sweets & Bakery') : '',
            subtitle: i === 0 ? (settings.value.hero_subtitle || '') : '',
            btn1: 'Browse Products', btn2: 'Custom Cake Order',
            emoji1: '', emoji2: '',
        }));
    }
    return defaultSlides;
});
const currentSlide = ref(0);
let slideTimer = null;
const nextSlide = () => currentSlide.value = (currentSlide.value + 1) % slides.value.length;
const prevSlide = () => currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length;
onMounted(() => { slideTimer = setInterval(nextSlide, 5000); });
onUnmounted(() => { if (slideTimer) clearInterval(slideTimer); });

const categoryEmoji = (name) => {
    const map = { 'Cake': '🎂', 'Bread': '🍞', 'Cookies': '🍪', 'Sweets': '🍬', 'Fast Food': '🍔', 'Toast': '🥪', 'Dessert': '🍮', 'Order Cake': '🎂' };
    return map[name] || '🍰';
};

// Add to cart with toast
const toast = ref({ show: false, message: '', product: null });
let toastTimer = null;
const showToast = (product) => {
    toast.value = { show: true, message: 'Added to cart!', product };
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toast.value.show = false; }, 3000);
};

const addToCart = (product) => {
    let cart = [];
    try {
        cart = JSON.parse(localStorage.getItem('cart') || '[]');
    } catch {
        cart = [];
    }
    const existing = cart.find(item => item.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: product.effective_price,
            quantity: 1,
        });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    window.dispatchEvent(new Event('cart-updated'));
    showToast(product);
};
</script>
