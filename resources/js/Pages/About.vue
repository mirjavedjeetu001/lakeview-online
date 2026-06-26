<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">About Us</h1>
            <div class="prose max-w-none text-gray-600">
                <p class="text-lg mb-4">{{ settings.about_text }}</p>
                <p class="mb-4">We take pride in using only the finest ingredients to create our sweets, cakes, cookies, breads, and bakery items. Every product is made fresh daily with love and care.</p>
                <p class="mb-4">With 7 branches across Satkhira, we are always close to you. Visit any of our branches or order online for pickup or home delivery.</p>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const settings = computed(() => page.props.settings || {});
</script>
