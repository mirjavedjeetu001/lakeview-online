<template>
    <div class="min-h-screen flex">
        <!-- Left side - Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 hero-gradient relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 text-9xl">🎂</div>
                <div class="absolute bottom-32 right-16 text-8xl">🧁</div>
                <div class="absolute top-1/2 left-1/3 text-7xl">🍞</div>
                <div class="absolute bottom-20 left-20 text-6xl">🍪</div>
            </div>
            <div class="flex flex-col justify-center px-16 text-cream-100 relative z-10">
                <div class="w-20 h-20 bg-gold-500 rounded-full flex items-center justify-center mb-8 shadow-2xl">
                    <span class="text-brand-950 font-serif font-bold text-3xl">LV</span>
                </div>
                <h1 class="font-serif text-4xl font-bold leading-tight">Lake View<br/>Sweets & Bakery</h1>
                <p class="text-gold-300 mt-4 text-lg">The finest sweets and bakery in Satkhira. Fresh baked every morning.</p>
                <div class="mt-10 space-y-3">
                    <div class="flex items-center gap-3 text-cream-200">
                        <span class="w-8 h-8 bg-gold-500/20 rounded-full flex items-center justify-center text-gold-400">📞</span>
                        <span class="text-sm">+8801722554400</span>
                    </div>
                    <div class="flex items-center gap-3 text-cream-200">
                        <span class="w-8 h-8 bg-gold-500/20 rounded-full flex items-center justify-center text-gold-400">🕐</span>
                        <span class="text-sm">Open Daily 7:00 AM - 10:00 PM</span>
                    </div>
                    <div class="flex items-center gap-3 text-cream-200">
                        <span class="w-8 h-8 bg-gold-500/20 rounded-full flex items-center justify-center text-gold-400">📍</span>
                        <span class="text-sm">Satkhira, Khulna</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Login form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-cream-50 px-4 py-12">
            <div class="max-w-md w-full">
                <!-- Mobile logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 hero-gradient rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-gold-400 font-serif font-bold text-2xl">LV</span>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-brand-100 p-8 sm:p-10">
                    <h2 class="font-serif text-2xl font-bold text-brand-900">Welcome Back</h2>
                    <p class="text-brand-400 text-sm mt-1 mb-8">Login with your phone number or email</p>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Login field (phone or email) -->
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Phone or Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </span>
                                <input v-model="form.login" type="text" required placeholder="01XXXXXXXXX or email@example.com" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 pl-12 pr-4 py-3.5 text-brand-900 transition outline-none" />
                            </div>
                            <div v-if="errors.login" class="text-red-500 text-sm mt-1.5 bg-red-50 rounded-lg px-3 py-2">{{ errors.login }}</div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Password</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </span>
                                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="Enter your password" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 pl-12 pr-12 py-3.5 text-brand-900 transition outline-none" />
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-brand-400 hover:text-brand-600">
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29.883L7.708 14.06m6.836-6.936l3.29-.883L17.292 9.94M21 21L3 3" /></svg>
                                </button>
                            </div>
                            <div v-if="errors.password" class="text-red-500 text-sm mt-1.5">{{ errors.password }}</div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-brand-600 cursor-pointer">
                                <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded text-gold-500 focus:ring-gold-400 border-brand-200" /> Remember me
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-gold-500 text-brand-950 py-3.5 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <span>Login</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-brand-500">
                        Don't have an account?
                        <Link :href="route('register')" class="text-gold-600 font-semibold hover:text-gold-500">Create one</Link>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <Link :href="route('home')" class="text-sm text-brand-400 hover:text-brand-600 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Home
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ errors: Object });
const showPassword = ref(false);

const form = useForm({ login: '', password: '', remember: false });

const submit = () => form.post(route('login.post'), {
    preserveScroll: true,
});
</script>
