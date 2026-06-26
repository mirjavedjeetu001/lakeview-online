<template>
    <AdminLayout active-menu="custom-cakes" page-title="Custom Cake Details">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Cake Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-serif font-bold text-brand-900 text-lg">Cake Details</h3>
                        <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-bold capitalize">{{ order.status.replace(/_/g, ' ') }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Type</span><span class="font-medium text-brand-900">{{ order.cake_type || '-' }}</span></div>
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Size</span><span class="font-medium text-brand-900">{{ order.cake_size || '-' }}</span></div>
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Flavor</span><span class="font-medium text-brand-900">{{ order.cake_flavor || '-' }}</span></div>
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Message on Cake</span><span class="font-medium text-brand-900">{{ order.message_on_cake || '-' }}</span></div>
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Delivery Date</span><span class="font-medium text-brand-900">{{ order.delivery_date }}</span></div>
                        <div class="bg-cream-50 rounded-xl p-3"><span class="text-brand-400 block text-xs">Delivery Time</span><span class="font-medium text-brand-900">{{ order.delivery_time || '-' }}</span></div>
                    </div>
                    <div v-if="order.design_image" class="mt-4">
                        <h4 class="font-medium text-sm text-brand-700 mb-2">Design Image</h4>
                        <img :src="'/storage/' + order.design_image" alt="Design" class="max-w-sm rounded-xl border-2 border-brand-100" />
                    </div>
                    <div v-if="order.notes" class="mt-4 border-t border-brand-100 pt-4">
                        <h4 class="font-medium text-sm text-brand-700 mb-1">Customer Notes</h4>
                        <p class="text-sm text-brand-600 bg-cream-50 rounded-xl p-3">{{ order.notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Customer Info</h3>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><span class="text-brand-400">Name</span><span class="font-medium text-brand-900">{{ order.customer_name }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Phone</span><span class="font-medium text-brand-900">{{ order.customer_phone }}</span></div>
                        <div v-if="order.customer_address" class="flex justify-between"><span class="text-brand-400">Address</span><span class="font-medium text-brand-900 text-right">{{ order.customer_address }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Delivery</span><span class="font-medium text-brand-900">{{ order.delivery_type === 'pickup' ? 'Pickup' : 'Home Delivery' }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Branch</span><span class="font-medium text-brand-900">{{ order.branch?.name }}</span></div>
                        <div v-if="order.delivery_area" class="flex justify-between"><span class="text-brand-400">Area</span><span class="font-medium text-brand-900">{{ order.delivery_area?.name }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Payment</span><span class="font-medium text-brand-900">{{ order.payment_method === 'advance_payment' ? 'Advance Payment' : 'Cash on Delivery' }}</span></div>
                    </div>
                </div>

                <!-- Price & Status Update -->
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Set Price & Status</h3>
                    <form @submit.prevent="updateOrder" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Estimated Price (৳)</label>
                            <input v-model="form.estimated_price" type="number" step="0.01" min="0" placeholder="0" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                            <p class="text-xs text-brand-400 mt-1">Set the price for this custom cake. Total will include delivery charge.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Status</label>
                            <select v-model="form.status" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none">
                                <option v-for="s in statuses" :key="s" :value="s">{{ s.replace(/_/g, ' ') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Admin Notes</label>
                            <textarea v-model="form.admin_notes" rows="3" placeholder="Internal notes about this order..." class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none resize-y"></textarea>
                        </div>
                        <div v-if="order.estimated_price" class="bg-cream-100 rounded-xl p-3 text-sm">
                            <div class="flex justify-between mb-1"><span class="text-brand-400">Cake Price</span><span class="font-bold text-brand-900">৳{{ order.estimated_price }}</span></div>
                            <div class="flex justify-between mb-1"><span class="text-brand-400">Delivery</span><span class="font-bold text-brand-900">৳{{ order.delivery_charge || 0 }}</span></div>
                            <div class="flex justify-between border-t border-brand-200 pt-1"><span class="font-bold text-brand-700">Total</span><span class="font-bold text-gold-600 font-serif text-lg">৳{{ order.total || 0 }}</span></div>
                        </div>
                        <button type="submit" class="w-full bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg">Update Order</button>
                    </form>
                </div>

                <!-- Payment Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Payment Status</h3>
                    <!-- Advance Payment Info -->
                    <div v-if="order.payment_method === 'advance_payment'" class="bg-gold-50 border border-gold-200 rounded-xl p-3 mb-3 space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-brand-400">Method</span><span class="font-bold text-brand-900">Advance Payment</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Advance Amount</span><span class="font-bold text-green-600">৳{{ order.advance_amount || 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Remaining (COD)</span><span class="font-bold text-brand-900">৳{{ Math.max(0, (order.total || 0) - (order.advance_amount || 0)) }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Transaction ID</span><span class="font-mono font-bold text-brand-900">{{ order.transaction_id || 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-brand-400">Verified</span><span :class="order.payment_verified ? 'text-green-600' : 'text-red-600'" class="font-bold">{{ order.payment_verified ? 'Yes' : 'No' }}</span></div>
                    </div>
                    <div v-if="order.payment_method === 'advance_payment' && !order.payment_verified" class="mb-3">
                        <button @click="verifyPayment" class="w-full bg-green-500 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-green-600 transition">Verify Payment</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="updatePayment('unpaid')" :class="order.payment_status === 'unpaid' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600 hover:bg-red-100'" class="py-3 rounded-xl font-bold transition">Unpaid</button>
                        <button @click="updatePayment('paid')" :class="order.payment_status === 'paid' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-600 hover:bg-green-100'" class="py-3 rounded-xl font-bold transition">Paid</button>
                    </div>
                    <p class="text-xs text-brand-400 text-center mt-3">Current: <span :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-red-600'" class="font-bold capitalize">{{ order.payment_status || 'unpaid' }}</span></p>
                </div>

                <!-- Delivery Man (home delivery only) -->
                <div v-if="order.delivery_type === 'home_delivery'" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Assign Delivery Man</h3>
                    <form @submit.prevent="assignDeliveryMan" class="space-y-3">
                        <select v-model="deliveryManForm.delivery_man_id" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none">
                            <option value="">Select delivery man...</option>
                            <option v-for="dm in deliveryMen" :key="dm.id" :value="dm.id">{{ dm.name }} - {{ dm.phone }}</option>
                        </select>
                        <div v-if="order.delivery_man" class="bg-cream-100 rounded-xl p-3 text-sm">
                            <div class="font-bold text-brand-900">{{ order.delivery_man.name }}</div>
                            <div class="text-brand-400">{{ order.delivery_man.phone }}</div>
                        </div>
                        <button type="submit" class="w-full bg-brand-700 text-white py-3 rounded-xl font-bold hover:bg-brand-800 transition">Assign</button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ order: Object, deliveryMen: Array });
const statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
const form = ref({
    status: props.order.status,
    estimated_price: props.order.estimated_price || '',
    admin_notes: props.order.admin_notes || '',
});
const deliveryManForm = ref({ delivery_man_id: props.order.delivery_man_id || '' });

const updateOrder = () => router.patch(route('admin.custom-cakes.status', props.order.id), form.value);
const assignDeliveryMan = () => router.patch(route('admin.custom-cakes.delivery-man', props.order.id), deliveryManForm.value);
const updatePayment = (status) => router.patch(route('admin.custom-cakes.payment', props.order.id), { payment_status: status });
const verifyPayment = () => router.patch(route('admin.custom-cakes.verify-payment', props.order.id), {}, {
    onSuccess: () => { props.order.payment_verified = true; props.order.payment_status = 'paid'; },
});
const statusClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    preparing: 'bg-purple-100 text-purple-700',
    ready: 'bg-indigo-100 text-indigo-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');
</script>
