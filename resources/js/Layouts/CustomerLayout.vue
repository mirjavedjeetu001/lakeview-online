<template>
    <div class="min-h-screen bg-cream-50 text-brand-900 flex flex-col">
        <div class="bg-brand-950 text-cream-100 text-[11px] tracking-wide">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-4">
                <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-sage-400 animate-pulse"></span>Freshly baked every day</span>
                <span class="hidden sm:inline">{{ settings.opening_hours || 'Open daily · 9:00 AM – 11:00 PM' }}</span>
            </div>
        </div>

        <header class="sticky top-0 z-50 bg-cream-50/90 backdrop-blur-xl border-b border-brand-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="h-[76px] flex items-center justify-between gap-4">
                    <Link :href="route('home')" class="flex items-center gap-3 shrink-0 group">
                        <div class="logo-mark"><span>LV</span></div>
                        <div class="leading-none">
                            <div class="font-serif text-[19px] sm:text-xl font-bold tracking-tight text-brand-900">Lake View</div>
                            <div class="mt-1 text-[9px] tracking-[.25em] uppercase text-brand-500 font-semibold">Sweets & Bakery</div>
                        </div>
                    </Link>

                    <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-brand-700">
                        <Link :href="route('home')" :class="isActive('home') ? 'nav-active' : 'nav-link'">Home</Link>
                        <Link :href="route('products.index')" :class="isActive('products') ? 'nav-active' : 'nav-link'">Shop</Link>
                        <Link :href="route('custom-cake.index')" :class="isActive('custom-cake') ? 'nav-active' : 'nav-link'">Custom cake</Link>
                        <Link :href="route('about')" :class="isActive('about') ? 'nav-active' : 'nav-link'">Our story</Link>
                        <Link :href="route('contact')" :class="isActive('contact') ? 'nav-active' : 'nav-link'">Contact</Link>
                    </nav>

                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <button @click="branchPickerOpen = true" class="branch-pill hidden sm:inline-flex">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.8"/></svg>
                            <span class="max-w-[132px] truncate">{{ selectedBranch?.name || 'Choose outlet' }}</span>
                            <svg class="w-3.5 h-3.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <button @click="branchPickerOpen = true" class="sm:hidden icon-button" aria-label="Choose outlet">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.8"/></svg>
                        </button>

                        <div v-if="$page.props.auth?.user" class="relative hidden md:block">
                            <button @click="profileOpen = !profileOpen" class="icon-button flex items-center gap-2 px-2">
                                <span class="avatar">{{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}</span>
                                <span class="hidden md:block max-w-[140px] truncate text-xs font-semibold text-brand-800">{{ $page.props.auth.user.name }}</span>
                            </button>
                            <div v-if="profileOpen" class="absolute right-0 top-full mt-3 w-56 rounded-2xl bg-white shadow-card border border-brand-100 p-2 z-50">
                                <div class="px-3 py-2.5 border-b border-brand-100 mb-1">
                                    <div class="text-sm font-bold">{{ $page.props.auth.user.name }}</div>
                                    <div class="text-xs text-brand-500 mt-0.5">{{ $page.props.auth.user.phone }}</div>
                                </div>
                                <Link :href="route('profile')" class="menu-item" @click="profileOpen = false">My profile</Link>
                                <Link v-if="['admin', 'super_admin'].includes($page.props.auth.user.role)" :href="route('admin.dashboard')" class="menu-item" @click="profileOpen = false">Admin panel</Link>
                                <button @click="logout" class="menu-item text-red-600 w-full text-left">Sign out</button>
                            </div>
                        </div>
                        <Link v-else :href="route('login')" class="hidden md:inline-flex text-sm font-semibold text-brand-700 hover:text-brand-500 px-2">Login</Link>

                        <Link :href="route('checkout.index')" class="cart-button" aria-label="Open cart">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14l-1 12H6L5 8Zm3 0V6a4 4 0 0 1 8 0v2"/></svg>
                            <span v-if="cartCount" class="cart-count">{{ cartCount }}</span>
                        </Link>
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="icon-button lg:hidden" aria-label="Open menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-brand-100 bg-cream-50 px-4 py-4 shadow-soft">
                <div class="grid grid-cols-2 gap-2">
                    <Link v-for="item in mobileLinks" :key="item.label" :href="item.href" class="mobile-nav-link" @click="mobileMenuOpen = false">{{ item.label }}</Link>
                    <Link v-if="!$page.props.auth?.user" :href="route('login')" class="mobile-nav-link" @click="mobileMenuOpen = false">Login</Link>
                    <Link v-if="!$page.props.auth?.user" :href="route('register')" class="mobile-nav-link" @click="mobileMenuOpen = false">Create account</Link>
                    <Link v-if="$page.props.auth?.user" :href="route('profile')" class="mobile-nav-link" @click="mobileMenuOpen = false">My profile</Link>
                    <Link v-if="$page.props.auth?.user && ['admin', 'super_admin'].includes($page.props.auth.user.role)" :href="route('admin.dashboard')" class="mobile-nav-link" @click="mobileMenuOpen = false">Admin panel</Link>
                    <button v-if="$page.props.auth?.user" type="button" class="mobile-nav-link text-left" @click="logout">Sign out</button>
                </div>
                <button @click="branchPickerOpen = true; mobileMenuOpen = false" class="mt-3 w-full flex items-center justify-between rounded-xl bg-brand-100 px-4 py-3 text-sm font-semibold text-brand-800">
                    <span>Shopping from {{ selectedBranch?.name || 'an outlet' }}</span><span class="text-brand-500">Change →</span>
                </button>
            </div>
        </header>

        <div v-if="$page.props.flash?.success" class="bg-sage-50 border-b border-sage-200 text-sage-700 text-sm px-4 py-3 text-center">{{ $page.props.flash.success }}</div>
        <div v-if="$page.props.flash?.error" class="bg-red-50 border-b border-red-200 text-red-700 text-sm px-4 py-3 text-center">{{ $page.props.flash.error }}</div>

        <main class="flex-1"><slot /></main>

        <footer class="mt-20 bg-brand-950 text-cream-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
                    <div>
                        <div class="flex items-center gap-3 mb-5"><div class="logo-mark logo-mark-dark"><span>LV</span></div><div><div class="font-serif text-xl font-bold text-cream-50">Lake View</div><div class="text-[9px] tracking-[.25em] uppercase text-brand-300 mt-1">Sweets & Bakery</div></div></div>
                        <p class="max-w-xs text-sm leading-7 text-brand-200">{{ settings.site_description || 'A little sweetness for every kind of day.' }}</p>
                    </div>
                    <div><h3 class="footer-heading">Explore</h3><div class="space-y-3 text-sm"><Link :href="route('home')" class="footer-link">Home</Link><Link :href="route('products.index')" class="footer-link">Shop all</Link><Link :href="route('custom-cake.index')" class="footer-link">Custom cake</Link></div></div>
                    <div><h3 class="footer-heading">Need help?</h3><div class="space-y-3 text-sm"><Link :href="route('contact')" class="footer-link">Contact us</Link><Link :href="route('checkout.track')" class="footer-link">Track order</Link><button @click="branchPickerOpen = true" class="footer-link text-left">Change outlet</button></div></div>
                    <div><h3 class="footer-heading">Main branch</h3><p class="text-sm leading-7 text-brand-200">{{ mainBranch?.name || 'Lake View Cafe & Restaurant (Main)' }}<br>{{ mainBranch?.address || 'Lake View Sweets & Bakery outlet' }}</p><a v-if="mainBranch?.phones?.length" :href="'tel:' + mainBranch.phones[0]" class="inline-flex mt-3 text-sm text-brand-100 hover:text-gold-300">{{ mainBranch.phones.join(' / ') }}</a><a v-else href="tel:+8801722554400" class="inline-flex mt-3 text-sm text-brand-100 hover:text-gold-300">+8801722554400</a></div>
                </div>
                <div class="mt-12 pt-5 border-t border-brand-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-brand-300"><span>© {{ new Date().getFullYear() }} Lake View Sweets & Bakery</span><span>Powered by Mir Javed Jeetu | Metasoft Info Solutions | 01811480222</span></div>
            </div>
        </footer>

        <nav class="lg:hidden sticky bottom-0 z-40 bg-cream-50/95 backdrop-blur-xl border-t border-brand-200/70 shadow-lg">
            <div class="h-16 flex items-center justify-around px-2">
                <Link :href="route('home')" class="bottom-nav-item" :class="isActive('home') && 'bottom-nav-active'"><span>⌂</span><small>Home</small></Link>
                <Link :href="route('products.index')" class="bottom-nav-item" :class="isActive('products') && 'bottom-nav-active'"><span>◌</span><small>Shop</small></Link>
                <Link :href="route('custom-cake.index')" class="bottom-nav-item" :class="isActive('custom-cake') && 'bottom-nav-active'"><span>✦</span><small>Cake</small></Link>
                <Link :href="route('checkout.index')" class="bottom-nav-item relative"><span>◇<b v-if="cartCount" class="cart-count cart-count-small">{{ cartCount }}</b></span><small>Bag</small></Link>
                <button type="button" class="bottom-nav-item" :class="mobileMenuOpen && 'bottom-nav-active'" @click="mobileMenuOpen = true"><span>☰</span><small>More</small></button>
            </div>
        </nav>

        <Transition name="fade">
            <div v-if="branchPickerOpen" class="fixed inset-0 z-[100] bg-brand-950/55 backdrop-blur-sm p-4 flex items-center justify-center" @click.self="selectedBranch && (branchPickerOpen = false)">
                <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-cream-50 shadow-2xl p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4 mb-6"><div><p class="eyebrow">Your local bakery</p><h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Choose an outlet</h2><p class="text-sm text-brand-500 mt-2">We’ll show availability and pricing for your selected branch.</p></div><button v-if="selectedBranch" @click="branchPickerOpen = false" class="icon-button" aria-label="Close"><span class="text-xl">×</span></button></div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <button v-for="branch in branches" :key="branch.id" @click="selectBranch(branch.id)" class="text-left rounded-2xl border p-4 transition group" :class="selectedBranch?.id === branch.id ? 'border-brand-500 bg-brand-100' : 'border-brand-200 bg-white hover:border-brand-400 hover:-translate-y-0.5'">
                            <div class="flex items-start gap-3"><span class="outlet-icon">⌖</span><span class="min-w-0"><span class="block font-semibold text-brand-900 group-hover:text-brand-600">{{ branch.name }}</span><span class="block text-xs text-brand-500 mt-1 line-clamp-2">{{ branch.address || 'Lake View Sweets & Bakery outlet' }}</span><span v-if="branch.phones?.[0]" class="block text-xs text-brand-600 mt-2">{{ branch.phones[0] }}</span></span><span v-if="selectedBranch?.id === branch.id" class="ml-auto text-brand-600">✓</span></div>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});
