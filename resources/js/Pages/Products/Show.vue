<template>
    <CustomerLayout>
        <section class="section-shell pt-8 sm:pt-14">
            <div class="text-xs text-brand-400 mb-6"><Link :href="route('products.index')" class="hover:text-brand-600">Shop</Link><span class="mx-2">/</span>{{ product.category?.name }}</div>
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
                <div><div class="rounded-[2rem] overflow-hidden bg-brand-50 border border-brand-100 shadow-soft aspect-square flex items-center justify-center"><img v-if="selectedImage" :src="assetUrl(selectedImage)" :alt="product.name" class="w-full h-full object-cover"/><span v-else class="text-9xl">🍰</span></div><div v-if="productImages.length > 1" class="grid grid-cols-5 gap-2 mt-3"><button v-for="image in productImages" :key="image" type="button" @click="selectedImage = image" class="aspect-square overflow-hidden rounded-xl border-2 transition" :class="selectedImage === image ? 'border-gold-500' : 'border-brand-100 hover:border-brand-300'"><img :src="assetUrl(image)" :alt="product.name" class="w-full h-full object-cover" /></button></div></div>
                <div class="pt-2"><p class="eyebrow">{{ product.category?.name || 'From the bakery' }}</p><h1 class="font-serif text-4xl sm:text-5xl font-bold tracking-tight text-brand-900 mt-4">{{ product.name }}</h1><div class="flex items-end gap-3 mt-6"><span class="font-serif font-bold text-3xl text-brand-600">৳{{ money(product.effective_price) }}</span><span v-if="hasDiscount" class="text-base text-brand-300 line-through mb-1">৳{{ money(product.branch_price || product.price) }}</span></div><p v-if="product.description" class="text-brand-600 leading-8 mt-6">{{ product.description }}</p><div class="mt-8 p-4 rounded-2xl bg-brand-50 border border-brand-100 text-sm text-brand-700"><div class="flex items-center gap-2 font-semibold"><span class="w-2 h-2 rounded-full bg-sage-400"></span>Available at {{ selectedBranch?.name }}</div><p class="text-xs text-brand-500 mt-2">Freshness and availability are shown for your selected outlet.</p></div><div class="flex items-center gap-3 mt-7"><div class="flex items-center rounded-full border border-brand-200 bg-white p-1"><button @click="quantity > 1 && quantity--" class="w-9 h-9 rounded-full text-brand-600 hover:bg-brand-100">−</button><span class="w-9 text-center text-sm font-bold">{{ quantity }}</span><button @click="quantity++" class="w-9 h-9 rounded-full text-brand-600 hover:bg-brand-100">+</button></div><button @click="addToCart" class="btn-primary flex-1">Add to bag <span>→</span></button></div><Link :href="route('checkout.index')" class="btn-outline w-full mt-3">Go to checkout</Link></div>
            </div>
            <div v-if="related.length" class="mt-20"><div class="flex items-end justify-between gap-4"><div><p class="eyebrow">You may also like</p><h2 class="section-title text-left text-3xl">More from this collection</h2></div><Link :href="route('products.index')" class="text-sm font-bold text-brand-600">View all →</Link></div><div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 mt-8"><ProductCard v-for="item in related" :key="item.id" :product="item" @add="quickAdd" /></div></div>
        </section>

        <Teleport to="body">
            <Transition name="fade">
                <div v-if="addedModalOpen" class="fixed inset-0 z-[120] flex items-center justify-center bg-brand-950/60 p-3 sm:p-5 backdrop-blur-sm" @click.self="addedModalOpen = false">
                    <div class="relative w-full max-w-md rounded-[2rem] bg-cream-50 p-7 sm:p-10 text-center shadow-2xl">
                        <button type="button" @click="addedModalOpen = false" class="absolute right-4 top-4 icon-button bg-white shadow-soft" aria-label="Close modal">×</button>
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sage-50 text-3xl text-sage-600">✓</div>
                        <p class="eyebrow mt-6">Saved for later</p>
                        <h2 class="mt-3 font-serif text-3xl font-bold text-brand-900">Added to your bag</h2>
                        <div class="mx-auto mt-6 flex items-center gap-3 rounded-2xl border border-brand-100 bg-white p-3 text-left">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-brand-50 flex items-center justify-center"><img v-if="product.image" :src="assetUrl(product.image)" :alt="product.name" class="h-full w-full object-cover" /><span v-else class="text-3xl">🍰</span></div>
                            <div class="min-w-0"><div class="truncate font-bold text-brand-900">{{ product.name }}</div><div class="mt-1 text-xs text-brand-500">{{ addedQuantity }} item{{ addedQuantity > 1 ? 's' : '' }} · ৳{{ money(Number(product.effective_price) * addedQuantity) }}</div></div>
                        </div>
                        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                            <button type="button" @click="addedModalOpen = false" class="btn-outline">Continue shopping</button>
                            <Link :href="route('checkout.index')" class="btn-primary">View my bag <span>→</span></Link>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ product: Object, related: Array });
const page = usePage();
const selectedBranch = computed(() => page.props.selectedBranch || null);
const quantity = ref(1);
const addedQuantity = ref(1);
const addedModalOpen = ref(false);
const productImages = computed(() => [props.product.image, ...(props.product.gallery || [])].filter(Boolean));
const selectedImage = ref(productImages.value[0] || '');
const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const money = (value) => Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const hasDiscount = computed(() => props.product.branch_discount_price !== null && props.product.branch_discount_price !== undefined ? Number(props.product.branch_discount_price) > 0 : Number(props.product.discount_price) > 0);
const save = (product, amount = 1) => { let cart = []; try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch {} const item = cart.find(i => i.product_id === product.id); if (item) { item.quantity += amount; item.image = product.image || item.image; item.allow_pickup = product.allow_pickup !== false; item.allow_home_delivery = product.allow_home_delivery !== false; } else cart.push({ product_id: product.id, name: product.name, image: product.image || '', price: Number(product.effective_price), quantity: amount, allow_pickup: product.allow_pickup !== false, allow_home_delivery: product.allow_home_delivery !== false }); localStorage.setItem('cart', JSON.stringify(cart)); window.dispatchEvent(new Event('cart-updated')); };
const addToCart = () => { save(props.product, quantity.value); addedQuantity.value = quantity.value; addedModalOpen.value = true; };
const quickAdd = (product, amount = 1) => save(product, amount);
</script>
