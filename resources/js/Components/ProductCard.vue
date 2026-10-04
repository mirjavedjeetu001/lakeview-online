<template>
    <article class="product-card group min-w-0 rounded-[1.4rem] border border-brand-100 bg-white overflow-hidden shadow-soft hover:-translate-y-1 hover:shadow-card transition">
        <button type="button" @click="openQuickView" class="block relative w-full text-left">
            <div class="product-card-media aspect-[.95] bg-brand-50 flex items-center justify-center overflow-hidden">
                <ImageCarousel v-if="productImages.length" :images="productImages" :alt="product.name" :fallback="emoji" image-class="h-full w-full object-contain p-2 sm:p-3 group-hover:scale-105 transition duration-500" />
                <span v-else class="text-6xl">{{ emoji }}</span>
            </div>
            <span v-if="hasDiscount" class="absolute top-3 left-3 rounded-full bg-brand-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white">Sale</span>
            <span class="absolute bottom-3 right-3 rounded-full bg-white/90 px-3 py-1 text-[10px] font-bold text-brand-700 shadow-soft opacity-0 transition group-hover:opacity-100">Quick view</span>
        </button>
        <div class="p-3.5 sm:p-4">
            <button type="button" @click="openQuickView" class="block w-full text-left">
                <h3 class="font-semibold text-brand-900 text-sm line-clamp-1 group-hover:text-brand-500 transition">{{ product.name }}</h3>
                <p class="text-[11px] text-brand-400 mt-1">{{ product.category?.name }}</p>
            </button>
            <div class="flex items-center justify-between gap-2 mt-4">
                <div>
                    <span class="font-serif font-bold text-lg text-brand-700">{{ isCake && sizeOptions.length ? 'From ' : '' }}৳{{ money(startingPrice) }}</span>
                    <span v-if="hasDiscount && !isCake" class="ml-1 text-[10px] text-brand-300 line-through">৳{{ money(product.branch_price || product.price) }}</span>
                </div>
                <button type="button" @click.stop="isCake || customizationOnly ? openQuickView() : addToBag(1)" class="add-button" :aria-label="isCake || customizationOnly ? 'Choose cake options' : 'Add to bag'">{{ isCake || customizationOnly ? '↗' : '+' }}</button>
            </div>
        </div>
    </article>

    <Teleport to="body">
        <Transition name="fade">
            <div v-if="quickViewOpen || bagModalOpen" class="fixed inset-0 z-[120] flex items-center justify-center bg-brand-950/60 p-3 sm:p-5 backdrop-blur-sm" @click.self="closeModals">
                <div class="relative w-full max-w-3xl max-h-[92vh] overflow-y-auto rounded-[2rem] bg-cream-50 shadow-2xl">
                    <button type="button" @click="closeModals" class="absolute right-4 top-4 z-10 icon-button bg-white/90 shadow-soft" aria-label="Close modal">×</button>

                    <div v-if="quickViewOpen" class="grid md:grid-cols-2">
                        <div class="aspect-square bg-brand-50 p-4 sm:p-7">
                            <div class="h-full overflow-hidden rounded-[1.5rem] border border-brand-100 bg-white">
                                <ImageCarousel :images="productImages" :alt="product.name" :fallback="emoji" image-class="h-full w-full object-cover" />
                            </div>
                        </div>
                        <div class="p-6 sm:p-8 flex flex-col">
                            <p class="eyebrow">{{ product.category?.name || 'From the bakery' }}</p>
                            <h2 class="mt-3 font-serif text-3xl sm:text-4xl font-bold tracking-tight text-brand-900">{{ product.name }}</h2>
                            <div class="mt-5 flex items-end gap-3">
                                <span class="font-serif text-3xl font-bold text-brand-600">৳{{ money(displayPrice) }}</span>
                                <span v-if="hasDiscount && !selectedSize" class="mb-1 text-sm text-brand-300 line-through">৳{{ money(product.branch_price || product.price) }}</span>
                            </div>
                            <p v-if="isCake && sizeOptions.length" class="mt-1 text-xs leading-5 text-brand-500">{{ customizationOnly ? 'Base cake estimate; final custom price is confirmed by our team.' : 'Ready-cake price for the selected size.' }}</p>
                            <p class="mt-5 text-sm leading-7 text-brand-600">{{ product.description || 'Freshly prepared with care in our local bakery.' }}</p>

                            <div v-if="isCake && sizeOptions.length" class="mt-5 rounded-2xl border border-gold-200 bg-gold-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-xs font-bold uppercase tracking-[.14em] text-brand-600">Choose cake size</label>
                                    <span class="text-xs font-bold text-gold-700">{{ sizeOptions.length }} options</span>
                                </div>
                                <select v-model="selectedSizeLabel" class="field-input mt-2">
                                    <option v-for="size in sizeOptions" :key="size.label" :value="size.label">{{ size.label }} · ৳{{ money(sizePrice(size)) }}</option>
                                </select>
                                <p class="mt-2 text-xs text-brand-500">Selected size: <strong>{{ selectedSize?.label }}</strong></p>
                            </div>

                            <div v-if="customizationOnly" class="mt-4 rounded-2xl border border-brand-200 bg-brand-50 p-4 text-xs leading-5 text-brand-700">
                                <strong class="text-brand-900">Customization required</strong>
                                <p class="mt-1">Choose the size, flavour and design on the custom cake form.</p>
                            </div>

                            <div class="mt-5 rounded-2xl border border-brand-100 bg-white p-4 text-sm text-brand-700">
                                <div class="flex items-center gap-2 font-semibold"><span class="h-2 w-2 rounded-full bg-sage-400"></span>Fresh and available for your outlet</div>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                    <span v-if="product.allow_home_delivery !== false" class="rounded-full bg-sage-50 px-3 py-1.5 text-sage-700">Home delivery</span>
                                    <span v-if="product.allow_pickup !== false" class="rounded-full bg-gold-50 px-3 py-1.5 text-gold-600">Pickup available</span>
                                </div>
                            </div>

                            <div class="mt-auto flex items-center gap-3 pt-6">
                                <div class="flex items-center rounded-full border border-brand-200 bg-white p-1">
                                    <button type="button" @click="quantity > 1 && quantity--" class="h-9 w-9 rounded-full text-brand-600 hover:bg-brand-100">−</button>
                                    <span class="w-9 text-center text-sm font-bold">{{ quantity }}</span>
                                    <button type="button" @click="quantity++" class="h-9 w-9 rounded-full text-brand-600 hover:bg-brand-100">+</button>
                                </div>
                                <Link v-if="customizationOnly" :href="customizationHref" class="btn-primary flex-1 text-center">Customize this cake <span>→</span></Link>
                                <button v-else type="button" @click="addToBag(quantity)" class="btn-primary flex-1">Add to bag <span>→</span></button>
                            </div>
                            <Link v-if="isCake && product.customization_mode === 'ready_and_customization'" :href="customizationHref" class="btn-outline mt-3 w-full whitespace-normal text-center">Need a custom design? Open cake request <span>→</span></Link>
                        </div>
                    </div>

                    <div v-else class="p-7 sm:p-10 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sage-50 text-3xl text-sage-600">✓</div>
                        <p class="eyebrow mt-6">Saved for later</p>
                        <h2 class="mt-3 font-serif text-3xl font-bold text-brand-900">Added to your bag</h2>
                        <div class="mx-auto mt-6 flex max-w-sm items-center gap-3 rounded-2xl border border-brand-100 bg-white p-3 text-left">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-brand-50 flex items-center justify-center"><img v-if="product.image" :src="assetUrl(product.image)" :alt="product.name" class="h-full w-full object-cover" /><span v-else class="text-3xl">{{ emoji }}</span></div>
                            <div class="min-w-0"><div class="truncate font-bold text-brand-900">{{ product.name }}</div><div class="mt-1 text-xs text-brand-500">{{ addedQuantity }} item{{ addedQuantity > 1 ? 's' : '' }}<span v-if="addedCakeSize"> · {{ addedCakeSize }}</span> · ৳{{ money(addedUnitPrice * addedQuantity) }}</div></div>
                        </div>
                        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                            <button type="button" @click="closeModals" class="btn-outline">Continue shopping</button>
                            <Link :href="route('checkout.index')" class="btn-primary">View my bag <span>→</span></Link>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ImageCarousel from '@/Components/ImageCarousel.vue';

