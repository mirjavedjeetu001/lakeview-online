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

            <div class="bg-white rounded-2xl border border-brand-100 shadow-soft overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px]">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr>
                                <th class="table-head">Category</th>
                                <th class="table-head">Products</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-cream-50 transition">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-brand-50 overflow-hidden flex items-center justify-center">
                                            <img v-if="cat.image" :src="assetUrl(cat.image)" :alt="cat.name" class="w-full h-full object-cover" />
                                            <span v-else class="text-xl">🍰</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-brand-900">{{ cat.name }}</div>
                                            <div class="text-xs text-brand-400">{{ cat.description || 'No description' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell font-semibold text-brand-700">{{ cat.products_count || 0 }}</td>
                                <td class="table-cell"><span :class="cat.is_active ? 'status-success' : 'status-danger'">{{ cat.is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="table-cell whitespace-nowrap">
                                    <button @click="openModal(cat)" class="text-brand-600 hover:text-brand-500 font-semibold text-sm mr-4">Edit</button>
                                    <button @click="deleteCategory(cat)" class="text-red-500 hover:text-red-700 font-semibold text-sm">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="!categories?.length"><td colspan="4" class="p-12 text-center text-sm text-brand-500">No categories found.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] bg-brand-950/60 backdrop-blur-sm p-4 flex items-center justify-center" @click.self="showModal = false">
            <div class="w-full max-w-lg max-h-[92vh] overflow-y-auto rounded-3xl bg-cream-50 shadow-2xl p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div><p class="eyebrow">Menu group</p><h3 class="font-serif text-2xl font-bold text-brand-900 mt-2">{{ editing ? 'Edit category' : 'Add category' }}</h3></div>
                    <button @click="showModal = false" class="icon-button" aria-label="Close">×</button>
                </div>
                <form @submit.prevent="saveCategory" class="space-y-5">
                    <label class="field-label">Category name<input v-model="form.name" type="text" required class="field-input" /></label>
                    <label class="field-label">Description<textarea v-model="form.description" rows="3" class="field-input"></textarea></label>
                    <label class="field-label">Category image<input @change="handleFile" type="file" accept="image/*" class="field-input file:mr-3 file:rounded-full file:border-0 file:bg-brand-100 file:px-3 file:py-1 file:text-xs file:font-bold file:text-brand-700" /><span class="block mt-1 text-xs font-normal text-brand-400">JPG, PNG or WEBP · maximum 2MB</span></label>
                    <div v-if="fileError" class="rounded-xl bg-red-50 px-3 py-2 text-xs text-red-700">{{ fileError }}</div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <label class="field-label">Sort order<input v-model="form.sort_order" type="number" min="0" class="field-input" /></label>
                        <label class="inline-flex items-center gap-2 self-end pb-3 text-sm font-semibold text-brand-700"><input v-model="form.is_active" type="checkbox" class="rounded border-brand-300 text-brand-600" /> Active category</label>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" :disabled="saving || !!fileError" class="flex-1 rounded-full bg-brand-700 py-3 text-sm font-bold text-white hover:bg-brand-600 disabled:opacity-60">{{ saving ? 'Saving...' : editing ? 'Save changes' : 'Create category' }}</button>
                        <button type="button" @click="showModal = false" class="rounded-full border border-brand-200 px-6 py-3 text-sm font-bold text-brand-700">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
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
const form = ref({ name: '', description: '', sort_order: 0, is_active: true, image: null });
const assetUrl = (path) => path?.startsWith('http') ? path : '/storage/' + path;

const openModal = (cat = null) => {
    editing.value = cat;
    fileError.value = '';
    form.value = cat
        ? { name: cat.name, description: cat.description || '', sort_order: cat.sort_order || 0, is_active: !!cat.is_active, image: null }
        : { name: '', description: '', sort_order: 0, is_active: true, image: null };
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
</script>
