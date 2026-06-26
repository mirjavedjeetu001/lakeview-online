<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Contact Us</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Get in Touch</h2>
                    <div class="space-y-3 text-sm">
                        <div><span class="text-gray-500">Phone:</span> {{ settings.whatsapp_number }}</div>
                        <div><span class="text-gray-500">Hours:</span> {{ settings.opening_hours }}</div>
                        <div><span class="text-gray-500">Location:</span> Satkhira, Khulna, Bangladesh</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Our Branches</h2>
                    <div class="space-y-3">
                        <div v-for="branch in branches" :key="branch.id" class="text-sm">
                            <div class="font-medium text-gray-900">{{ branch.name }}</div>
                            <a v-for="phone in branch.phones" :key="phone" :href="'tel:' + phone" class="text-brand-600">{{ phone }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
defineProps({ branches: Array });
const page = usePage();
const settings = computed(() => page.props.settings || {});
</script>
