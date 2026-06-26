<template>
    <div class="min-h-screen bg-gradient-to-b from-brand-50 via-cream-50 to-cream-100 flex flex-col">
        <!-- Preloader -->
        <Transition leave-active-class="transition duration-500 ease-in-out" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="loading" class="fixed inset-0 z-[100] hero-gradient flex items-center justify-center">
                <div class="text-center">
                    <div class="relative w-20 h-20 mx-auto mb-4">
                        <div class="absolute inset-0 rounded-full border-4 border-gold-400/30"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-gold-400 animate-spin"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-gold-400 font-serif font-bold text-xl">LV</span>
                        </div>
                    </div>
                    <p class="text-cream-200 font-serif text-sm tracking-widest uppercase">Lake View</p>
                    <p class="text-gold-400 text-xs tracking-[0.3em] uppercase mt-1">Sweets & Bakery</p>
                </div>
            </div>
        </Transition>

        <!-- Top Bar -->
        <div class="bg-brand-950 text-cream-100 text-xs py-2 hidden sm:block">
            <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Open Daily 9:00 AM - 11:00 PM
                </span>
                <div class="flex items-center gap-4">
                    <a :href="'tel:' + settings.whatsapp_number" class="hover:text-gold-400 transition">📞 {{ settings.whatsapp_number }}</a>
                    <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" class="hover:text-gold-400 transition">Facebook</a>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b-2 border-gold-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <Link :href="route('home')" class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full hero-gradient flex items-center justify-center shadow-lg ring-2 ring-gold-300">
                            <span class="text-gold-400 font-serif font-bold text-xl">LV</span>
                        </div>
                        <div>
                            <div class="font-serif font-bold text-brand-900 text-xl leading-tight">Lake View</div>
                            <div class="text-xs text-gold-600 tracking-widest uppercase font-medium">Sweets & Bakery</div>
                        </div>
                    </Link>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex items-center gap-8">
                        <Link :href="route('home')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">Home</Link>
                        <Link :href="route('products.index')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">Products</Link>
                        <Link :href="route('custom-cake.index')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">Custom Cake</Link>
                        <Link :href="route('about')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">About</Link>
                        <Link :href="route('contact')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">Contact</Link>
                        <Link :href="route('checkout.track')" class="text-brand-800 hover:text-gold-600 font-medium transition-colors">Track Order</Link>
                    </nav>

                    <!-- Right side -->
                    <div class="flex items-center gap-3">
                        <!-- Profile dropdown (if logged in) -->
                        <div v-if="$page.props.auth.user" class="relative">
                            <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl hover:bg-brand-50 transition">
                                <div class="w-9 h-9 hero-gradient rounded-full flex items-center justify-center text-gold-400 text-sm font-serif font-bold">
                                    {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <div class="text-sm font-bold text-brand-900 leading-tight">{{ $page.props.auth.user.name }}</div>
                                    <div class="text-xs text-brand-400 leading-tight capitalize">{{ $page.props.auth.user.role }}</div>
                                </div>
                                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <!-- Dropdown -->
                            <div v-if="profileOpen" class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-brand-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-brand-50">
                                    <div class="text-sm font-bold text-brand-900">{{ $page.props.auth.user.name }}</div>
                                    <div class="text-xs text-brand-400">{{ $page.props.auth.user.phone }}</div>
                                </div>
                                <Link :href="route('profile')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-brand-700 hover:bg-brand-50 transition" @click="profileOpen = false">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    My Profile
                                </Link>
                                <Link v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'super_admin'" :href="route('admin.dashboard')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-brand-700 hover:bg-brand-50 transition" @click="profileOpen = false">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    Admin Panel
                                </Link>
                                <div class="border-t border-brand-50 mt-1 pt-1">
                                    <button @click="logout(); profileOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Login/Register (if not logged in) -->
                        <template v-else>
                            <Link :href="route('login')" class="hidden sm:block text-sm text-brand-700 hover:text-gold-600 font-medium transition">Login</Link>
                            <Link :href="route('register')" class="hidden sm:block bg-gold-500 text-brand-950 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gold-400 transition shadow-md">Register</Link>
                        </template>
                        <!-- Cart -->
                        <Link :href="route('checkout.index')" class="relative p-2 text-brand-700 hover:text-gold-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-gold-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">{{ cartCount }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
            <div class="max-w-7xl mx-auto">{{ $page.props.flash.success }}</div>
        </div>
        <div v-if="$page.props.flash.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <div class="max-w-7xl mx-auto">{{ $page.props.flash.error }}</div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 pb-20 md:pb-0" @click="profileOpen = false">
            <slot />
        </main>

        <!-- Footer (desktop) -->
        <footer class="bg-brand-950 text-cream-200 mt-12 hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="md:col-span-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full hero-gradient flex items-center justify-center">
                                <span class="text-gold-400 font-serif font-bold text-xl">LV</span>
                            </div>
                            <div>
                                <div class="font-serif font-bold text-cream-100 text-lg">Lake View</div>
                                <div class="text-xs text-gold-500 tracking-widest uppercase">Sweets & Bakery</div>
                            </div>
                        </div>
                        <p class="text-sm text-brand-400 leading-relaxed">{{ settings.site_description }}</p>
                    </div>
                    <div>
                        <h3 class="font-serif font-semibold text-gold-400 mb-4 text-lg">Quick Links</h3>
                        <ul class="space-y-2.5 text-sm">
                            <li><Link :href="route('home')" class="hover:text-gold-400 transition">Home</Link></li>
                            <li><Link :href="route('products.index')" class="hover:text-gold-400 transition">Products</Link></li>
                            <li><Link :href="route('custom-cake.index')" class="hover:text-gold-400 transition">Custom Cake</Link></li>
                            <li><Link :href="route('about')" class="hover:text-gold-400 transition">About</Link></li>
                            <li><Link :href="route('contact')" class="hover:text-gold-400 transition">Contact</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-serif font-semibold text-gold-400 mb-4 text-lg">Contact</h3>
                        <ul class="space-y-2.5 text-sm text-brand-400">
                            <li class="flex items-center gap-2">📞 {{ settings.whatsapp_number }}</li>
                            <li class="flex items-center gap-2">🕐 Open 9AM - 11PM</li>
                            <li class="flex items-center gap-2">📍 Satkhira, Khulna</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-serif font-semibold text-gold-400 mb-4 text-lg">Follow Us</h3>
                        <div class="flex gap-3">
                            <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" class="w-10 h-10 bg-brand-800 rounded-full flex items-center justify-center hover:bg-gold-500 transition">
                                <svg class="w-5 h-5 text-cream-100" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a v-if="settings.whatsapp_number" :href="'https://wa.me/' + settings.whatsapp_number" target="_blank" class="w-10 h-10 bg-brand-800 rounded-full flex items-center justify-center hover:bg-green-600 transition">
                                <svg class="w-5 h-5 text-cream-100" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-brand-800 mt-10 pt-6 text-center text-sm text-brand-500">
                    <p>© {{ new Date().getFullYear() }} Lake View Sweets & Bakery. All rights reserved.</p>
                    <p class="mt-1 text-brand-400">Developed By <span class="text-gold-500 font-medium">Mir Javed Jeetu</span> | Contact: <span class="text-gold-500 font-medium">01811480222</span></p>
                </div>
            </div>
        </footer>

        <!-- Mobile Footer Credit -->
        <div class="md:hidden bg-brand-950 text-center py-3 text-xs text-brand-400 pb-20">
            <p>Developed By <span class="text-gold-500 font-medium">Mir Javed Jeetu</span> | Contact: <span class="text-gold-500 font-medium">01811480222</span></p>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t-2 border-gold-200 shadow-2xl z-50">
            <div class="flex items-center justify-around h-16 px-2">
                <Link :href="route('home')" :class="isActive('home') ? 'text-gold-600' : 'text-brand-400'" class="flex flex-col items-center gap-0.5 px-3 py-1 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    <span class="text-[10px] font-medium">Home</span>
                </Link>
                <Link :href="route('products.index')" :class="isActive('products') ? 'text-gold-600' : 'text-brand-400'" class="flex flex-col items-center gap-0.5 px-3 py-1 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <span class="text-[10px] font-medium">Products</span>
                </Link>
                <Link :href="route('custom-cake.index')" :class="isActive('custom-cake') ? 'text-gold-600' : 'text-brand-400'" class="flex flex-col items-center gap-0.5 px-3 py-1 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-1a2 2 0 00-2-2H8a2 2 0 00-2 2v1h12z" /></svg>
                    <span class="text-[10px] font-medium">Cake</span>
                </Link>
                <Link :href="route('checkout.index')" class="relative flex flex-col items-center gap-0.5 px-3 py-1 transition text-brand-400">
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-gold-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center font-bold">{{ cartCount }}</span>
                    </div>
                    <span class="text-[10px] font-medium">Cart</span>
                </Link>
                <button @click="moreOpen = !moreOpen" class="flex flex-col items-center gap-0.5 px-3 py-1 transition text-brand-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <span class="text-[10px] font-medium">More</span>
                </button>
            </div>
        </nav>

        <!-- Mobile More Menu -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-full opacity-0">
            <div v-if="moreOpen" class="md:hidden fixed bottom-16 left-0 right-0 bg-white rounded-t-3xl shadow-2xl border-t-2 border-gold-200 z-40 pb-4 max-h-[70vh] overflow-y-auto">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-serif font-bold text-brand-900">Menu</h3>
                        <button @click="moreOpen = false" class="p-1 text-brand-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div v-if="$page.props.auth.user" class="bg-cream-100 rounded-2xl p-3 mb-3 flex items-center gap-3">
                        <div class="w-10 h-10 hero-gradient rounded-full flex items-center justify-center text-gold-400 font-serif font-bold">
                            {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-brand-900 text-sm">{{ $page.props.auth.user.name }}</div>
                            <div class="text-xs text-brand-400">{{ $page.props.auth.user.phone }}</div>
                        </div>
                        <Link :href="route('profile')" @click="moreOpen = false" class="text-gold-600 text-sm font-bold">Profile →</Link>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <Link :href="route('about')" @click="moreOpen = false" class="flex flex-col items-center gap-1.5 bg-cream-50 rounded-2xl p-3 hover:bg-cream-100 transition">
                            <span class="text-2xl">ℹ️</span>
                            <span class="text-xs font-medium text-brand-700">About</span>
                        </Link>
                        <Link :href="route('contact')" @click="moreOpen = false" class="flex flex-col items-center gap-1.5 bg-cream-50 rounded-2xl p-3 hover:bg-cream-100 transition">
                            <span class="text-2xl">📞</span>
                            <span class="text-xs font-medium text-brand-700">Contact</span>
                        </Link>
                        <Link :href="route('checkout.track')" @click="moreOpen = false" class="flex flex-col items-center gap-1.5 bg-cream-50 rounded-2xl p-3 hover:bg-cream-100 transition">
                            <span class="text-2xl">📦</span>
                            <span class="text-xs font-medium text-brand-700">Track</span>
                        </Link>
                        <Link v-if="!$page.props.auth.user" :href="route('login')" @click="moreOpen = false" class="flex flex-col items-center gap-1.5 bg-gold-50 rounded-2xl p-3 hover:bg-gold-100 transition">
                            <span class="text-2xl">🔑</span>
                            <span class="text-xs font-medium text-brand-700">Login</span>
                        </Link>
                        <Link v-if="!$page.props.auth.user" :href="route('register')" @click="moreOpen = false" class="flex flex-col items-center gap-1.5 bg-gold-50 rounded-2xl p-3 hover:bg-gold-100 transition">
                            <span class="text-2xl">✨</span>
                            <span class="text-xs font-medium text-brand-700">Register</span>
                        </Link>
                        <Link v-if="$page.props.auth.user && ($page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'super_admin')" :href="route('admin.dashboard')" class="flex flex-col items-center gap-1.5 bg-brand-100 rounded-2xl p-3 hover:bg-brand-200 transition">
                            <span class="text-2xl">⚙️</span>
                            <span class="text-xs font-medium text-brand-700">Admin</span>
                        </Link>
                        <button v-if="$page.props.auth.user" @click="logout(); moreOpen = false" class="flex flex-col items-center gap-1.5 bg-red-50 rounded-2xl p-3 hover:bg-red-100 transition">
                            <span class="text-2xl">🚪</span>
                            <span class="text-xs font-medium text-red-600">Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
        <div v-if="moreOpen" @click="moreOpen = false" class="md:hidden fixed inset-0 bg-black/30 z-30"></div>
    </div>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const loading = ref(true);
const moreOpen = ref(false);
const profileOpen = ref(false);
const page = usePage();

const settings = computed(() => page.props.settings || {});

const isActive = (menu) => {
    const url = page.url;
    if (menu === 'home') return url === '/';
    if (menu === 'products') return url.startsWith('/products');
    if (menu === 'custom-cake') return url.startsWith('/custom-cake');
    return false;
};

const logout = () => router.post(route('logout'));

const cart = ref([]);
const cartCount = computed(() => cart.value.reduce((sum, item) => sum + item.quantity, 0));

const loadCart = () => {
    try {
        cart.value = JSON.parse(localStorage.getItem('cart') || '[]');
    } catch {
        cart.value = [];
    }
};

onMounted(() => {
    loadCart();
    window.addEventListener('cart-updated', loadCart);
    setTimeout(() => { loading.value = false; }, 600);
});
</script>
