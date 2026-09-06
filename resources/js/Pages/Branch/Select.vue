<template>
    <div class="min-h-screen bg-cream-50 relative overflow-hidden flex items-center justify-center px-4 py-10">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-brand-100/70 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-gold-100/70 blur-3xl"></div>
        <div class="relative w-full max-w-5xl">
            <div class="text-center mb-10">
                <div class="logo-mark mx-auto mb-5"><span>LV</span></div>
                <p class="eyebrow justify-center">Lake View Sweets & Bakery</p>
                <h1 class="font-serif text-4xl sm:text-5xl font-bold text-brand-900 mt-3">Where shall we bake for you?</h1>
                <p class="text-brand-500 mt-4 max-w-xl mx-auto leading-7">Choose your nearest outlet to see the right products, prices and availability for your order.</p>
            </div>

            <div v-if="branches.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <button v-for="branch in branches" :key="branch.id" @click="choose(branch.id)" class="group text-left rounded-[1.5rem] border border-brand-200 bg-white p-5 shadow-soft transition hover:-translate-y-1 hover:border-brand-500 hover:shadow-card" :class="selectedBranchId === branch.id ? 'ring-2 ring-brand-500 bg-brand-50' : ''">
                    <div class="flex items-start justify-between gap-4"><span class="outlet-icon outlet-icon-large">⌖</span><span v-if="selectedBranchId === branch.id" class="text-brand-600 text-lg">✓</span></div>
                    <h2 class="font-semibold text-brand-900 mt-5 group-hover:text-brand-600">{{ branch.name }}</h2>
                    <p class="text-sm text-brand-500 mt-2 leading-6">{{ branch.address || 'Lake View Sweets & Bakery outlet' }}</p>
                    <p v-if="branch.phones?.[0]" class="text-sm text-brand-600 mt-4">{{ branch.phones[0] }}</p>
                    <span class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-brand-700 group-hover:text-brand-500">Start shopping <span>→</span></span>
                </button>
            </div>

            <div v-else class="rounded-3xl bg-white border border-brand-200 p-10 text-center shadow-soft"><p class="font-semibold text-brand-900">No active outlets are available right now.</p><p class="text-sm text-brand-500 mt-2">Please ask an administrator to add an active branch.</p></div>
            <p class="text-center text-xs text-brand-400 mt-8">You can change your outlet anytime from the header.</p>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({ branches: Array, selectedBranchId: [Number, String, null] });
const choose = (branchId) => router.post(route('branch.select.store'), { branch_id: branchId }, { onSuccess: () => router.visit(route('home')) });
</script>
