<template>
    <CustomerLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <div class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
                        <img v-if="product.image" :src="'/storage/' + product.image" :alt="product.name" class="w-full h-full object-cover rounded-xl" />
                        <div v-else class="text-8xl text-gray-300">🍰</div>
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-2">{{ product.category?.name }}</div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-3xl text-brand-600 font-bold">৳{{ product.effective_price }}</span>
                        <span v-if="product.discount_price" class="text-xl text-gray-400 line-through">৳{{ product.price }}</span>
                    </div>
                    <p v-if="product.description" class="text-gray-600 mb-6">{{ product.description }}</p>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center border rounded-lg">
                            <button @click="quantity > 1 && quantity--" class="px-4 py-2 text-gray-600 hover:bg-gray-100">-</button>
                            <span class="px-4 py-2 font-medium">{{ quantity }}</span>
                            <button @click="quantity++" class="px-4 py-2 text-gray-600 hover:bg-gray-100">+</button>
                        </div>
                        <button @click="addToCart" class="flex-1 bg-brand-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-brand-700 transition">Add to Cart</button>
                    </div>
                    <Link :href="route('checkout.index')" class="block w-full text-center border-2 border-brand-600 text-brand-600 px-6 py-3 rounded-lg font-bold hover:bg-brand-50 transition">Go to Checkout</Link>
                </div>
            </div>

            <div v-if="related.length" class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Related Products</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div v-for="item in related" :key="item.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <Link :href="route('products.show', item.slug)">
                            <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                <img v-if="item.image" :src="'/storage/' + item.image" :alt="item.name" class="w-full h-full object-cover" />
                                <div v-else class="text-4xl text-gray-300">🍰</div>
                            </div>
                        </Link>
                        <div class="p-3">
                            <h3 class="font-medium text-gray-900 text-sm mb-1 line-clamp-1">{{ item.name }}</h3>
                            <span class="text-brand-600 font-bold">৳{{ item.effective_price }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ product: Object, related: Array });
const quantity = ref(1);

const addToCart = () => {
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch { cart = []; }
    const existing = cart.find(item => item.product_id === props.product.id);
    if (existing) { existing.quantity += quantity.value; }
    else { cart.push({ product_id: props.product.id, name: props.product.name, price: props.product.effective_price, quantity: quantity.value }); }
    localStorage.setItem('cart', JSON.stringify(cart));
    window.dispatchEvent(new Event('cart-updated'));
    alert('Added to cart!');
};
</script>
