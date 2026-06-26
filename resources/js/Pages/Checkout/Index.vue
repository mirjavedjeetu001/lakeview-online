<template>
    <CustomerLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Checkout</span>
                <h1 class="font-serif text-3xl font-bold text-brand-900 mt-2">Complete Your Order</h1>
                <div class="w-20 h-1 bg-gold-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Empty Cart -->
            <div v-if="!cartItems.length" class="bg-white rounded-2xl card-shadow border border-brand-100 p-12 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <p class="text-brand-500 text-lg mb-4">Your cart is empty</p>
                <Link :href="route('products.index')" class="inline-block bg-gold-500 text-brand-950 px-6 py-3 rounded-full font-bold hover:bg-gold-400 transition">Browse Products</Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left: Form -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Cart Items -->
                    <div class="bg-white rounded-2xl card-shadow border border-brand-100 p-6">
                        <h2 class="font-serif font-bold text-brand-900 text-lg mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-sm">1</span>
                            Order Items
                        </h2>
                        <div v-for="item in cartItems" :key="item.product_id" class="flex items-center gap-3 py-3 border-b border-brand-50 last:border-0">
                            <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl">🍰</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-brand-900 text-sm truncate">{{ item.name }}</div>
                                <div class="text-xs text-brand-400">৳{{ item.price }} × {{ item.quantity }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="updateQty(item.product_id, -1)" class="w-7 h-7 bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition font-bold">−</button>
                                <span class="w-8 text-center text-sm font-medium text-brand-900">{{ item.quantity }}</span>
                                <button @click="updateQty(item.product_id, 1)" class="w-7 h-7 bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition font-bold">+</button>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-brand-900 text-sm">৳{{ item.price * item.quantity }}</div>
                                <button @click="removeItem(item.product_id)" class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Details -->
                    <form @submit.prevent="submitOrder" class="bg-white rounded-2xl card-shadow border border-brand-100 p-6 space-y-5">
                        <h2 class="font-serif font-bold text-brand-900 text-lg mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-sm">2</span>
                            Delivery Details
                        </h2>

                        <!-- Branch selector (both pickup and home delivery) -->
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">
                                {{ form.delivery_type === 'pickup' ? 'Select Branch for Pickup' : 'Select Outlet for Delivery' }}
                            </label>
                            <select v-model="form.branch_id" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                                <option value="">Choose an outlet...</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                            </select>
                        </div>

                        <!-- Delivery Area (home delivery only) -->
                        <div v-if="form.delivery_type === 'home_delivery'">
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Select Your Area</label>
                            <select v-model="form.delivery_area_id" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                                <option value="">Choose your area...</option>
                                <optgroup label="📍 Satkhira Sadar (৳100)">
                                    <option v-for="area in sadarAreas" :key="area.id" :value="area.id">{{ area.name }}</option>
                                </optgroup>
                                <optgroup label="🛵 Outside Sadar - Upazilas (৳200)">
                                    <option v-for="area in outsideAreas" :key="area.id" :value="area.id">{{ area.name }}</option>
                                </optgroup>
                            </select>
                            <div v-if="selectedArea" class="mt-2 bg-cream-100 rounded-lg px-3 py-2 text-sm text-brand-600">
                                Delivery to: <strong>{{ selectedArea.name }}</strong> — Charge: ৳{{ selectedArea.delivery_charge }}
                            </div>
                            <!-- Min order warning -->
                            <div v-if="minOrderError" class="mt-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm text-red-600">
                                ⚠️ {{ minOrderError }}
                            </div>
                        </div>

                        <!-- Validation errors -->
                        <div v-if="Object.keys(errors).length" class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
                            <div v-for="(error, key) in errors" :key="key" class="text-sm text-red-600">{{ error }}</div>
                        </div>

                        <!-- Delivery Type -->
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Delivery Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label :class="form.delivery_type === 'pickup' ? 'border-gold-400 bg-gold-50 text-brand-900' : 'border-brand-100 bg-cream-50 text-brand-600'" class="cursor-pointer border-2 rounded-xl p-4 text-center transition">
                                    <input type="radio" v-model="form.delivery_type" value="pickup" class="hidden" />
                                    <div class="text-2xl mb-1">🏪</div>
                                    <div class="text-sm font-medium">Pickup</div>
                                </label>
                                <label :class="form.delivery_type === 'home_delivery' ? 'border-gold-400 bg-gold-50 text-brand-900' : 'border-brand-100 bg-cream-50 text-brand-600'" class="cursor-pointer border-2 rounded-xl p-4 text-center transition">
                                    <input type="radio" v-model="form.delivery_type" value="home_delivery" class="hidden" />
                                    <div class="text-2xl mb-1">🛵</div>
                                    <div class="text-sm font-medium">Home Delivery</div>
                                </label>
                            </div>
                        </div>

                        <!-- Name & Phone -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Your Name</label>
                                <input v-model="form.customer_name" type="text" required placeholder="Enter your name" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Phone Number</label>
                                <input v-model="form.customer_phone" type="tel" required placeholder="01XXXXXXXXX" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div v-if="form.delivery_type === 'home_delivery'">
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Delivery Address</label>
                            <textarea v-model="form.customer_address" required rows="2" placeholder="Enter your full address" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition"></textarea>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Notes (optional)</label>
                            <textarea v-model="form.notes" rows="2" placeholder="Any special instructions..." class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition"></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-t border-brand-100 pt-4">
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label :class="form.payment_method === 'cash_on_delivery' ? 'border-gold-400 bg-gold-50' : 'border-brand-100 bg-cream-50'" class="cursor-pointer border-2 rounded-xl p-3 text-center transition">
                                    <input type="radio" v-model="form.payment_method" value="cash_on_delivery" class="hidden" />
                                    <div class="text-xl mb-1">💵</div>
                                    <div class="text-xs font-medium text-brand-900">Cash on Delivery</div>
                                </label>
                                <label :class="form.payment_method === 'advance_payment' ? 'border-gold-400 bg-gold-50' : 'border-brand-100 bg-cream-50'" class="cursor-pointer border-2 rounded-xl p-3 text-center transition">
                                    <input type="radio" v-model="form.payment_method" value="advance_payment" class="hidden" />
                                    <div class="text-xl mb-1">💳</div>
                                    <div class="text-xs font-medium text-brand-900">Advance Payment</div>
                                </label>
                            </div>
                        </div>

                        <!-- Advance Payment Details -->
                        <div v-if="form.payment_method === 'advance_payment'" class="bg-gold-50 border-2 border-gold-200 rounded-2xl p-4 space-y-4">
                            <div class="text-center">
                                <h4 class="font-serif font-bold text-brand-900 text-base mb-1">Advance Payment</h4>
                                <p class="text-xs text-brand-500">{{ settings.payment_instructions || 'Send money to our merchant number and enter the transaction ID below.' }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 text-center border border-gold-200">
                                <p class="text-xs text-brand-400 mb-1">Send Money To (Merchant)</p>
                                <p class="font-bold text-brand-900 text-lg">{{ settings.merchant_number || '01722554400' }}</p>
                                <p class="text-xs text-brand-500">{{ settings.merchant_name || 'Lake View Sweets & Bakery' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Advance Amount (৳)</label>
                                <input v-model="form.advance_amount" type="number" min="0" :max="total" placeholder="Enter advance amount" class="w-full rounded-xl border-2 border-gold-200 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-white px-4 py-3 text-brand-900 transition outline-none" />
                                <p class="text-xs text-brand-400 mt-1">Pay partial or full amount in advance. Remaining will be cash on delivery.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Transaction ID</label>
                                <input v-model="form.transaction_id" type="text" placeholder="Enter transaction ID" class="w-full rounded-xl border-2 border-gold-200 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-white px-4 py-3 text-brand-900 transition outline-none" />
                                <p class="text-xs text-brand-400 mt-1">Enter the transaction ID from your payment confirmation.</p>
                            </div>
                        </div>

                        <!-- Coupon -->
                        <div class="border-t border-brand-100 pt-4">
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Coupon Code (optional)</label>
                            <div class="flex gap-2">
                                <input v-model="form.coupon_code" type="text" placeholder="Enter coupon code" class="flex-1 rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                                <button type="button" @click="applyCoupon" class="bg-brand-700 text-white px-5 py-3 rounded-xl text-sm font-bold hover:bg-brand-800 transition">Apply</button>
                            </div>
                            <div v-if="couponMessage" :class="couponSuccess ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="text-sm mt-2 px-3 py-2 rounded-lg">{{ couponMessage }}</div>
                        </div>
                    </form>
                </div>

                <!-- Right: Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl card-shadow border border-brand-100 p-6 sticky top-24">
                        <h2 class="font-serif font-bold text-brand-900 text-lg mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-brand-500">Subtotal ({{ cartItems.length }} items)</span>
                                <span class="font-medium text-brand-900">৳{{ subtotal }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-brand-500">Delivery Charge</span>
                                <span class="font-medium text-brand-900">৳{{ deliveryCharge }}</span>
                            </div>
                            <div v-if="discount > 0" class="flex justify-between text-sm text-green-600">
                                <span>Discount</span>
                                <span class="font-medium">-৳{{ discount }}</span>
                            </div>
                        </div>

                        <div class="border-t border-brand-100 pt-4 mb-4">
                            <div class="flex justify-between font-bold text-xl">
                                <span class="text-brand-900">Total</span>
                                <span class="text-gold-600 font-serif">৳{{ total }}</span>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="bg-cream-100 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-xl">{{ form.payment_method === 'advance_payment' ? '💳' : '💵' }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-brand-900">{{ form.payment_method === 'advance_payment' ? 'Advance Payment' : 'Cash on Delivery' }}</div>
                                    <div class="text-xs text-brand-400">{{ form.payment_method === 'advance_payment' ? 'Pay in advance' : 'Pay when you receive' }}</div>
                                </div>
                            </div>
                            <div v-if="form.payment_method === 'advance_payment' && form.advance_amount" class="border-t border-brand-200 pt-2 mt-2 space-y-1 text-xs">
                                <div class="flex justify-between"><span class="text-brand-500">Advance Paid</span><span class="font-bold text-green-600">৳{{ form.advance_amount }}</span></div>
                                <div class="flex justify-between"><span class="text-brand-500">Remaining (COD)</span><span class="font-bold text-brand-900">৳{{ Math.max(0, total - parseFloat(form.advance_amount || 0)) }}</span></div>
                            </div>
                        </div>

                        <button @click="submitOrder" :disabled="processing || !!minOrderError" class="w-full bg-gold-500 text-brand-950 py-4 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="!processing">Place Order →</span>
                            <span v-else>Processing...</span>
                        </button>

                        <p class="text-xs text-brand-400 text-center mt-3">By placing your order, an account will be auto-created with your phone number for order tracking.</p>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({ branches: Array, deliveryAreas: Array, auth: Object, minOrder: Object });

const cartItems = ref([]);
const processing = ref(false);
const errors = ref({});
const form = ref({
    branch_id: '',
    delivery_type: 'pickup',
    delivery_area_id: '',
    customer_name: '',
    customer_phone: '',
    customer_address: '',
    notes: '',
    coupon_code: '',
    payment_method: 'cash_on_delivery',
    advance_amount: '',
    transaction_id: '',
});
const discount = ref(0);
const couponMessage = ref('');
const couponSuccess = ref(false);

const loadCart = () => {
    try {
        let raw = JSON.parse(localStorage.getItem('cart') || '[]');
        // Filter out items with invalid prices (from old cart before fix)
        raw = raw.filter(item => item.price != null && !isNaN(item.price));
        cartItems.value = raw;
        if (raw.length < JSON.parse(localStorage.getItem('cart') || '[]').length) {
            localStorage.setItem('cart', JSON.stringify(raw));
        }
    } catch { cartItems.value = []; }
};

onMounted(() => {
    loadCart();
    // Auto-fill name/phone if user is logged in
    if (props.auth?.user) {
        form.value.customer_name = props.auth.user.name || '';
        form.value.customer_phone = props.auth.user.phone || '';
    }
});

const selectedBranch = computed(() => props.branches?.find(b => b.id == form.value.branch_id));

// Use global delivery areas
const allDeliveryAreas = computed(() => props.deliveryAreas || []);

const sadarAreas = computed(() => allDeliveryAreas.value.filter(a => a.zone_type === 'sadar'));
const outsideAreas = computed(() => allDeliveryAreas.value.filter(a => a.zone_type === 'outside_sadar'));
const selectedArea = computed(() => allDeliveryAreas.value.find(a => a.id == form.value.delivery_area_id));

const subtotal = computed(() => cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0));
const deliveryCharge = computed(() => {
    if (form.value.delivery_type !== 'home_delivery' || !form.value.delivery_area_id) return 0;
    return selectedArea.value ? parseFloat(selectedArea.value.delivery_charge) : 0;
});
const total = computed(() => Math.max(0, subtotal.value + deliveryCharge.value - discount.value));

const minOrderError = computed(() => {
    if (form.value.delivery_type !== 'home_delivery' || !selectedArea.value) return '';
    const minAmount = selectedArea.value.zone_type === 'sadar'
        ? (props.minOrder?.sadar || 500)
        : (props.minOrder?.outside || 1000);
    if (subtotal.value < minAmount) {
        return `Minimum order for this area is ৳${minAmount}. Your subtotal is ৳${subtotal.value}. Please add ৳${minAmount - subtotal.value} more.`;
    }
    return '';
});

const updateQty = (productId, delta) => {
    const item = cartItems.value.find(i => i.product_id === productId);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        cartItems.value = cartItems.value.filter(i => i.product_id !== productId);
    }
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event('cart-updated'));
};

const removeItem = (productId) => {
    cartItems.value = cartItems.value.filter(item => item.product_id !== productId);
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event('cart-updated'));
};

const applyCoupon = async () => {
    if (!form.value.coupon_code) return;
    try {
        const response = await fetch(route('checkout.apply-coupon'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
            body: JSON.stringify({ code: form.value.coupon_code, subtotal: subtotal.value }),
        });
        const data = await response.json();
        if (data.success) {
            discount.value = data.discount;
            couponSuccess.value = true;
            couponMessage.value = data.message;
        } else {
            discount.value = 0;
            couponSuccess.value = false;
            couponMessage.value = data.message;
        }
    } catch {
        couponMessage.value = 'Failed to apply coupon.';
        couponSuccess.value = false;
    }
};

const submitOrder = () => {
    if (!cartItems.value.length || processing.value) return;
    processing.value = true;
    errors.value = {};
    form.value.items = cartItems.value.map(item => ({ product_id: item.product_id, quantity: item.quantity }));
    router.post(route('checkout.store'), form.value, {
        onSuccess: () => {
            localStorage.removeItem('cart');
            window.dispatchEvent(new Event('cart-updated'));
        },
        onError: (errs) => {
            errors.value = errs;
        },
        onFinish: () => { processing.value = false; },
    });
};
</script>
