<template>
    <AdminLayout active-menu="delivery-areas" page-title="Delivery Areas">
        <div class="bg-white rounded-2xl card-shadow border border-brand-100 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-brand-100">
                <div>
                    <h2 class="font-serif font-bold text-brand-900 text-lg">Delivery Areas</h2>
                    <p class="text-sm text-brand-400 mt-1">Manage delivery zones and charges for Satkhira areas</p>
                </div>
                <button @click="openModal()" class="bg-gold-500 text-brand-950 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gold-400 transition">+ Add Area</button>
            </div>
            <table class="w-full">
                <thead class="bg-cream-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-brand-500 uppercase">Area</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-brand-500 uppercase">Zone</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-brand-500 uppercase">Charge</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-brand-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    <tr v-for="area in areas" :key="area.id" class="hover:bg-cream-50 transition">
                        <td class="px-5 py-3 text-sm font-medium text-brand-900">{{ area.name }}</td>
                        <td class="px-5 py-3 text-sm">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium" :class="area.zone_type === 'sadar' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'">
                                {{ area.zone_type === 'sadar' ? '📍 Sadar' : '🛵 Outside Sadar' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm font-bold text-gold-600">৳{{ area.delivery_charge }}</td>
                        <td class="px-5 py-3 text-sm">
                            <button @click="openModal(area)" class="text-brand-600 hover:text-brand-800 mr-3 font-medium">Edit</button>
                            <button @click="deleteArea(area)" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-brand-100">
                <h3 class="font-serif font-bold text-brand-900 text-lg mb-4">{{ editing ? 'Edit Area' : 'Add Delivery Area' }}</h3>
                <form @submit.prevent="saveArea" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Area Name</label>
                        <input v-model="form.name" type="text" required placeholder="e.g. Binerpota, Debhata, Kalaroa..." class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Zone Type</label>
                        <select v-model="form.zone_type" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition">
                            <option value="sadar">📍 Sadar (Within Satkhira Sadar)</option>
                            <option value="outside_sadar">🛵 Outside Sadar (Upazilas)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-700 mb-1.5">Delivery Charge (৳)</label>
                        <input v-model="form.delivery_charge" type="number" step="0.01" required class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-gold-400 bg-cream-50 px-4 py-3 text-brand-900 transition" />
                        <p class="text-xs text-brand-400 mt-1">Sadar = ৳100, Outside Sadar = ৳200 (adjustable)</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-brand-700">
                        <input v-model="form.is_active" type="checkbox" class="rounded text-gold-500 focus:ring-gold-400" /> Active
                    </label>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gold-500 text-brand-950 py-3 rounded-xl font-bold hover:bg-gold-400 transition">{{ editing ? 'Update' : 'Create' }}</button>
                        <button type="button" @click="showModal = false" class="flex-1 border-2 border-brand-100 text-brand-700 py-3 rounded-xl font-medium hover:bg-cream-50 transition">Cancel</button>
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

const props = defineProps({ areas: Array });
const showModal = ref(false);
const editing = ref(null);
const form = ref({ name: '', zone_type: 'sadar', delivery_charge: 100, is_active: true });

const openModal = (area = null) => {
    editing.value = area;
    if (area) { form.value = { name: area.name, zone_type: area.zone_type, delivery_charge: area.delivery_charge, is_active: area.is_active }; }
    else { form.value = { name: '', zone_type: 'sadar', delivery_charge: 100, is_active: true }; }
    showModal.value = true;
};

const saveArea = () => {
    if (editing.value) { router.put(route('admin.delivery-areas.update', editing.value.id), form.value, { onSuccess: () => showModal.value = false }); }
    else { router.post(route('admin.delivery-areas.store'), form.value, { onSuccess: () => showModal.value = false }); }
};

const deleteArea = (area) => { if (confirm('Delete this area?')) router.delete(route('admin.delivery-areas.destroy', area.id)); };
</script>
