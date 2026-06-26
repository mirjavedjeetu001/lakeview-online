<template>
    <CustomerLayout>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3">Custom Cake Order Submitted!</h1>
            <p class="text-gray-600 mb-2">Your order number is:</p>
            <p class="text-2xl font-bold text-brand-600 mb-6">{{ order.order_number }}</p>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-left mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Branch</span><span class="font-medium">{{ order.branch?.name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Delivery Date</span><span class="font-medium">{{ order.delivery_date }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium capitalize">{{ order.status }}</span></div>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Our team will review your design and contact you shortly to confirm the price.</p>
            <Link :href="route('home')" class="bg-brand-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-brand-700 transition">Back to Home</Link>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link } from '@inertiajs/vue3';
defineProps({ order: Object });
</script>
