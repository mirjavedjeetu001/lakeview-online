<template>
    <AdminLayout active-menu="delivery-areas" page-title="Delivery Areas">
        <div class="space-y-5">
            <div class="flex flex-col gap-4 rounded-[1.75rem] bg-brand-950 p-6 text-cream-50 shadow-card sm:flex-row sm:items-end sm:justify-between sm:p-8">
                <div><p class="text-xs font-bold uppercase tracking-[.2em] text-gold-300">Delivery control</p><h2 class="mt-2 font-serif text-3xl font-bold">Where can we deliver?</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-brand-200">Create branch areas, Satkhira Sadar/Rural zones and district-wise Bangladesh courier services from one place.</p></div>
                <button type="button" @click="openModal()" class="inline-flex items-center justify-center rounded-full bg-gold-500 px-5 py-3 text-sm font-bold text-brand-950 transition hover:bg-gold-400">+ Add service area</button>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="stat-card"><span class="stat-label">Total areas</span><span class="stat-value">{{ areas.length }}</span></div>
                <div class="stat-card"><span class="stat-label">Sadar & rural</span><span class="stat-value">{{ localCount }}</span></div>
                <div class="stat-card"><span class="stat-label">Outside Sadar</span><span class="stat-value">{{ outsideCount }}</span></div>
                <div class="stat-card"><span class="stat-label">Bangladesh courier</span><span class="stat-value">{{ nationalCount }}</span></div>
            </div>

            <div class="overflow-hidden rounded-[1.5rem] border border-brand-100 bg-white shadow-soft">
                <div class="flex flex-col gap-3 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between"><div><h3 class="font-serif text-xl font-bold text-brand-900">Configured delivery services</h3><p class="mt-1 text-sm text-brand-400">Inactive services remain saved but will not appear at checkout.</p></div><div class="rounded-full bg-brand-50 px-3 py-2 text-xs font-bold text-brand-600">Charges are per order</div></div>
                <div class="grid gap-3 border-b border-brand-100 bg-cream-50/70 p-5 sm:grid-cols-2 lg:grid-cols-[minmax(0,1.5fr)_repeat(3,minmax(0,1fr))_auto]"><input v-model="filters.search" type="search" placeholder="Search area, district, upazila or courier..." class="field-input bg-white" /><select v-model="filters.scope" class="field-input bg-white"><option value="all">All service scopes</option><option value="sadar">Satkhira Sadar</option><option value="sadar_rural">Sadar Rural</option><option value="outside_sadar">Outside Sadar</option><option value="national">Bangladesh courier</option></select><select v-model="filters.branch" class="field-input bg-white"><option value="all">All branches</option><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select><select v-model="filters.status" class="field-input bg-white"><option value="all">All statuses</option><option value="active">Active only</option><option value="inactive">Inactive only</option></select><button type="button" @click="resetFilters" class="rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-xs font-bold text-brand-700 hover:bg-brand-50">Reset</button></div>
                <div class="flex items-center justify-between gap-3 border-b border-brand-100 px-5 py-3 text-xs text-brand-400"><span>Showing <strong class="text-brand-700">{{ filteredAreas.length }}</strong> of {{ areas.length }} services</span><span v-if="hasFilters" class="rounded-full bg-gold-50 px-3 py-1 font-bold text-gold-700">Filters active</span></div>
                <div class="overflow-x-auto"><table class="w-full min-w-[850px]"><thead class="bg-brand-50"><tr><th class="table-head">Service</th><th class="table-head">Location</th><th class="table-head">Branch</th><th class="table-head">Charge</th><th class="table-head">Status</th><th class="table-head">Actions</th></tr></thead><tbody class="divide-y divide-brand-100"><tr v-for="area in filteredAreas" :key="area.id" class="transition hover:bg-cream-50"><td class="table-cell"><span class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold" :class="scopeClass(area)">{{ scopeLabel(area) }}</span></td><td class="table-cell"><div class="font-semibold text-brand-900">{{ area.name }}</div><div v-if="area.upazila || area.district" class="mt-1 text-xs text-brand-400">{{ [area.upazila, area.district].filter(Boolean).join(' · ') }}</div><div v-if="area.courier_name" class="mt-1 text-xs text-brand-500">{{ area.courier_name }}</div></td><td class="table-cell text-brand-600">{{ branchName(area.branch_id) }}</td><td class="table-cell font-bold text-gold-600">৳{{ money(area.delivery_charge) }}</td><td class="table-cell"><span :class="area.is_active ? 'status-success' : 'status-danger'">{{ area.is_active ? 'Active' : 'Inactive' }}</span></td><td class="table-cell"><button type="button" @click="openModal(area)" class="mr-4 text-sm font-semibold text-brand-600 hover:text-brand-500">Edit</button><button type="button" @click="deleteArea(area)" class="text-sm font-semibold text-red-500 hover:text-red-700">Delete</button></td></tr><tr v-if="!filteredAreas.length"><td colspan="6" class="p-12 text-center text-sm text-brand-500">{{ areas.length ? 'No service matches these filters.' : 'No delivery service configured yet.' }}</td></tr></tbody></table></div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-brand-950/60 p-4 backdrop-blur-sm" @click.self="showModal = false">
            <div class="max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-[1.75rem] bg-cream-50 p-6 shadow-2xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Delivery service</p><h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">{{ editing ? 'Edit service area' : 'Add service area' }}</h3><p class="mt-1 text-sm text-brand-500">The selected scope controls how customers see this option at checkout.</p></div><button type="button" @click="showModal = false" class="icon-button bg-brand-100">×</button></div>
                <form @submit.prevent="saveArea" class="space-y-4">
                    <label class="field-label">Service scope<select v-model="form.service_scope" class="field-input"><option value="sadar">Satkhira Sadar</option><option value="sadar_rural">Satkhira Sadar Rural</option><option value="outside_sadar">Outside Sadar / Upazila</option><option value="national">All Bangladesh · District courier</option></select></label>
                    <div class="rounded-2xl border border-gold-200 bg-gold-50/70 p-4 text-xs leading-5 text-brand-700"><strong class="text-brand-900">{{ scopeHelp }}</strong><br>{{ form.service_scope === 'national' ? 'Use a district row for each courier price. Customers will see this only when every product in the bag is marked nationwide eligible.' : 'Use separate rows when the charge or service area is different.' }}</div>
                    <label class="field-label">Serving branch <select v-model="form.branch_id" class="field-input"><option value="">All branches / global service</option><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}{{ !branch.is_active ? ' · hidden' : '' }}</option></select><span class="mt-1 block text-xs font-normal text-brand-400">Global rows are shared by every branch; choose a branch for branch-specific pricing.</span></label>
                    <label v-if="form.service_scope === 'national'" class="field-label">Bangladesh district<select v-model="form.district" required class="field-input"><option value="">Choose a district</option><option v-for="district in districts" :key="district" :value="district">{{ district }}</option></select></label>
                    <div class="grid gap-4 sm:grid-cols-2"><label class="field-label">Area / service name<input v-model="form.name" :readonly="form.service_scope === 'national' && !!form.district" required placeholder="e.g. Binerpota, Tala, Dhaka" class="field-input" /></label><label v-if="form.service_scope === 'outside_sadar'" class="field-label">Upazila <span class="font-normal text-brand-400">(optional)</span><input v-model="form.upazila" class="field-input" placeholder="e.g. Kalaroa" /></label></div>
                    <div class="grid gap-4 sm:grid-cols-2"><label class="field-label">{{ form.service_scope === 'national' ? 'Courier charge (৳)' : 'Delivery charge (৳)' }}<input v-model="form.delivery_charge" type="number" min="0" step="1" required class="field-input" /></label><label v-if="form.service_scope === 'national'" class="field-label">Courier name <span class="font-normal text-brand-400">(optional)</span><input v-model="form.courier_name" class="field-input" placeholder="e.g. Sundarban Courier" /></label></div>
                    <label class="flex items-center gap-3 rounded-2xl border border-brand-200 bg-white p-4 text-sm font-semibold text-brand-700"><input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200" /> Show this service at checkout</label>
                    <div class="flex gap-3 pt-2"><button type="submit" :disabled="saving" class="flex-1 rounded-full bg-brand-700 py-3 text-sm font-bold text-white transition hover:bg-brand-600 disabled:opacity-60">{{ saving ? 'Saving...' : editing ? 'Update service' : 'Create service' }}</button><button type="button" @click="showModal = false" class="rounded-full border border-brand-200 px-6 py-3 text-sm font-bold text-brand-700 hover:bg-white">Cancel</button></div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ areas: Array, branches: Array });
