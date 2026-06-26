<template>
    <AdminLayout active-menu="products" page-title="Products">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-b gap-3">
                <h2 class="font-bold text-gray-900">All Products</h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <input v-model="search" type="text" placeholder="Search..." class="flex-1 rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 text-sm" @input="debouncedSearch" />
                    <select v-model="categoryFilter" class="rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 text-sm" @change="doFilter">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <button @click="openModal()" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 whitespace-nowrap">Add Product</button>
                </div>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ product.name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ product.category?.name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">৳{{ product.effective_price }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs" :class="product.is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">{{ product.is_available ? 'Available' : 'Unavailable' }}</span></td>
                        <td class="px-4 py-3 text-sm">
                            <button @click="openModal(product)" class="text-brand-600 hover:text-brand-700 mr-3">Edit</button>
                            <button @click="deleteProduct(product)" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="products.links && products.links.length > 1" class="p-4 flex justify-center gap-2">
                <Link v-for="(link, i) in products.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-brand-600 text-white' : 'bg-white text-gray-700 border'" class="px-3 py-1.5 rounded-lg text-sm" v-html="link.label" :preserve-scroll="true"></Link>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="font-bold text-gray-900 mb-4">{{ editing ? 'Edit Product' : 'Add Product' }}</h3>
                <form @submit.prevent="saveProduct" class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" type="text" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Category</label><select v-model="form.category_id" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"><option value="">Select...</option><option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option></select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Price (৳)</label><input v-model="form.price" type="number" step="0.01" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Discount Price (৳)</label><input v-model="form.discount_price" type="number" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Image</label><input @change="e => form.image = e.target.files[0]" type="file" accept="image/*" class="w-full rounded-lg border-gray-300" /></div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_available" type="checkbox" class="rounded text-brand-600" /> Available</label>
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_featured" type="checkbox" class="rounded text-brand-600" /> Featured</label>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label><input v-model="form.sort_order" type="number" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" /></div>
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

const props = defineProps({ products: Object, categories: Array, filters: Object });
const showModal = ref(false);
const editing = ref(null);
const search = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category_id || '');
let debounceTimer = null;
const form = ref({ name: '', category_id: '', price: '', discount_price: '', description: '', image: null, is_available: true, is_featured: false, sort_order: 0 });

const debouncedSearch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => doFilter(), 300); };
const doFilter = () => router.get(route('admin.products.index'), { search: search.value, category_id: categoryFilter.value }, { preserveState: true, preserveScroll: true });

const openModal = (product = null) => {
    editing.value = product;
    if (product) { form.value = { name: product.name, category_id: product.category_id, price: product.price, discount_price: product.discount_price || '', description: product.description || '', image: null, is_available: product.is_available, is_featured: product.is_featured, sort_order: product.sort_order }; }
    else { form.value = { name: '', category_id: '', price: '', discount_price: '', description: '', image: null, is_available: true, is_featured: false, sort_order: 0 }; }
    showModal.value = true;
};

const saveProduct = () => {
    const data = new FormData();
    Object.keys(form.value).forEach(key => { if (form.value[key] !== null && form.value[key] !== '') data.append(key, form.value[key]); });
    data.append('is_available', form.value.is_available ? 1 : 0);
    data.append('is_featured', form.value.is_featured ? 1 : 0);
    if (editing.value) { data.append('_method', 'PUT'); router.post(route('admin.products.update', editing.value.id), data, { forceFormData: true, onSuccess: () => showModal.value = false }); }
    else { router.post(route('admin.products.store'), data, { forceFormData: true, onSuccess: () => showModal.value = false }); }
};

const deleteProduct = (product) => { if (confirm('Delete this product?')) router.delete(route('admin.products.destroy', product.id)); };
</script>
