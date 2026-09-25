<template>
    <AdminLayout active-menu="coupons" page-title="Coupons">
        <div class="mx-auto max-w-6xl space-y-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Offers & rewards</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Coupons</h2>
                    <p class="mt-2 text-sm text-brand-500">Create and manage discounts for your bakery customers.</p>
                </div>
                <button type="button" @click="openModal()" class="btn-primary !rounded-xl !px-5 !py-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Create coupon
                </button>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
                <div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-900">Coupon library</h3>
                        <p class="mt-1 text-sm text-brand-400">Review discount rules, usage and availability.</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gold-50 px-3 py-1.5 text-xs font-bold text-gold-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                        {{ coupons.total || coupons.data.length }} {{ (coupons.total || coupons.data.length) === 1 ? 'coupon' : 'coupons' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px]">
                        <thead class="bg-brand-50">
                            <tr>
                                <th class="table-head">Coupon code</th>
                                <th class="table-head">Offer</th>
                                <th class="table-head">Minimum order</th>
                                <th class="table-head">Redemptions</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="coupon in coupons.data" :key="coupon.id" class="transition hover:bg-cream-50/80">
                                <td class="table-cell"><span class="inline-flex rounded-lg border border-gold-200 bg-gold-50 px-3 py-1.5 font-mono text-xs font-extrabold tracking-wider text-brand-800">{{ coupon.code }}</span></td>
                                <td class="table-cell">
                                    <span :class="coupon.type === 'percentage' ? 'bg-gold-50 text-gold-700' : 'bg-brand-50 text-brand-700'" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path v-if="coupon.type === 'percentage'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5 5 19M7 7h.01M17 17h.01" /><path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m5-13H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>
                                        {{ coupon.type === 'percentage' ? coupon.value + '% off' : '৳' + coupon.value + ' off' }}
                                    </span>
                                    <p v-if="coupon.type === 'percentage' && coupon.max_discount_amount" class="mt-1.5 text-xs text-brand-400">Up to ৳{{ coupon.max_discount_amount }}</p>
                                </td>
                                <td class="table-cell font-semibold text-brand-700">৳{{ coupon.min_order_amount }}</td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-brand-700"><span>{{ coupon.used_count }}</span><span class="text-brand-300">/</span><span class="text-brand-400">{{ coupon.usage_limit || '∞' }}</span></div>
                                    <div v-if="coupon.usage_limit" class="mt-2 h-1.5 w-24 overflow-hidden rounded-full bg-brand-100"><div class="h-full rounded-full bg-gold-500" :style="{ width: Math.min((coupon.used_count / coupon.usage_limit) * 100, 100) + '%' }"></div></div>
                                </td>
                                <td class="table-cell"><span :class="coupon.is_active ? 'status-success' : 'status-danger'">{{ coupon.is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="table-cell whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openModal(coupon)" class="text-brand-600">Edit</button>
                                        <button type="button" @click="deleteCoupon(coupon)" class="text-red-500">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!coupons.data.length">
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M3 3h5l13 13-5 5L3 8V3Z" /></svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-brand-800">No coupons yet</p>
                                    <p class="mt-1 text-sm text-brand-400">Create your first offer to reward your customers.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="coupons.links && coupons.links.length > 1" class="flex justify-center gap-2 border-t border-brand-100 p-4">
                    <Link v-for="(link, i) in coupons.links" :key="i" :href="link.url || '#'" :class="link.active ? 'border-brand-700 bg-brand-700 text-white shadow-sm' : 'border-brand-200 bg-white text-brand-700 hover:border-brand-300 hover:bg-brand-50'" class="rounded-xl border px-3 py-2 text-sm font-bold transition" v-html="link.label" :preserve-scroll="true"></Link>
                </div>
            </section>
        </div>

        <Transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-brand-950/55 p-4 backdrop-blur-sm" @click.self="showModal = false">
                <section class="w-full max-w-xl overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-2xl">
                    <div class="flex items-start justify-between gap-4 border-b border-brand-100 bg-cream-50/80 p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gold-100 text-gold-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M3 3h5l13 13-5 5L3 8V3Z" /></svg>
                            </span>
                            <div>
                                <p class="eyebrow">Offers & rewards</p>
                                <h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">{{ editing ? 'Edit coupon' : 'Create a coupon' }}</h3>
                                <p class="mt-1 text-sm leading-6 text-brand-500">Set the discount and rules for this offer.</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="icon-button shrink-0" aria-label="Close form">×</button>
                    </div>

                    <form @submit.prevent="saveCoupon" class="space-y-5 p-6 sm:p-7">
                        <label class="field-label">Coupon code
                            <input v-model="form.code" type="text" required autocomplete="off" placeholder="e.g. WELCOME10" class="field-input font-mono font-bold uppercase tracking-wider" />
                        </label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="field-label">Discount type
                                <select v-model="form.type" class="field-input"><option value="percentage">Percentage discount</option><option value="fixed">Fixed amount</option></select>
                            </label>
                            <label class="field-label">Discount value
                                <span class="relative block"><input v-model="form.value" type="number" min="0" step="0.01" required class="field-input pr-14" /><span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-brand-400">{{ form.type === 'percentage' ? '%' : '৳' }}</span></span>
                            </label>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="field-label">Minimum order (৳)<input v-model="form.min_order_amount" type="number" min="0" step="0.01" class="field-input" /></label>
                            <label class="field-label">Maximum discount (৳)<input v-model="form.max_discount_amount" type="number" min="0" step="0.01" placeholder="No cap" class="field-input" /></label>
                        </div>
                        <label class="field-label">Usage limit <span class="font-normal text-brand-400">(optional)</span>
                            <input v-model="form.usage_limit" type="number" min="1" placeholder="Leave blank for unlimited use" class="field-input" />
                        </label>
                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-brand-100 bg-cream-50 px-4 py-3.5 transition hover:border-brand-200">
                            <span><span class="block text-sm font-bold text-brand-800">Coupon is active</span><span class="mt-0.5 block text-xs text-brand-400">Customers can use this offer at checkout.</span></span>
                            <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-brand-300 text-gold-500 focus:ring-gold-300" />
                        </label>
                        <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-5 sm:flex-row sm:justify-end">
                            <button type="button" @click="showModal = false" class="btn-outline !rounded-xl">Cancel</button>
                            <button type="submit" class="btn-primary !rounded-xl">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" /></svg>
                                {{ editing ? 'Save changes' : 'Create coupon' }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ coupons: Object });
const showModal = ref(false);
const editing = ref(null);
const form = ref({ code: '', type: 'percentage', value: '', min_order_amount: 0, max_discount_amount: '', usage_limit: '', is_active: true });

const openModal = (coupon = null) => {
    editing.value = coupon;
    if (coupon) { form.value = { code: coupon.code, type: coupon.type, value: coupon.value, min_order_amount: coupon.min_order_amount, max_discount_amount: coupon.max_discount_amount || '', usage_limit: coupon.usage_limit || '', is_active: coupon.is_active }; }
    else { form.value = { code: '', type: 'percentage', value: '', min_order_amount: 0, max_discount_amount: '', usage_limit: '', is_active: true }; }
    showModal.value = true;
};

const saveCoupon = () => {
    if (editing.value) { router.put(route('admin.coupons.update', editing.value.id), form.value, { onSuccess: () => showModal.value = false }); }
    else { router.post(route('admin.coupons.store'), form.value, { onSuccess: () => showModal.value = false }); }
};

const deleteCoupon = (coupon) => { if (confirm('Delete this coupon?')) router.delete(route('admin.coupons.destroy', coupon.id)); };
</script>