const props = defineProps({ product: Object });
const emit = defineEmits(['add']);
const quickViewOpen = ref(false);
const bagModalOpen = ref(false);
const quantity = ref(1);
const addedQuantity = ref(1);
const addedUnitPrice = ref(0);
const addedCakeSize = ref('');

const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;
const money = (value) => Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const productImages = computed(() => [props.product.image, ...(props.product.gallery || [])].filter(Boolean));
const hasDiscount = computed(() => props.product.branch_discount_price !== null && props.product.branch_discount_price !== undefined ? Number(props.product.branch_discount_price) > 0 : Number(props.product.discount_price) > 0);
const emoji = computed(() => ({ Cake: '🎂', Bread: '🍞', Cookies: '🍪', Sweets: '🍬', 'Fast Food': '🥪', Dessert: '🍮' }[props.product.category?.name] || '🍰'));
const isCake = computed(() => /cake/i.test(`${props.product.category?.name || ''} ${props.product.name || ''}`) || (props.product.cake_sizes || []).length > 0);
const sizeOptions = computed(() => isCake.value ? (props.product.cake_sizes || []) : []);
const selectedSizeLabel = ref(sizeOptions.value[0]?.label || '');
const selectedSize = computed(() => sizeOptions.value.find(size => size.label === selectedSizeLabel.value));
const sizePrice = size => Number(size?.discount_price || 0) > 0 ? Number(size.discount_price) : Number(size?.price || 0);
const displayPrice = computed(() => selectedSize.value ? sizePrice(selectedSize.value) : Number(props.product.effective_price || 0));
const startingPrice = computed(() => sizeOptions.value.length ? Math.min(...sizeOptions.value.map(sizePrice)) : Number(props.product.effective_price || 0));
const customizationOnly = computed(() => props.product.customization_mode === 'customization_only');
const customizationHref = computed(() => route('custom-cake.index', { product: props.product.id }));

const openQuickView = () => {
    quantity.value = 1;
    selectedSizeLabel.value = sizeOptions.value[0]?.label || '';
    quickViewOpen.value = true;
    bagModalOpen.value = false;
};

const closeModals = () => {
    quickViewOpen.value = false;
    bagModalOpen.value = false;
};

const addToBag = (amount = 1) => {
    const finalAmount = Math.max(1, Number(amount) || 1);
    emit('add', props.product, finalAmount, selectedSize.value || null);
    addedQuantity.value = finalAmount;
    addedUnitPrice.value = displayPrice.value;
    addedCakeSize.value = selectedSize.value?.label || '';
    quickViewOpen.value = false;
    bagModalOpen.value = true;
};
</script>
