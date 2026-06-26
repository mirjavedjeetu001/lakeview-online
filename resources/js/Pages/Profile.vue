<template>
    <CustomerLayout>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-3xl shadow-xl border border-brand-100 overflow-hidden">
                <!-- Header Banner -->
                <div class="hero-gradient px-6 py-8 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 left-6 text-7xl">🎂</div>
                        <div class="absolute bottom-2 right-8 text-6xl">🧁</div>
                    </div>
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-gold-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl ring-4 ring-white/20">
                            <span class="text-brand-950 font-serif font-bold text-4xl">{{ user?.name?.charAt(0)?.toUpperCase() }}</span>
                        </div>
                        <h1 class="font-serif text-2xl font-bold text-cream-100">{{ user?.name }}</h1>
                        <p class="text-gold-300 text-sm capitalize mt-1">{{ user?.role?.replace('_', ' ') }}</p>
                        <p class="text-cream-300 text-xs mt-1">{{ user?.phone }}</p>
                    </div>
                </div>

                <!-- Profile Edit Form -->
                <div class="p-6">
                    <h2 class="font-serif font-bold text-brand-900 text-lg mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit Profile
                    </h2>
                    <form @submit.prevent="saveProfile" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Name</label>
                            <input v-model="form.name" type="text" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Phone</label>
                            <input v-model="form.phone" type="tel" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                        </div>
                        <button type="submit" class="w-full bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg">Save Changes</button>
                    </form>
                </div>

                <!-- Order History -->
                <div v-if="orders.length" class="border-t border-brand-100 p-6">
                    <h2 class="font-serif font-bold text-brand-900 text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Recent Orders
                    </h2>
                    <div class="space-y-3">
                        <div v-for="order in orders" :key="order.id" class="border border-brand-100 rounded-xl p-4 flex justify-between items-center hover:bg-cream-50 transition">
                            <div>
                                <div class="font-bold text-brand-900 text-sm">{{ order.order_number }}</div>
                                <div class="text-xs text-brand-400">{{ order.branch?.name }} · {{ order.items.length }} items</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gold-600 font-serif">৳{{ order.total }}</div>
                                <span :class="statusClass(order.status)" class="text-xs px-2 py-0.5 rounded-full capitalize">{{ order.status.replace(/_/g, ' ') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object, orders: Array });

const form = useForm({
    name: props.user?.name || '',
    phone: props.user?.phone || '',
    email: props.user?.email || '',
});

const saveProfile = () => form.post(route('profile.update'));
const statusClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    preparing: 'bg-purple-100 text-purple-700',
    ready: 'bg-indigo-100 text-indigo-700',
    out_for_delivery: 'bg-orange-100 text-orange-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');
</script>
