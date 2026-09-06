<template>
    <article class="group rounded-[1.4rem] border border-brand-100 bg-white overflow-hidden shadow-soft hover:-translate-y-1 hover:shadow-card transition">
        <Link :href="route('products.show', product.slug)" class="block relative">
            <div class="aspect-[.95] bg-brand-50 flex items-center justify-center overflow-hidden">
                <img v-if="product.image" :src="assetUrl(product.image)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                <span v-else class="text-6xl">{{ emoji }}</span>
            </div>
            <span v-if="hasDiscount" class="absolute top-3 left-3 rounded-full bg-brand-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white">Sale</span>
        </Link>
        <div class="p-3.5 sm:p-4"><Link :href="route('products.show', product.slug)" class="block"><h3 class="font-semibold text-brand-900 text-sm line-clamp-1 group-hover:text-brand-500 transition">{{ product.name }}</h3><p class="text-[11px] text-brand-400 mt-1">{{ product.category?.name }}</p></Link><div class="flex items-center justify-between gap-2 mt-4"><div><span class="font-serif font-bold text-lg text-brand-700">৳{{ money(product.effective_price) }}</span><span v-if="hasDiscount" class="ml-1 text-[10px] text-brand-300 line-through">৳{{ money(product.branch_price || product.price) }}</span></div><button @click="$emit('add', product)" class="add-button" aria-label="Add to bag">+</button></div></div>
    </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ product: Object });
defineEmits(['add']);
const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const money = (value) => Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const hasDiscount = computed(() => props.product.branch_discount_price !== null && props.product.branch_discount_price !== undefined ? Number(props.product.branch_discount_price) > 0 : Number(props.product.discount_price) > 0);
const emoji = ({ Cake: '🎂', Bread: '🍞', Cookies: '🍪', Sweets: '🍬', 'Fast Food': '🥪', Dessert: '🍮' }[props.product.category?.name] || '🍰');
</script>