const branches = computed(() => page.props.branches || []);
const selectedBranch = computed(() => page.props.selectedBranch || null);
const mainBranch = computed(() => page.props.mainBranch || branches.value.find(branch => branch.name?.toLowerCase().includes('main')) || branches.value[0] || null);
const profileOpen = ref(false);
const mobileMenuOpen = ref(false);
const branchPickerOpen = ref(!selectedBranch.value || (page.url || '').includes('choose_branch=1'));
const cart = ref([]);
const cartCount = computed(() => cart.value.reduce((sum, item) => sum + Number(item.quantity || 0), 0));

const mobileLinks = computed(() => [
    { label: 'Home', href: route('home') },
    { label: 'Shop all', href: route('products.index') },
    { label: 'Custom cake', href: route('custom-cake.index') },
    { label: 'Our story', href: route('about') },
    { label: 'Contact', href: route('contact') },
    { label: 'Track order', href: route('checkout.track') },
]);

const isActive = (menu) => {
    const url = page.url || '';
    if (menu === 'home') return url === '/';
    if (menu === 'products') return url.startsWith('/products');
    if (menu === 'custom-cake') return url.startsWith('/custom-cake');
    if (menu === 'about') return url.startsWith('/about');
    if (menu === 'contact') return url.startsWith('/contact');
    return false;
};

const loadCart = () => {
    try { cart.value = JSON.parse(localStorage.getItem('cart') || '[]'); } catch { cart.value = []; }
};

const selectBranch = (branchId) => {
    if (Number(branchId) !== Number(selectedBranch.value?.id)) {
        localStorage.removeItem('cart');
        window.dispatchEvent(new Event('cart-updated'));
    }
    router.post(route('branch.select.store'), { branch_id: branchId }, {
        preserveScroll: true,
        onSuccess: () => { branchPickerOpen.value = false; router.visit(route('home')); },
    });
};

const logout = () => router.post(route('logout'));

onMounted(() => { loadCart(); window.addEventListener('cart-updated', loadCart); });
onUnmounted(() => window.removeEventListener('cart-updated', loadCart));
</script>
