<template>
    <AdminLayout active-menu="products" page-title="Products">
        <div class="space-y-5">
            <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                <div>
                    <p class="eyebrow">Catalog control</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Branch-wise products</h2>
                    <p class="mt-2 text-sm text-brand-500">Select the catalog type first, then choose its category and compatible outlets.</p>
                </div>
                <button type="button" @click="openModal()" class="btn-primary !rounded-xl !px-5 !py-3">+ Add product</button>
            </div>

            <div class="flex flex-col gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-soft sm:flex-row">
                <input v-model="search" @input="debouncedSearch" type="search" placeholder="Search products..." class="field-input flex-1 pl-4" />
                <select v-model="categoryFilter" @change="doFilter" class="field-input sm:w-56">
                    <option value="">All categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[780px]">
                        <thead class="border-b border-brand-100 bg-brand-50">
                            <tr><th class="table-head">Product</th><th class="table-head">Category</th><th class="table-head">Base price</th><th class="table-head">Outlets</th><th class="table-head">Status</th><th class="table-head">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-cream-50">
                                <td class="table-cell"><div class="flex items-center gap-3"><div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-brand-50"><img v-if="product.image" :src="assetUrl(product.image)" :alt="product.name" class="h-full w-full object-cover" /><span v-else class="text-xs font-bold text-brand-500">Cake</span></div><div><p class="font-semibold text-brand-900">{{ product.name }}</p><p class="text-xs text-brand-400">#{{ product.id }}</p></div></div></td>
                                <td class="table-cell"><span class="font-semibold text-brand-700">{{ product.category?.name }}</span><span v-if="product.category?.business_type && product.category.business_type !== 'both'" class="mt-1 block text-xs text-brand-400">{{ businessTypeLabel(product.category.business_type) }}</span></td>
                                <td class="table-cell font-semibold text-brand-700">৳{{ money(product.effective_price) }}</td>
                                <td class="table-cell"><span class="rounded-full bg-sage-50 px-3 py-1.5 text-xs font-bold text-sage-700">{{ product.branches_count || product.branches?.length || 0 }} outlets</span></td>
                                <td class="table-cell"><span :class="product.is_available ? 'status-success' : 'status-danger'">{{ product.is_available ? 'Available' : 'Hidden' }}</span></td>
                                <td class="table-cell whitespace-nowrap"><button type="button" @click="openModal(product)" class="mr-4 text-sm font-semibold text-brand-600">Edit</button><button type="button" @click="deleteProduct(product)" class="text-sm font-semibold text-red-500">Delete</button></td>
                            </tr>
                            <tr v-if="!products.data?.length"><td colspan="6" class="p-12 text-center text-sm text-brand-500">No products found.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="products.links?.length > 1" class="flex justify-center gap-2 border-t border-brand-100 p-4"><Link v-for="(link, index) in products.links" :key="index" :href="link.url || '#'" :class="link.active ? 'border-brand-700 bg-brand-700 text-white' : 'border-brand-200 bg-white text-brand-700'" class="rounded-xl border px-3 py-2 text-xs font-bold" v-html="link.label" :preserve-scroll="true" /></div>
            </section>
        </div>

        <Transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-brand-950/60 p-4 backdrop-blur-sm" @click.self="showModal = false">
                <section class="max-h-[94vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-cream-50 p-5 shadow-2xl sm:p-8">
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div><p class="eyebrow">Catalog item</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">{{ editing ? 'Edit product' : 'Add product' }}</h3><p class="mt-1 text-sm text-brand-500">Pharmacy products stay separate from food and bakery products.</p></div>
                        <button type="button" @click="showModal = false" class="icon-button bg-brand-100">×</button>
                    </div>

                    <form @submit.prevent="saveProduct" class="space-y-5">
                        <div class="rounded-2xl border border-brand-200 bg-white p-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="field-label">1. Product type<select v-model="form.catalog_type" @change="onCatalogTypeChange" class="field-input"><option value="">Choose food or pharmacy first</option><option value="food">Food / Bakery</option><option value="pharmacy">Pharmacy / Medicine</option><option value="clinic">Clinic / Dental</option></select><span class="mt-1 block text-xs font-normal text-brand-400">This controls which categories and outlets are available below.</span></label>
                                <div class="rounded-xl bg-brand-50 p-3 text-sm text-brand-700"><p class="font-bold text-brand-900">Separate catalog</p><p class="mt-1 text-xs leading-5">Choose Pharmacy / Medicine for medicines. Food categories will not be available for a pharmacy branch.</p></div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="field-label">Product name<input v-model="form.name" required class="field-input" /></label>
                            <label class="field-label">2. Category<select v-model="form.category_id" required :disabled="!form.catalog_type" class="field-input disabled:cursor-not-allowed disabled:bg-brand-50"><option value="">{{ form.catalog_type ? 'Select matching category' : 'Choose product type first' }}</option><option v-for="category in availableCategories" :key="category.id" :value="category.id">{{ category.name }}{{ category.business_type !== 'both' ? ` · ${businessTypeLabel(category.business_type)}` : '' }}</option></select><span class="mt-1 block text-xs font-normal text-brand-400">{{ selectedCategory ? `Category type: ${businessTypeLabel(selectedCategory.business_type)}` : 'Only compatible categories are shown.' }}</span></label>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2"><label class="field-label">Base price (৳)<input v-model="form.price" type="number" min="0" step="0.01" required class="field-input" /></label><label class="field-label">Base discount price (৳)<input v-model="form.discount_price" type="number" min="0" step="0.01" class="field-input" /></label></div>
                        <label class="field-label">Description<textarea v-model="form.description" rows="3" class="field-input"></textarea></label>
                        <label class="field-label">Cover image<input @change="event => form.image = event.target.files?.[0] || null" type="file" accept="image/*" class="field-input cursor-pointer file:mr-3 file:rounded-full file:border-0 file:bg-brand-100 file:px-3 file:py-1 file:text-xs file:font-bold file:text-brand-700" /></label>

                        <div v-if="isCakeCategory" class="rounded-2xl border border-gold-200 bg-gold-50/70 p-4">
                            <label class="field-label">Cake order mode<select v-model="form.customization_mode" class="field-input"><option value="ready_only">Ready only</option><option value="ready_and_customization">Ready + customization</option><option value="customization_only">Customization only</option></select><span class="mt-1 block text-xs font-normal text-brand-600">Customization-enabled cakes appear in the custom cake flow.</span></label>
                            <div class="mt-4 rounded-2xl border border-gold-200 bg-white/80 p-4"><div class="flex items-start justify-between gap-3"><div><h4 class="font-bold text-brand-900">Cake size, price & images</h4><p class="mt-1 text-xs text-brand-600">Add 500 gm, 1 kg or any custom size. Each size can have its own slider images.</p></div><button type="button" @click="addCakeSize" class="text-xs font-bold text-brand-700">+ Add size</button></div><div v-if="!form.cake_sizes.length" class="mt-3 rounded-xl bg-gold-50 px-3 py-2 text-xs text-brand-600">No size added; the base product price will be used.</div><div v-for="(size, index) in form.cake_sizes" :key="index" class="mt-4 rounded-2xl border border-gold-100 bg-white p-3"><div class="grid gap-2 sm:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)_minmax(0,1fr)_auto]"><input v-model="size.label" class="branch-input" placeholder="500 gm / 1 kg" /><input v-model="size.price" type="number" min="0" step="0.01" class="branch-input" placeholder="Price" /><input v-model="size.discount_price" type="number" min="0" step="0.01" class="branch-input" placeholder="Sale price" /><button type="button" @click="form.cake_sizes.splice(index, 1)" class="rounded-xl px-2 text-red-500">×</button></div><div class="mt-3 rounded-xl border border-dashed border-brand-200 bg-cream-50 p-3"><div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-xs font-bold text-brand-800">Images for {{ size.label || `size ${index + 1}` }}</p><p class="mt-1 text-[11px] text-brand-400">Add multiple images; customers can slide through them.</p></div><label class="cursor-pointer rounded-full bg-brand-100 px-3 py-1.5 text-[11px] font-bold text-brand-700 hover:bg-brand-200">+ Add image<input @change="handleCakeSizeImages($event, index)" type="file" accept="image/*" multiple class="sr-only" /></label></div><div v-if="(size.images?.length || 0) + (size.new_image_previews?.length || 0)" class="mt-3 grid grid-cols-5 gap-2"><div v-for="path in (size.images || [])" :key="path" class="relative aspect-square overflow-hidden rounded-lg border border-brand-200"><img :src="assetUrl(path)" :alt="size.label" class="h-full w-full object-cover" /><button type="button" @click="size.images = size.images.filter(image => image !== path)" class="absolute right-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-red-600 shadow">×</button></div><div v-for="(preview, previewIndex) in (size.new_image_previews || [])" :key="`${preview}-${previewIndex}`" class="relative aspect-square overflow-hidden rounded-lg border border-gold-200"><img :src="preview" :alt="size.label" class="h-full w-full object-cover" /><button type="button" @click="removeCakeSizeImage(index, previewIndex)" class="absolute right-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-red-600 shadow">×</button></div></div></div></div></div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2"><label class="field-label">Delivery options<select v-model="form.delivery_mode" class="field-input"><option value="inherit">Use category setting</option><option value="both">Pickup & Home Delivery</option><option value="pickup">Pickup only</option><option value="home_delivery">Home Delivery only</option></select></label><label class="flex items-start gap-3 rounded-2xl border border-gold-200 bg-gold-50/70 p-4 text-sm font-semibold text-brand-800"><input v-model="form.national_delivery" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-gold-300 text-gold-600" /><span>Bangladesh-wide courier eligible<small class="mt-1 block text-xs font-normal text-brand-600">Only eligible products can be ordered from national courier areas.</small></span></label></div>

                        <div class="rounded-2xl border border-brand-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-4"><div><h4 class="font-bold text-brand-900">3. Select compatible outlets</h4><p class="mt-1 text-xs text-brand-500">Only branches that match the selected product type are shown.</p></div><span class="text-xs font-bold text-brand-500">{{ enabledCount }}/{{ visibleAssignments.length }} selected</span></div>
                            <div v-if="!form.catalog_type" class="mt-4 rounded-xl bg-brand-50 px-3 py-3 text-xs text-brand-600">Choose Food / Bakery or Pharmacy / Medicine above to see compatible outlets.</div>
                            <div v-else-if="!visibleAssignments.length" class="mt-4 rounded-xl bg-red-50 px-3 py-3 text-xs text-red-700">No compatible outlet is available for this catalog type. Add or edit a branch first.</div>
                            <div v-else class="mt-4 space-y-3"><div v-for="assignment in visibleAssignments" :key="assignment.branch_id" class="rounded-xl border p-3" :class="assignment.enabled ? 'border-brand-300 bg-brand-50/60' : 'border-brand-100 bg-cream-50'"><div class="flex items-center gap-3"><input v-model="assignment.enabled" type="checkbox" class="h-4 w-4 rounded border-brand-300 text-brand-600" /><div class="min-w-0 flex-1"><div class="flex items-center gap-2 text-sm font-semibold text-brand-900"><span class="truncate">{{ branchName(assignment.branch_id) }}</span><span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-brand-500">{{ businessTypeLabel(branchType(assignment.branch_id)) }}</span></div><div class="text-[11px] text-brand-400">{{ assignment.is_available ? 'Available for ordering' : 'Hidden from customers' }}</div></div><label v-if="assignment.enabled" class="inline-flex items-center gap-1.5 text-[11px] text-brand-600"><input v-model="assignment.is_available" type="checkbox" class="h-3.5 w-3.5 rounded border-brand-300 text-brand-600" /> Live</label></div><div v-if="assignment.enabled" class="mt-3 grid grid-cols-3 gap-2"><label class="text-[10px] font-bold uppercase tracking-wide text-brand-400">Price<input v-model="assignment.price" type="number" min="0" step="0.01" placeholder="Base" class="branch-input" /></label><label class="text-[10px] font-bold uppercase tracking-wide text-brand-400">Discount<input v-model="assignment.discount_price" type="number" min="0" step="0.01" placeholder="Optional" class="branch-input" /></label><label class="text-[10px] font-bold uppercase tracking-wide text-brand-400">Stock<input v-model="assignment.stock" type="number" min="0" placeholder="Unlimited" class="branch-input" /></label></div></div></div>
                        </div>

                        <div class="rounded-2xl border border-brand-200 bg-white p-4"><div class="flex items-start justify-between gap-3"><div><h4 class="font-bold text-brand-900">Product image slider</h4><p class="mt-1 text-xs text-brand-500">Add multiple images for any food, bakery, pharmacy or cake product.</p></div><span class="text-xs font-bold text-brand-500">{{ form.gallery.length + form.existing_gallery.length }} images</span></div><input @change="handleGalleryFiles" type="file" accept="image/*" multiple class="mt-4 field-input cursor-pointer file:mr-3 file:rounded-full file:border-0 file:bg-brand-100 file:px-3 file:py-1 file:text-xs file:font-bold file:text-brand-700" /><p class="mt-2 text-xs text-brand-400">Select several files or choose more files again. Maximum 4MB per image.</p><p v-if="galleryError" class="mt-2 rounded-xl bg-red-50 px-3 py-2 text-xs text-red-600">{{ galleryError }}</p><div v-if="form.existing_gallery.length || form.gallery.length" class="mt-3 grid grid-cols-5 gap-2"><div v-for="path in form.existing_gallery" :key="path" class="relative aspect-square overflow-hidden rounded-xl border border-brand-200"><img :src="assetUrl(path)" class="h-full w-full object-cover" /><button type="button" @click="removeExistingImage(path)" class="absolute right-1 top-1 h-6 w-6 rounded-full bg-white text-red-600">×</button></div><div v-for="(file, index) in form.gallery" :key="`${file.name}-${file.lastModified}-${index}`" class="relative aspect-square overflow-hidden rounded-xl border border-gold-200"><img :src="galleryPreviews[index]" :alt="file.name" class="h-full w-full object-cover" /><button type="button" @click="removeNewGallery(index)" class="absolute right-1 top-1 h-6 w-6 rounded-full bg-white text-red-600">×</button></div></div></div>
                        <div class="flex flex-wrap gap-4"><label class="inline-flex items-center gap-2 text-sm font-semibold text-brand-700"><input v-model="form.is_available" type="checkbox" class="rounded border-brand-300 text-brand-600" /> Product active</label><label class="inline-flex items-center gap-2 text-sm font-semibold text-brand-700"><input v-model="form.is_featured" type="checkbox" class="rounded border-brand-300 text-brand-600" /> Feature on home</label></div>
                        <div class="flex gap-3 pt-2"><button type="submit" :disabled="saving || !form.catalog_type || !form.category_id || !enabledCount" class="btn-primary flex-1 !rounded-xl disabled:cursor-not-allowed disabled:opacity-60">{{ saving ? 'Saving...' : editing ? 'Save changes' : 'Create product' }}</button><button type="button" @click="showModal = false" class="btn-outline !rounded-xl">Cancel</button></div>
                    </form>
                </section>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({ products: Object, categories: Array, branches: Array, filters: Object });
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const galleryError = ref('');
const search = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category_id || '');
let debounceTimer;

const foodTypes = ['bakery', 'fast_food', 'restaurant'];
const defaultCakeSizes = () => [{ label: '500 gm', price: '', discount_price: '', images: [], new_images: [], new_image_previews: [] }, { label: '1 kg', price: '', discount_price: '', images: [], new_images: [], new_image_previews: [] }];
const emptyAssignments = () => (props.branches || []).map(branch => ({ branch_id: branch.id, enabled: false, price: '', discount_price: '', is_available: true, stock: '' }));
const blankForm = () => ({ name: '', catalog_type: '', category_id: '', price: '', discount_price: '', description: '', image: null, gallery: [], existing_gallery: [], remove_gallery: [], delivery_mode: 'inherit', national_delivery: false, customization_mode: 'ready_only', cake_sizes: [], is_available: true, is_featured: false, sort_order: 0, branch_assignments: emptyAssignments() });
const form = ref(blankForm());
const galleryPreviews = ref([]);

const assetUrl = path => path?.startsWith('http') ? path : '/storage/' + path;
const money = value => Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const businessTypeLabel = type => ({ bakery: 'Bakery', fast_food: 'Fast food', restaurant: 'Restaurant', pharmacy: 'Pharmacy', clinic: 'Clinic / dental', both: 'All types' }[type] || 'All types');
const branch = id => (props.branches || []).find(item => Number(item.id) === Number(id));
const branchName = id => branch(id)?.name || 'Outlet';
const branchType = id => branch(id)?.business_type || 'both';
const selectedCategory = computed(() => (props.categories || []).find(category => Number(category.id) === Number(form.value.category_id)));
const isCakeCategory = computed(() => String(selectedCategory.value?.name || '').toLowerCase().includes('cake'));
const selectedBranchTypes = computed(() => form.value.branch_assignments.filter(assignment => assignment.enabled).map(assignment => branchType(assignment.branch_id)).filter(Boolean));

const categoryMatchesCatalogType = category => {
    const categoryType = category.business_type || 'both';
    if (!form.value.catalog_type) return true;
    if (form.value.catalog_type === 'food') return foodTypes.includes(categoryType) || categoryType === 'both';
    return categoryType === form.value.catalog_type;
};

const categoryMatchesBranchType = (category, type) => {
    const categoryType = category.business_type || 'both';
    if (type === 'both') return categoryType !== 'pharmacy' && categoryType !== 'clinic';
    if (type === 'pharmacy' || type === 'clinic') return categoryType === type;
    return foodTypes.includes(type) && (foodTypes.includes(categoryType) || categoryType === 'both');
};

const availableCategories = computed(() => (props.categories || []).filter(category => categoryMatchesCatalogType(category) && (!selectedBranchTypes.value.length || selectedBranchTypes.value.every(type => categoryMatchesBranchType(category, type)))));

const branchMatchesSelection = item => {
    if (!item) return false;
    const selectedType = selectedCategory.value?.business_type && selectedCategory.value.business_type !== 'both' ? selectedCategory.value.business_type : form.value.catalog_type;
    const type = item.business_type || 'both';
    if (!selectedType) return false;
    if (selectedType === 'food') return type === 'both' || foodTypes.includes(type);
    return type === selectedType;
};

const visibleAssignments = computed(() => form.value.branch_assignments.filter(assignment => branchMatchesSelection(branch(assignment.branch_id))));
const enabledCount = computed(() => visibleAssignments.value.filter(assignment => assignment.enabled).length);
const debouncedSearch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(doFilter, 350); };
const doFilter = () => router.get(route('admin.products.index'), { search: search.value, category_id: categoryFilter.value }, { preserveState: true, preserveScroll: true });
const onCatalogTypeChange = () => { if (form.value.category_id && !availableCategories.value.some(category => Number(category.id) === Number(form.value.category_id))) form.value.category_id = ''; };

watch(isCakeCategory, isCake => { if (isCake && !form.value.cake_sizes.length) form.value.cake_sizes = defaultCakeSizes(); if (!isCake) form.value.cake_sizes = []; });
watch([availableCategories, visibleAssignments], ([categories, assignments]) => {
    if (form.value.category_id && !categories.some(category => Number(category.id) === Number(form.value.category_id))) form.value.category_id = '';
    const allowedIds = new Set(assignments.map(assignment => Number(assignment.branch_id)));
    form.value.branch_assignments.forEach(assignment => { if (!allowedIds.has(Number(assignment.branch_id))) assignment.enabled = false; });
});

const openModal = product => {
    editing.value = product;
    galleryError.value = '';
    galleryPreviews.value = [];
    if (!product) { form.value = blankForm(); showModal.value = true; return; }
    const productBranches = Object.fromEntries((product.branches || []).map(item => [item.id, item]));
    const productType = product.category?.business_type === 'pharmacy' ? 'pharmacy' : product.category?.business_type === 'clinic' ? 'clinic' : 'food';
    form.value = { name: product.name, catalog_type: productType, category_id: product.category_id, price: product.price, discount_price: product.discount_price || '', description: product.description || '', image: null, gallery: [], existing_gallery: product.gallery || [], remove_gallery: [], delivery_mode: product.delivery_mode || 'inherit', national_delivery: !!product.national_delivery, customization_mode: product.customization_mode || 'ready_only', cake_sizes: (product.cake_sizes || []).map(size => ({ ...size, images: size.images || [], new_images: [], new_image_previews: [] })), is_available: !!product.is_available, is_featured: !!product.is_featured, sort_order: product.sort_order || 0, branch_assignments: (props.branches || []).map(item => { const row = productBranches[item.id]; const pivot = row?.pivot || {}; return { branch_id: item.id, enabled: !!row, price: pivot.price ?? '', discount_price: pivot.discount_price ?? '', is_available: pivot.is_available === undefined ? true : !!pivot.is_available, stock: pivot.stock ?? '' }; }) };
    showModal.value = true;
};

const addCakeSize = () => form.value.cake_sizes.push({ label: '', price: '', discount_price: '', images: [], new_images: [], new_image_previews: [] });
const handleGalleryFiles = event => { const files = Array.from(event.target.files || []); const invalid = files.find(file => !file.type.startsWith('image/') || file.size > 4 * 1024 * 1024); galleryError.value = invalid ? 'Each gallery image must be smaller than 4MB.' : ''; if (!invalid) { form.value.gallery = [...form.value.gallery, ...files]; galleryPreviews.value = [...galleryPreviews.value, ...files.map(file => URL.createObjectURL(file))]; } event.target.value = ''; };
const removeNewGallery = index => { form.value.gallery.splice(index, 1); galleryPreviews.value.splice(index, 1); };
const handleCakeSizeImages = (event, index) => { const files = Array.from(event.target.files || []); const invalid = files.find(file => !file.type.startsWith('image/') || file.size > 4 * 1024 * 1024); galleryError.value = invalid ? 'Each cake size image must be smaller than 4MB.' : ''; if (!invalid) { const size = form.value.cake_sizes[index]; size.new_images = [...(size.new_images || []), ...files]; size.new_image_previews = [...(size.new_image_previews || []), ...files.map(file => URL.createObjectURL(file))]; } event.target.value = ''; };
const removeCakeSizeImage = (sizeIndex, imageIndex) => { const size = form.value.cake_sizes[sizeIndex]; size.new_images.splice(imageIndex, 1); size.new_image_previews.splice(imageIndex, 1); };
const removeExistingImage = path => { form.value.existing_gallery = form.value.existing_gallery.filter(item => item !== path); form.value.remove_gallery.push(path); };
const saveProduct = () => {
    if (galleryError.value || !form.value.catalog_type || !form.value.category_id || !enabledCount.value) return;
    saving.value = true;
    const data = new FormData();
    ['name', 'category_id', 'price', 'discount_price', 'description', 'sort_order', 'delivery_mode', 'customization_mode'].forEach(key => { if (form.value[key] !== null && form.value[key] !== '') data.append(key, form.value[key]); });
    data.append('cake_sizes', JSON.stringify(isCakeCategory.value ? form.value.cake_sizes.map(size => ({ label: size.label, price: size.price, discount_price: size.discount_price, images: size.images || [] })) : []));
    data.append('national_delivery', form.value.national_delivery ? '1' : '0');
    data.append('is_available', form.value.is_available ? '1' : '0');
    data.append('is_featured', form.value.is_featured ? '1' : '0');
    data.append('branch_assignments', JSON.stringify(form.value.branch_assignments.filter(assignment => assignment.enabled)));
    data.append('remove_gallery', JSON.stringify(form.value.remove_gallery));
    if (form.value.image) data.append('image', form.value.image);
    form.value.gallery.forEach(file => data.append('gallery[]', file));
    if (isCakeCategory.value) form.value.cake_sizes.forEach((size, index) => (size.new_images || []).forEach(file => data.append(`cake_size_images[${index}][]`, file)));
    const options = { forceFormData: true, onFinish: () => saving.value = false, onSuccess: () => showModal.value = false };
    if (editing.value) { data.append('_method', 'PUT'); router.post(route('admin.products.update', editing.value.id), data, options); } else router.post(route('admin.products.store'), data, options);
};
const deleteProduct = product => { if (confirm(`Delete ${product.name}?`)) router.delete(route('admin.products.destroy', product.id)); };
</script>
