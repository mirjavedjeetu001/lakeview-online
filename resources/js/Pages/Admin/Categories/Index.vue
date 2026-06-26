<template>
    <AdminLayout active-menu="categories" page-title="Categories">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="font-bold text-gray-900">All Categories</h2>
                <button @click="openModal()" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Add Category</button>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ cat.name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ cat.products_count }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs" :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">{{ cat.is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-4 py-3 text-sm">
                            <button @click="openModal(cat)" class="text-brand-600 hover:text-brand-700 mr-3">Edit</button>
                            <button @click="deleteCategory(cat)" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="font-bold text-gray-900 mb-4">{{ editing ? 'Edit Category' : 'Add Category' }}</h3>
                <form @submit.prevent="saveCategory" class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" type="text" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label><input v-model="form.sort_order" type="number" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ categories: Array });
const showModal = ref(false);
const editing = ref(null);
const form = ref({ name: '', description: '', sort_order: 0, is_active: true });

const openModal = (cat = null) => {
    editing.value = cat;
    if (cat) { form.value = { name: cat.name, description: cat.description || '', sort_order: cat.sort_order, is_active: cat.is_active }; }
    else { form.value = { name: '', description: '', sort_order: 0, is_active: true }; }
    showModal.value = true;
};

const saveCategory = () => {
    if (editing.value) { router.put(route('admin.categories.update', editing.value.id), form.value, { onSuccess: () => showModal.value = false }); }
    else { router.post(route('admin.categories.store'), form.value, { onSuccess: () => showModal.value = false }); }
};

const deleteCategory = (cat) => { if (confirm('Delete this category?')) router.delete(route('admin.categories.destroy', cat.id)); };
</script>
