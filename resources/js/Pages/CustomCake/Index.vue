<template>
    <CustomerLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <span class="text-gold-600 text-sm tracking-widest uppercase font-medium">Made to Order</span>
                <h1 class="font-serif text-3xl font-bold text-brand-900 mt-2">Custom Cake Order</h1>
                <div class="w-20 h-1 bg-gold-500 mx-auto mt-4 rounded-full"></div>
                <p class="text-brand-500 mt-4 max-w-2xl mx-auto">{{ settings.custom_cake_info || 'Upload your design and we will create the cake exactly as you want it.' }}</p>
            </div>

            <form @submit.prevent="submitOrder" class="bg-white rounded-2xl card-shadow border border-brand-100 p-6 sm:p-8 space-y-6">
                <!-- Delivery Info -->
                <div>
                    <h2 class="font-serif font-bold text-brand-900 text-lg mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-sm">1</span>
                        Delivery Details
                    </h2>

                    <!-- Delivery Type -->
                    <div class="mb-4">
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

                    <!-- Branch for pickup -->
                    <div v-if="form.delivery_type === 'pickup'" class="mb-4">
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Select Branch for Pickup</label>
                        <select v-model="form.branch_id" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                            <option value="">Choose a branch...</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                        </select>
                    </div>

                    <!-- Delivery area for home delivery -->
                    <div v-if="form.delivery_type === 'home_delivery'" class="mb-4">
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
                    </div>

                    <!-- Name & Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Your Name</label>
                            <input v-model="form.customer_name" type="text" required placeholder="Enter your name" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Phone Number</label>
                            <input v-model="form.customer_phone" type="tel" required placeholder="01XXXXXXXXX" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        </div>
                    </div>

                    <!-- Address for home delivery -->
                    <div v-if="form.delivery_type === 'home_delivery'" class="mb-4">
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Delivery Address</label>
                        <textarea v-model="form.customer_address" required rows="2" placeholder="Enter your full address" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition"></textarea>
                    </div>
                </div>

                <!-- Cake Details -->
                <div class="border-t border-brand-100 pt-6">
                    <h2 class="font-serif font-bold text-brand-900 text-lg mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-sm">2</span>
                        Cake Details
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Cake Type</label>
                            <input v-model="form.cake_type" type="text" placeholder="e.g. Birthday" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Cake Size</label>
                            <select v-model="form.cake_size" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                                <option value="">Select size...</option>
                                <option>1/2 kg</option><option>1 kg</option><option>1.5 kg</option><option>2 kg</option><option>2.5 kg</option><option>3 kg</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Flavor</label>
                            <select v-model="form.cake_flavor" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                                <option value="">Select flavor...</option>
                                <option>Vanilla</option><option>Chocolate</option><option>Strawberry</option><option>Black Forest</option><option>White Forest</option><option>Red Velvet</option><option>Blueberry</option><option>Lemon</option><option>Orange</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Message on Cake</label>
                        <input v-model="form.message_on_cake" type="text" placeholder="e.g. Happy Birthday John!" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Delivery Date</label>
                            <input v-model="form.delivery_date" type="date" required :min="minDate" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-700 mb-1.5">Preferred Time</label>
                            <input v-model="form.delivery_time" type="time" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Upload Design Image</label>
                        <div class="border-2 border-dashed border-brand-200 rounded-xl p-6 text-center hover:border-gold-400 transition cursor-pointer" @click="$refs.fileInput.click()">
                            <input ref="fileInput" @change="handleFile" type="file" accept="image/*" class="hidden" />
                            <div v-if="designImage" class="text-brand-700">
                                <div class="text-3xl mb-2">✅</div>
                                <div class="text-sm font-medium">{{ designImage.name }}</div>
                                <div class="text-xs text-brand-400 mt-1">Click to change</div>
                            </div>
                            <div v-else class="text-brand-400">
                                <div class="text-4xl mb-2">📸</div>
                                <div class="text-sm">Click to upload your cake design (max 5MB)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Additional Notes</label>
                        <textarea v-model="form.notes" rows="3" placeholder="Any special instructions..." class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition"></textarea>
                    </div>
                </div>

                <!-- Summary -->
                <div class="border-t border-brand-100 pt-6">
                    <div class="bg-cream-100 rounded-xl p-4 flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-xl">💵</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-brand-900">Cash on Delivery</div>
                            <div class="text-xs text-brand-400">Price will be confirmed by our team after reviewing your design</div>
                        </div>
                    </div>

                    <div v-if="form.delivery_type === 'home_delivery' && selectedArea" class="flex justify-between text-sm mb-2">
                        <span class="text-brand-500">Delivery Charge</span>
                        <span class="font-medium text-brand-900">৳{{ selectedArea.delivery_charge }}</span>
                    </div>

                    <button type="submit" :disabled="processing" class="w-full bg-gold-500 text-brand-950 py-4 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg disabled:opacity-50">
                        <span v-if="!processing">Submit Custom Cake Order →</span>
                        <span v-else>Processing...</span>
                    </button>
                    <p class="text-xs text-brand-400 text-center mt-3">An account will be auto-created with your phone number for order tracking.</p>
                </div>
            </form>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ branches: Array, deliveryAreas: Array, auth: Object });
const page = usePage();
const settings = computed(() => page.props.settings || {});

const processing = ref(false);
const form = ref({
    branch_id: '', delivery_type: 'pickup', delivery_area_id: '',
    customer_name: '', customer_phone: '', customer_address: '',
    cake_type: '', cake_size: '', cake_flavor: '', message_on_cake: '',
    delivery_date: '', delivery_time: '', notes: '',
});

// Auto-fill name/phone if user is logged in
if (props.auth?.user) {
    form.value.customer_name = props.auth.user.name || '';
    form.value.customer_phone = props.auth.user.phone || '';
}

const designImage = ref(null);
const minDate = computed(() => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().split('T')[0];
});

// Use global delivery areas
const allDeliveryAreas = computed(() => props.deliveryAreas || []);

const sadarAreas = computed(() => allDeliveryAreas.value.filter(a => a.zone_type === 'sadar'));
const outsideAreas = computed(() => allDeliveryAreas.value.filter(a => a.zone_type === 'outside_sadar'));
const selectedArea = computed(() => allDeliveryAreas.value.find(a => a.id == form.value.delivery_area_id));

const handleFile = (e) => { designImage.value = e.target.files[0]; };

const submitOrder = () => {
    processing.value = true;
    const formData = new FormData();
    Object.keys(form.value).forEach(key => formData.append(key, form.value[key]));
    if (designImage.value) formData.append('design_image', designImage.value);
    router.post(route('custom-cake.store'), formData, {
        forceFormData: true,
        onFinish: () => { processing.value = false; },
    });
};
</script>