const districts = ['Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogura', 'Brahmanbaria', 'Chandpur', 'Chattogram', 'Chuadanga', 'Coxs Bazar', 'Cumilla', 'Dhaka', 'Dinajpur', 'Faridpur', 'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore', 'Jhalokathi', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna', 'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur', 'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh', 'Naogaon', 'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Netrokona', 'Nilphamari', 'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi', 'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon'];
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref(blankForm());
const filters = ref({ search: '', scope: 'all', branch: 'all', status: 'all' });

function blankForm() {
    return { branch_id: '', name: '', district: '', upazila: '', service_scope: 'sadar', zone_type: 'sadar', delivery_charge: 100, courier_name: '', is_active: true };
}

const localCount = computed(() => (props.areas || []).filter(area => ['sadar', 'sadar_rural'].includes(area.service_scope || area.zone_type)).length);
const outsideCount = computed(() => (props.areas || []).filter(area => (area.service_scope || area.zone_type) === 'outside_sadar').length);
const nationalCount = computed(() => (props.areas || []).filter(area => (area.service_scope || area.zone_type) === 'national').length);
const filteredAreas = computed(() => {
    const query = filters.value.search.trim().toLowerCase();
    return (props.areas || []).filter(area => {
        const scope = area.service_scope || (area.zone_type === 'sadar' ? 'sadar' : 'outside_sadar');
        const searchable = [area.name, area.district, area.upazila, area.courier_name, branchName(area.branch_id)].filter(Boolean).join(' ').toLowerCase();
        return (!query || searchable.includes(query))
            && (filters.value.scope === 'all' || scope === filters.value.scope)
            && (filters.value.branch === 'all' || String(area.branch_id || '') === String(filters.value.branch))
            && (filters.value.status === 'all' || (filters.value.status === 'active' ? !!area.is_active : !area.is_active));
    });
});
const hasFilters = computed(() => !!filters.value.search || filters.value.scope !== 'all' || filters.value.branch !== 'all' || filters.value.status !== 'all');
const resetFilters = () => { filters.value = { search: '', scope: 'all', branch: 'all', status: 'all' }; };
const scopeHelp = computed(() => ({ sadar: 'Use for areas inside Satkhira Sadar.', sadar_rural: 'Use for rural areas that are still inside Satkhira Sadar.', outside_sadar: 'Use for another Satkhira upazila.', national: 'Use for a district outside Satkhira with courier delivery.' }[form.value.service_scope] || 'Configure this delivery service.'));
const money = value => Number(value || 0).toLocaleString('en-BD', { maximumFractionDigits: 0 });
const branchName = id => id ? (props.branches || []).find(branch => Number(branch.id) === Number(id))?.name || 'Branch' : 'All branches / global';
const scopeLabel = area => ({ sadar: '📍 Sadar', sadar_rural: '🌾 Sadar Rural', outside_sadar: '🛵 Outside Sadar', national: '🚚 Bangladesh courier' }[area.service_scope || area.zone_type] || 'Delivery');
const scopeClass = area => ({ sadar: 'bg-blue-100 text-blue-700', sadar_rural: 'bg-sage-50 text-sage-700', outside_sadar: 'bg-orange-100 text-orange-700', national: 'bg-gold-100 text-brand-800' }[area.service_scope || area.zone_type] || 'bg-brand-100 text-brand-700');

watch(() => form.value.district, value => { if (form.value.service_scope === 'national' && value) form.value.name = value; });
watch(() => form.value.service_scope, value => { if (value === 'national' && form.value.district) form.value.name = form.value.district; if (value !== 'national') form.value.district = ''; if (value !== 'outside_sadar') form.value.upazila = ''; if (value === 'sadar') form.value.delivery_charge = 100; if (value === 'sadar_rural') form.value.delivery_charge = 150; if (value === 'outside_sadar') form.value.delivery_charge = 200; if (value === 'national') form.value.delivery_charge = 250; });

const openModal = area => {
    editing.value = area;
    if (area) {
        form.value = { branch_id: area.branch_id ?? '', name: area.name || '', district: area.district || '', upazila: area.upazila || '', service_scope: area.service_scope || (area.zone_type === 'sadar' ? 'sadar' : 'outside_sadar'), zone_type: area.zone_type || 'sadar', delivery_charge: area.delivery_charge || 0, courier_name: area.courier_name || '', is_active: !!area.is_active };
    } else {
        form.value = { ...blankForm(), branch_id: props.branches?.find(branch => branch.is_active)?.id || '' };
    }
    showModal.value = true;
};

const saveArea = () => {
    saving.value = true;
    const payload = { ...form.value, zone_type: ['sadar', 'sadar_rural'].includes(form.value.service_scope) ? 'sadar' : 'outside_sadar' };
    const options = { onFinish: () => saving.value = false, onSuccess: () => showModal.value = false };
    if (editing.value) router.put(route('admin.delivery-areas.update', editing.value.id), payload, options);
    else router.post(route('admin.delivery-areas.store'), payload, options);
};

const deleteArea = area => { if (confirm('Delete this delivery service?')) router.delete(route('admin.delivery-areas.destroy', area.id)); };
</script>
