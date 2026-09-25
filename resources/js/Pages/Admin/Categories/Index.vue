<template>
    <AdminLayout active-menu="categories" page-title="Categories">
        <div class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Catalog organization</p>
                    <h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Categories</h2>
                    <p class="text-sm text-brand-500 mt-2">Keep the bakery menu easy to browse on every screen.</p>
                </div>
                <button @click="openModal()" class="rounded-full bg-brand-700 px-5 py-3 text-sm font-bold text-white hover:bg-brand-600 transition">+ Add category</button>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
                <div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-900">Category directory</h3>
                        <p class="mt-1 text-sm text-brand-400">Organize products into easy-to-browse menu groups.</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gold-50 px-3 py-1.5 text-xs font-bold text-gold-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                        {{ categories.length }} {{ categories.length === 1 ? 'category' : 'categories' }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr>
                                <th class="table-head">Category</th>
                                <th class="table-head">Products</th>
                                <th class="table-head">Delivery</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="cat in categories" :key="cat.id" class="transition hover:bg-cream-50/80">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-brand-100 bg-cream-50 shadow-sm">
                                            <img v-if="cat.image" :src="assetUrl(cat.image)" :alt="cat.name" class="w-full h-full object-cover" />
                                            <span v-else class="text-xl">🍰</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-brand-900">{{ cat.name }}</p>
                                            <p class="mt-0.5 max-w-sm truncate text-xs text-brand-400">{{ cat.description || 'No description added' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700">
                                        <svg class="h-3.5 w-3.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7 12 3 4 7v10l8 4 8-4V7Zm-8 0v14m8-14-8 5-8-5" /></svg>
                                        {{ cat.products_count || 0 }} {{ (cat.products_count || 0) === 1 ? 'product' : 'products' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-cream-50 px-3 py-1.5 text-xs font-semibold text-brand-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ deliveryLabel(cat.delivery_mode) }}
                                    </span>
                                </td>
                                <td class="table-cell"><span :class="cat.is_active ? 'status-success' : 'status-danger'">{{ cat.is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="table-cell whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openModal(cat)" class="text-brand-600">Edit</button>
                                        <button type="button" @click="deleteCategory(cat)" class="text-red-500">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!categories?.length">
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7 12 3 4 7v10l8 4 8-4V7Zm-8 0v14m8-14-8 5-8-5" /></svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-brand-800">No categories yet</p>
                                    <p class="mt-1 text-sm text-brand-400">Add a category to start organizing your bakery products.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <Transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-brand-950/60 p-4 backdrop-blur-sm" @click.self="showModal = false">
                <section class="max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-brand-100 bg-white shadow-2xl">
                    <div class="flex items-start justify-between gap-4 border-b border-brand-100 bg-cream-50/80 p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gold-100 text-gold-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 15.5c-.5 0-1 .15-1.5.5a2.7 2.7 0 0 1-3 0 2.7 2.7 0 0 0-3 0 2.7 2.7 0 0 1-3 0 2.7 2.7 0 0 0-3 0 2.7 2.7 0 0 1-3 0A2.7 2.7 0 0 0 2 15.5M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v7h18Z" /></svg>
                            </span>
                            <div>
                                <p class="eyebrow">Menu organization</p>
                                <h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">{{ editing ? 'Edit category' : 'Create a category' }}</h3>
                                <p class="mt-1 text-sm leading-6 text-brand-500">Set up how this group appears in your bakery menu.</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="icon-button shrink-0" aria-label="Close form">×</button>
                    </div>

                    <form @submit.prevent="saveCategory" class="space-y-5 p-6 sm:p-7">
                        <label class="field-label">Category name
                            <input v-model="form.name" type="text" required placeholder="e.g. Celebration cakes" class="field-input" />
                        </label>

                        <label class="field-label">Description <span class="font-normal text-brand-400">(optional)</span>
                            <textarea v-model="form.description" rows="3" placeholder="A short description to help customers browse..." class="field-input min-h-24 resize-y"></textarea>
                        </label>

                        <div class="space-y-2">
                            <label class="field-label">Category image <span class="font-normal text-brand-400">(optional)</span></label>
                            <div v-if="editing?.image" class="flex items-center gap-3 rounded-2xl border border-brand-100 bg-cream-50 p-3">
                                <img :src="assetUrl(editing.image)" :alt="editing.name" class="h-14 w-14 rounded-xl object-cover" />
                                <div class="min-w-0"><p class="text-sm font-bold text-brand-800">Current category image</p><p class="mt-0.5 text-xs text-brand-400">Upload another image below to replace it.</p></div>
                            </div>
                            <input @change="handleFile" type="file" accept="image/*" class="field-input cursor-pointer bg-white py-2.5 file:mr-3 file:rounded-full file:border-0 file:bg-brand-100 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-brand-700" />
                            <p class="text-xs text-brand-400">{{ form.image?.name || 'JPG, PNG or WEBP · maximum 2MB' }}</p>
                            <div v-if="fileError" role="alert" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 text-sm font-medium text-red-700">{{ fileError }}</div>
                        </div>

                        <label class="field-label">Delivery options
                            <select v-model="form.delivery_mode" class="field-input"><option value="both">Pickup & Home Delivery</option><option value="pickup">Pickup preferred</option><option value="home_delivery">Home Delivery only</option></select>
                            <span class="mt-1 block text-xs font-normal leading-5 text-brand-400">Home delivery remains available for every product; this controls pickup availability.</span>
                        </label>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="field-label">Sort order
                                <input v-model="form.sort_order" type="number" min="0" placeholder="0" class="field-input" />
                            </label>
                            <label class="flex cursor-pointer items-center justify-between gap-3 self-end rounded-2xl border border-brand-100 bg-cream-50 px-4 py-3.5 transition hover:border-brand-200">
                                <span><span class="block text-sm font-bold text-brand-800">Active category</span><span class="mt-0.5 block text-xs text-brand-400">Visible to customers</span></span>
                                <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-brand-300 text-gold-500 focus:ring-gold-300" />
                            </label>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-5 sm:flex-row sm:justify-end">
                            <button type="button" @click="showModal = false" class="btn-outline !rounded-xl">Cancel</button>
                            <button type="submit" :disabled="saving || !!fileError" class="btn-primary !rounded-xl disabled:cursor-not-allowed disabled:opacity-60">
                                <svg v-if="!saving" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" /></svg>
                                {{ saving ? 'Saving...' : editing ? 'Save changes' : 'Create category' }}
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ categories: Array });
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const fileError = ref('');
const form = ref({ name: '', description: '', sort_order: 0, delivery_mode: 'both', is_active: true, image: null });
const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;

const openModal = (cat = null) => {
    editing.value = cat;
    fileError.value = '';
    form.value = cat
        ? { name: cat.name, description: cat.description || '', sort_order: cat.sort_order || 0, delivery_mode: cat.delivery_mode || 'both', is_active: !!cat.is_active, image: null }
        : { name: '', description: '', sort_order: 0, delivery_mode: 'both', is_active: true, image: null };
    showModal.value = true;
};

const handleFile = (event) => {
    const file = event.target.files?.[0] || null;
    fileError.value = file && file.size > 2 * 1024 * 1024 ? 'Please choose an image smaller than 2MB.' : '';
    form.value.image = fileError.value ? null : file;
};

const saveCategory = () => {
    if (fileError.value) return;
    saving.value = true;
    const data = new FormData();
    data.append('name', form.value.name);
    data.append('description', form.value.description || '');
    data.append('sort_order', form.value.sort_order ?? 0);
    data.append('delivery_mode', form.value.delivery_mode || 'both');
    data.append('is_active', form.value.is_active ? '1' : '0');
    if (form.value.image) data.append('image', form.value.image);
    if (editing.value) {
        data.append('_method', 'PUT');
        router.post(route('admin.categories.update', editing.value.id), data, { forceFormData: true, onFinish: () => saving.value = false, onSuccess: () => showModal.value = false });
    } else {
        router.post(route('admin.categories.store'), data, { forceFormData: true, onFinish: () => saving.value = false, onSuccess: () => showModal.value = false });
    }
};

const deleteCategory = (cat) => { if (confirm('Delete this category?')) router.delete(route('admin.categories.destroy', cat.id)); };
const deliveryLabel = (mode) => ({ pickup: 'Pickup + delivery', home_delivery: 'Home delivery', both: 'Pickup + delivery' }[mode] || 'Pickup + delivery');
</script>
