<template>
    <AdminLayout active-menu="coupons" page-title="Coupons">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="font-bold text-gray-900">Coupons</h2>
                <button @click="openModal()" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Add Coupon</button>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Order</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Used</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ coupon.code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ coupon.type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ coupon.type === 'percentage' ? coupon.value + '%' : '৳' + coupon.value }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">৳{{ coupon.min_order_amount }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ coupon.used_count }}/{{ coupon.usage_limit || '∞' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs" :class="coupon.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">{{ coupon.is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-4 py-3 text-sm">
                            <button @click="openModal(coupon)" class="text-brand-600 hover:text-brand-700 mr-3">Edit</button>
                            <button @click="deleteCoupon(coupon)" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="coupons.links && coupons.links.length > 1" class="p-4 flex justify-center gap-2">
                <Link v-for="(link, i) in coupons.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-brand-600 text-white' : 'bg-white text-gray-700 border'" class="px-3 py-1.5 rounded-lg text-sm" v-html="link.label" :preserve-scroll="true"></Link>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="font-bold text-gray-900 mb-4">{{ editing ? 'Edit Coupon' : 'Add Coupon' }}</h3>
                <form @submit.prevent="saveCoupon" class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><input v-model="form.code" type="text" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 uppercase" /></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label><select v-model="form.type" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"><option value="percentage">Percentage</option><option value="fixed">Fixed</option></select></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Value</label><input v-model="form.value" type="number" step="0.01" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Min Order (৳)</label><input v-model="form.min_order_amount" type="number" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Max Discount (৳)</label><input v-model="form.max_discount_amount" type="number" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label><input v-model="form.usage_limit" type="number" placeholder="Leave empty for unlimited" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded text-brand-600" /> Active</label>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-brand-600 text-white py-2 rounded-lg font-medium hover:bg-brand-700">{{ editing ? 'Update' : 'Create' }}</button>
                        <button type="button" @click="showModal = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
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
