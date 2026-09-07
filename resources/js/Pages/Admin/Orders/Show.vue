<template>
    <AdminLayout active-menu="orders" page-title="Order Details">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div><p class="text-xs uppercase tracking-[.18em] text-brand-400 font-bold">{{ order.order_number }}</p><h3 class="font-serif font-bold text-brand-900 text-lg mt-1">Order Items</h3></div>
                        <button @click="copySummary" class="rounded-xl border border-brand-200 bg-brand-50 px-3 py-2 text-xs font-bold text-brand-700 hover:bg-brand-100 transition">{{ copied ? 'Copied!' : 'Copy order summary' }}</button>
                        <span class="rounded-full bg-gold-50 px-3 py-1.5 text-xs font-bold text-gold-700">{{ order.status.replace(/_/g, ' ') }}</span>
                    </div>
                    <div class="overflow-x-auto"><table class="w-full min-w-[560px]">
                        <thead class="bg-cream-50"><tr><th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Product</th><th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Qty</th><th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Price</th><th class="px-3 py-2.5 text-left text-xs font-bold text-brand-500 uppercase">Total</th></tr></thead>
                        <tbody class="divide-y divide-brand-50"><tr v-for="item in order.items" :key="item.id"><td class="px-3 py-3 text-sm font-medium text-brand-900">{{ item.product_name }}</td><td class="px-3 py-3 text-sm text-brand-600">{{ item.quantity }}</td><td class="px-3 py-3 text-sm text-brand-600">৳{{ item.price }}</td><td class="px-3 py-3 text-sm font-medium text-brand-900">৳{{ item.total }}</td></tr></tbody>
                    </table></div>
                    <div class="mt-4 space-y-1.5 text-sm border-t border-brand-100 pt-4"><div class="flex justify-between"><span class="text-brand-500">Subtotal</span><span class="font-medium text-brand-900">৳{{ order.subtotal }}</span></div><div class="flex justify-between"><span class="text-brand-500">Delivery charge</span><span class="font-medium text-brand-900">৳{{ order.delivery_charge }}</span></div><div v-if="order.discount > 0" class="flex justify-between text-green-600"><span>Discount</span><span class="font-medium">-৳{{ order.discount }}</span></div><div class="flex justify-between font-bold text-lg border-t border-brand-100 pt-2"><span class="text-brand-900">Total</span><span class="text-gold-600 font-serif">৳{{ order.total }}</span></div></div>
                </div>
                <div v-if="order.notes" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><h3 class="font-serif font-bold text-brand-900 text-lg mb-2">Customer Notes</h3><p class="text-sm text-brand-600">{{ order.notes }}</p></div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Customer Info</h3><div class="space-y-2.5 text-sm"><div class="flex justify-between gap-3"><span class="text-brand-400">Name</span><span class="font-medium text-brand-900 text-right">{{ order.customer_name }}</span></div><div class="flex justify-between gap-3"><span class="text-brand-400">Phone</span><span class="font-medium text-brand-900 text-right">{{ order.customer_phone }}</span></div><div v-if="order.customer_email" class="flex justify-between gap-3"><span class="text-brand-400">Email</span><span class="font-medium text-brand-900 text-right break-all">{{ order.customer_email }}</span></div><div v-if="order.customer_address" class="flex justify-between gap-3"><span class="text-brand-400">Address</span><span class="font-medium text-brand-900 text-right">{{ order.customer_address }}</span></div><div class="flex justify-between"><span class="text-brand-400">Delivery</span><span class="font-medium text-brand-900">{{ order.delivery_type === 'pickup' ? 'Pickup' : 'Home delivery' }}</span></div><div class="flex justify-between gap-3"><span class="text-brand-400">Branch</span><span class="font-medium text-brand-900 text-right">{{ order.branch?.name }}</span></div><div v-if="order.delivery_area" class="flex justify-between gap-3"><span class="text-brand-400">Area</span><span class="font-medium text-brand-900 text-right">{{ order.delivery_area.name }}</span></div><div class="flex justify-between"><span class="text-brand-400">Payment method</span><span class="font-medium text-brand-900">{{ paymentLabel(order.payment_method) }}</span></div></div></div>

                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><h3 class="font-serif font-bold text-brand-900 text-lg mb-2">Order discount</h3><p class="text-xs text-brand-400 mb-3">Apply or update a manual discount. Delivery charge is included in the maximum discount limit.</p><div class="flex gap-2"><input v-model.number="discount" type="number" min="0" step="0.01" class="flex-1 rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 outline-none" /><button @click="updateDiscount" class="rounded-xl bg-gold-500 text-brand-950 px-4 py-3 font-bold hover:bg-gold-400">Apply</button></div></div>

                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><div class="flex items-start justify-between gap-3 mb-4"><div><h3 class="font-serif font-bold text-brand-900 text-lg">Payment</h3><p class="text-xs text-brand-400 mt-1">Record full payment, partial advance or COD collection.</p></div><span :class="paymentStatus === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="rounded-full px-3 py-1 text-xs font-bold capitalize">{{ paymentStatus }}</span></div><div class="grid grid-cols-2 gap-2 mb-4"><button @click="paymentStatus = 'unpaid'" :class="paymentStatus === 'unpaid' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600'" class="py-2.5 rounded-xl font-bold text-sm transition">Unpaid / COD</button><button @click="setPaid" :class="paymentStatus === 'paid' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-600'" class="py-2.5 rounded-xl font-bold text-sm transition">Fully paid</button></div><div class="space-y-3"><label class="field-label">Paid amount (৳)<input v-model.number="paidAmount" type="number" min="0" :max="order.total" step="0.01" class="field-input" /></label><label class="field-label">Transaction / reference <span class="font-normal text-brand-400">(optional)</span><input v-model="transactionId" type="text" maxlength="255" placeholder="e.g. receipt number" class="field-input" /></label><label class="flex items-center gap-2 text-sm font-semibold text-brand-700"><input v-model="paymentVerified" type="checkbox" class="rounded border-brand-300 text-brand-600" /> Payment verified</label><div class="rounded-xl bg-cream-50 px-4 py-3 text-sm flex justify-between"><span class="text-brand-500">Due amount</span><strong :class="dueAmount > 0 ? 'text-red-600' : 'text-green-600'">৳{{ dueAmount.toFixed(2) }}</strong></div><button @click="savePayment" class="w-full rounded-xl bg-brand-700 text-white py-3 font-bold hover:bg-brand-800 transition">Save payment details</button></div></div>

                <div class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Order status</h3><select v-model="status" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none mb-3"><option v-for="s in statuses" :key="s" :value="s">{{ s.replace(/_/g, ' ') }}</option></select><button @click="updateStatus" class="w-full bg-brand-700 text-white py-3 rounded-xl font-bold hover:bg-brand-800 transition">Update status</button></div>

                <div v-if="order.delivery_type === 'home_delivery'" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6"><h3 class="font-serif font-bold text-brand-900 text-lg mb-4">Assign delivery man</h3><select v-model="deliveryManId" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none mb-3"><option value="">Select delivery man...</option><option v-for="man in deliveryMen" :key="man.id" :value="man.id">{{ man.name }} — {{ man.phone }} ({{ man.branch?.name || 'Any' }})</option></select><button @click="assignDeliveryMan" class="w-full bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition">Assign</button><div v-if="order.delivery_man" class="mt-3 bg-cream-100 rounded-xl px-4 py-3 text-sm"><div class="text-brand-400 text-xs mb-1">Assigned to:</div><div class="font-bold text-brand-900">{{ order.delivery_man.name }}</div><div class="text-brand-600">{{ order.delivery_man.phone }}</div></div></div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ order: Object, deliveryMen: Array });
const statuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
const status = ref(props.order.status);
const deliveryManId = ref(props.order.delivery_man_id || '');
const discount = ref(Number(props.order.discount || 0));
const paymentStatus = ref(props.order.payment_status || 'unpaid');
const paidAmount = ref(Number(props.order.advance_amount || (paymentStatus.value === 'paid' ? props.order.total : 0)));
const transactionId = ref(props.order.transaction_id || '');
const paymentVerified = ref(!!props.order.payment_verified);
const copied = ref(false);
const dueAmount = computed(() => Math.max(0, Number(props.order.total || 0) - Number(paidAmount.value || 0)));

const paymentLabel = (method) => ({ cash_on_delivery: 'Cash on delivery' }[method] || 'Cash on delivery');
const setPaid = () => { paymentStatus.value = 'paid'; paidAmount.value = Number(props.order.total || 0); };
const copySummary = async () => {
    const isHomeDelivery = props.order.delivery_type === 'home_delivery';
    const items = (props.order.items || []).map((item) => `- ${item.product_name} x${item.quantity} = ৳${item.total}`).join('\n');
    const summary = [
        `Order: ${props.order.order_number}`,
        `Customer: ${props.order.customer_name}`,
        `Phone: ${props.order.customer_phone}`,
        props.order.customer_email ? `Email: ${props.order.customer_email}` : null,
        `Delivery: ${props.order.delivery_type === 'pickup' ? 'Pickup' : 'Home delivery'}`,
        `Branch: ${props.order.branch?.name || 'N/A'}`,
        props.order.delivery_area?.name ? `Area: ${props.order.delivery_area.name}` : null,
        isHomeDelivery ? `Address: ${props.order.customer_address || 'Not provided'}` : null,
        '', 'Items:', items || '- No items', '',
        `Subtotal: ৳${props.order.subtotal}`,
        `Discount: ৳${props.order.discount || 0}`,
        `Delivery charge: ৳${props.order.delivery_charge || 0}`,
        `Total: ৳${props.order.total}`,
        `Payment: ${paymentLabel(props.order.payment_method)} - ${paymentStatus.value}`,
        `Paid: ৳${paidAmount.value || 0}`,
        `Due: ৳${dueAmount.value.toFixed(2)}`,
        `Payment verified: ${paymentVerified.value ? 'Yes' : 'No'}`,
        `Order status: ${props.order.status.replace(/_/g, ' ')}`,
        props.order.notes ? `Note: ${props.order.notes}` : null,
    ].filter(Boolean).join('\n');
    try {
        await navigator.clipboard.writeText(summary);
    } catch {
        const textarea = document.createElement('textarea');
        textarea.value = summary;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
    }
    copied.value = true;
    window.setTimeout(() => { copied.value = false; }, 1800);
};
const updateStatus = () => router.patch(route('admin.orders.status', props.order.id), { status: status.value });
const savePayment = () => router.patch(route('admin.orders.payment', props.order.id), { payment_status: paymentStatus.value, advance_amount: paidAmount.value, transaction_id: transactionId.value, payment_verified: paymentVerified.value }, { onSuccess: () => { props.order.payment_status = paymentStatus.value; props.order.advance_amount = paymentStatus.value === 'paid' ? Number(props.order.total) : Number(paidAmount.value || 0); props.order.transaction_id = transactionId.value; props.order.payment_verified = paymentVerified.value; } });
const assignDeliveryMan = () => router.patch(route('admin.orders.delivery-man', props.order.id), { delivery_man_id: deliveryManId.value });
const updateDiscount = () => router.patch(route('admin.orders.discount', props.order.id), { discount: discount.value }, { onSuccess: () => { props.order.discount = Number(discount.value); props.order.total = Math.max(0, Number(props.order.subtotal) + Number(props.order.delivery_charge) - Number(discount.value)); if (paymentStatus.value === 'paid') paidAmount.value = Number(props.order.total); } });
</script>
