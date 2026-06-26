<template>
    <div class="h-screen flex overflow-hidden bg-cream-50">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-0 md:w-20'" class="bg-brand-950 text-cream-200 transition-all duration-300 overflow-hidden flex-shrink-0 h-full z-50 md:relative fixed">
            <div class="p-4 h-full overflow-y-auto">
                <Link :href="route('admin.dashboard')" class="flex items-center gap-3 mb-8">
                    <div class="w-11 h-11 rounded-full hero-gradient flex items-center justify-center flex-shrink-0">
                        <span class="text-gold-400 font-serif font-bold text-lg">LV</span>
                    </div>
                    <span v-if="sidebarOpen" class="font-serif font-bold text-lg text-cream-100">Admin Panel</span>
                </Link>

                <nav class="space-y-1">
                    <Link :href="route('admin.dashboard')" :class="activeMenu === 'dashboard' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span v-if="sidebarOpen">Dashboard</span>
                    </Link>
                    <Link :href="route('admin.products.index')" :class="activeMenu === 'products' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span v-if="sidebarOpen">Products</span>
                    </Link>
                    <Link :href="route('admin.categories.index')" :class="activeMenu === 'categories' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span v-if="sidebarOpen">Categories</span>
                    </Link>
                    <Link :href="route('admin.orders.index')" :class="activeMenu === 'orders' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span v-if="sidebarOpen">Orders</span>
                    </Link>
                    <Link :href="route('admin.custom-cakes.index')" :class="activeMenu === 'custom-cakes' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-1a2 2 0 00-2-2H8a2 2 0 00-2 2v1h12z"/></svg>
                        <span v-if="sidebarOpen">Custom Cake Orders</span>
                    </Link>
                    <Link :href="route('admin.branches.index')" :class="activeMenu === 'branches' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span v-if="sidebarOpen">Branches</span>
                    </Link>
                    <Link :href="route('admin.delivery-areas.index')" :class="activeMenu === 'delivery-areas' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span v-if="sidebarOpen">Delivery Areas</span>
                    </Link>
                    <Link :href="route('admin.delivery-men.index')" :class="activeMenu === 'delivery-men' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span v-if="sidebarOpen">Delivery Men</span>
                    </Link>
                    <Link :href="route('admin.coupons.index')" :class="activeMenu === 'coupons' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span v-if="sidebarOpen">Coupons</span>
                    </Link>
                    <Link :href="route('admin.settings.index')" :class="activeMenu === 'settings' ? 'bg-gold-500 text-brand-950' : 'hover:bg-brand-800'" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span v-if="sidebarOpen">Settings</span>
                    </Link>
                </nav>

                <div class="mt-8 pt-8 border-t border-brand-800">
                    <Link :href="route('home')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-brand-800 transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span v-if="sidebarOpen">Back to Website</span>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 h-full">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm flex-shrink-0 border-b border-brand-100 z-30">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-brand-50 transition">
                            <svg class="w-6 h-6 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="font-serif text-lg font-bold text-brand-900">{{ pageTitle }}</h1>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl hover:bg-brand-50 transition">
                            <div class="w-9 h-9 hero-gradient rounded-full flex items-center justify-center text-gold-400 text-sm font-serif font-bold">
                                {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-bold text-brand-900 leading-tight">{{ $page.props.auth?.user?.name }}</div>
                                <div class="text-xs text-brand-400 leading-tight capitalize">{{ $page.props.auth?.user?.role }}</div>
                            </div>
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div v-if="profileOpen" class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-brand-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-brand-50">
                                <div class="text-sm font-bold text-brand-900">{{ $page.props.auth?.user?.name }}</div>
                                <div class="text-xs text-brand-400">{{ $page.props.auth?.user?.email }}</div>
                                <div v-if="$page.props.auth?.user?.phone" class="text-xs text-brand-400 mt-0.5">{{ $page.props.auth?.user?.phone }}</div>
                            </div>
                            <Link :href="route('profile')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-brand-700 hover:bg-brand-50 transition" @click="profileOpen = false">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                My Profile
                            </Link>
                            <Link :href="route('home')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-brand-700 hover:bg-brand-50 transition" @click="profileOpen = false">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3m9 3v3m0-3c0-1.66-4-3-9-3s9 1.34 9 3z" /></svg>
                                View Website
                            </Link>
                            <div class="border-t border-brand-50 mt-1 pt-1">
                                <form @submit.prevent="logout" class="w-full">
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto" @click="profileOpen = false">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm m-4 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Page Content -->
                <div class="p-4 sm:p-6 pb-12">
                    <slot />
                </div>

                <!-- Footer Credit -->
                <div class="text-center py-4 text-xs text-brand-400 border-t border-brand-50">
                    Developed By <span class="text-gold-600 font-medium">Mir Javed Jeetu</span> | Contact: <span class="text-gold-600 font-medium">01811480222</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    activeMenu: { type: String, default: '' },
    pageTitle: { type: String, default: 'Dashboard' },
});

const sidebarOpen = ref(window.innerWidth >= 768);
const profileOpen = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>
