<template>
    <AdminLayout active-menu="delivery-men" page-title="Delivery Men">
        <div class="max-w-4xl">
            <!-- Add Button -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-serif font-bold text-brand-900 text-lg">All Delivery Men</h2>
                <button @click="showModal = true" class="bg-gold-500 text-brand-950 px-5 py-2.5 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add Delivery Man
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-brand-100 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-cream-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-brand-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-brand-500 uppercase">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-brand-500 uppercase">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-brand-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-brand-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        <tr v-for="man in deliveryMen" :key="man.id" class="hover:bg-cream-50 transition">
                            <td class="px-4 py-3 text-sm font-medium text-brand-900">{{ man.name }}</td>
                            <td class="px-4 py-3 text-sm text-brand-600">{{ man.phone }}</td>
                            <td class="px-4 py-3 text-sm text-brand-600">{{ man.branch?.name || '—' }}</td>
                            <td class="px-4 py-3">
                                <span :class="man.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2.5 py-1 rounded-full text-xs font-medium">
                                    {{ man.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm flex gap-2">
                                <button @click="editMan(man)" class="text-brand-600 hover:text-brand-800 font-medium">Edit</button>
                                <button @click="deleteMan(man)" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="!deliveryMen.length">
                            <td colspan="5" class="px-4 py-8 text-center text-brand-400 text-sm">No delivery men added yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h3 class="font-serif font-bold text-brand-900 text-lg mb-5">{{ editing ? 'Edit Delivery Man' : 'Add Delivery Man' }}</h3>
                <form @submit.prevent="saveMan" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Name</label>
                        <input v-model="form.name" type="text" required placeholder="Delivery man name" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Phone Number</label>
                        <input v-model="form.phone" type="tel" required placeholder="01XXXXXXXXX" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Branch (optional)</label>
                        <select v-model="form.branch_id" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none">
                            <option value="">No specific branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input v-model="form.is_active" type="checkbox" id="active" class="w-4 h-4 rounded text-gold-500 focus:ring-gold-400" />
                        <label for="active" class="text-sm text-brand-700">Active</label>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition">{{ editing ? 'Update' : 'Add' }}</button>
                        <button type="button" @click="showModal = false" class="px-5 py-3 rounded-xl border-2 border-brand-100 text-brand-600 hover:bg-brand-50 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ deliveryMen: Array, branches: Array });

const showModal = ref(false);
const editing = ref(null);
const form = ref({ name: '', phone: '', branch_id: '', is_active: true });

const editMan = (man) => {
    editing.value = man.id;
    form.value = { name: man.name, phone: man.phone, branch_id: man.branch_id || '', is_active: man.is_active };
    showModal.value = true;
};

const saveMan = () => {
    if (editing.value) {
        router.patch(route('admin.delivery-men.update', editing.value), form.value, {
            onSuccess: () => { showModal.value = false; editing.value = null; },
        });
    } else {
        router.post(route('admin.delivery-men.store'), form.value, {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deleteMan = (man) => {
    if (confirm(`Remove ${man.name}?`)) {
        router.delete(route('admin.delivery-men.destroy', man.id));
    }
};
</script>
