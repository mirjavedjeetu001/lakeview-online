<template>
    <AdminLayout active-menu="delivery-men" page-title="Delivery Men">
        <div class="mx-auto max-w-5xl space-y-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Delivery team</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-brand-900">Delivery men</h2>
                    <p class="mt-2 text-sm text-brand-500">Manage your delivery team, contact details and branch assignments.</p>
                </div>
                <button type="button" @click="openCreateForm" class="btn-primary !rounded-xl !px-5 !py-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add delivery man
                </button>
            </div>

            <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
                <div class="flex flex-col gap-2 border-b border-brand-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-900">Team directory</h3>
                        <p class="mt-1 text-sm text-brand-400">Contact and status for each delivery member.</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gold-50 px-3 py-1.5 text-xs font-bold text-gold-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                        {{ deliveryMen.length }} {{ deliveryMen.length === 1 ? 'member' : 'members' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-brand-50">
                            <tr>
                                <th class="table-head">Name</th>
                                <th class="table-head">Phone</th>
                                <th class="table-head">Branch</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="man in deliveryMen" :key="man.id" class="transition hover:bg-cream-50/80">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-100 font-serif text-lg font-bold text-brand-700">{{ man.name?.charAt(0)?.toUpperCase() }}</span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-brand-900">{{ man.name }}</p>
                                            <p class="mt-0.5 text-xs text-brand-400">Team member #{{ man.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell"><a :href="'tel:' + man.phone" class="font-semibold text-brand-600 transition hover:text-gold-600">{{ man.phone }}</a></td>
                                <td class="table-cell">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700">
                                        <svg class="h-3.5 w-3.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l8-4v18m-4-14v.01M9 11v.01M9 15v.01M13 7v.01M13 11v.01M13 15v.01M17 21v-8h4v8" /></svg>
                                        {{ man.branch?.name || 'All branches' }}
                                    </span>
                                </td>
                                <td class="table-cell"><span :class="man.is_active ? 'status-success' : 'status-danger'">{{ man.is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="table-cell whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="editMan(man)" class="text-brand-600">Edit</button>
                                        <button type="button" @click="deleteMan(man)" class="text-red-500">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!deliveryMen.length">
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1v6m3-3h-6" /></svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-brand-800">No delivery men yet</p>
                                    <p class="mt-1 text-sm text-brand-400">Add your first team member to get started.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <Transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-brand-950/55 p-4 backdrop-blur-sm" @click.self="closeForm">
                <section class="w-full max-w-xl overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-2xl">
                    <div class="flex items-start justify-between gap-4 border-b border-brand-100 bg-cream-50/80 p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gold-100 text-gold-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1v6m3-3h-6" /></svg>
                            </span>
                            <div>
                                <p class="eyebrow">Delivery team</p>
                                <h3 class="mt-2 font-serif text-2xl font-bold text-brand-900">{{ editing ? 'Edit delivery man' : 'Add delivery man' }}</h3>
                                <p class="mt-1 text-sm leading-6 text-brand-500">Enter their contact details and choose an outlet.</p>
                            </div>
                        </div>
                        <button type="button" @click="closeForm" class="icon-button shrink-0" aria-label="Close form">×</button>
                    </div>

                    <form @submit.prevent="saveMan" class="space-y-5 p-6 sm:p-7">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="field-label">Full name<input v-model="form.name" type="text" required autocomplete="name" placeholder="Delivery man name" class="field-input" /></label>
                            <label class="field-label">Phone number<input v-model="form.phone" type="tel" required autocomplete="tel" placeholder="01XXXXXXXXX" class="field-input" /></label>
                        </div>
                        <label class="field-label">Assigned branch <span class="font-normal text-brand-400">(optional)</span>
                            <select v-model="form.branch_id" class="field-input">
                                <option value="">No specific branch</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                            </select>
                        </label>
                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-brand-100 bg-cream-50 px-4 py-3.5 transition hover:border-brand-200">
                            <span><span class="block text-sm font-bold text-brand-800">Active team member</span><span class="mt-0.5 block text-xs text-brand-400">Can be assigned to new delivery orders.</span></span>
                            <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-brand-300 text-gold-500 focus:ring-gold-300" />
                        </label>
                        <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-5 sm:flex-row sm:justify-end">
                            <button type="button" @click="closeForm" class="btn-outline !rounded-xl">Cancel</button>
                            <button type="submit" class="btn-primary !rounded-xl">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" /></svg>
                                {{ editing ? 'Save changes' : 'Add delivery man' }}
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
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ deliveryMen: Array, branches: Array });

const showModal = ref(false);
const editing = ref(null);
const form = ref({ name: '', phone: '', branch_id: '', is_active: true });

const openCreateForm = () => {
    editing.value = null;
    form.value = { name: '', phone: '', branch_id: '', is_active: true };
    showModal.value = true;
};

const closeForm = () => {
    showModal.value = false;
    editing.value = null;
};

const editMan = (man) => {
    editing.value = man.id;
    form.value = { name: man.name, phone: man.phone, branch_id: man.branch_id || '', is_active: man.is_active };
    showModal.value = true;
};

const saveMan = () => {
    if (editing.value) {
        router.patch(route('admin.delivery-men.update', editing.value), form.value, {
            onSuccess: closeForm,
        });
    } else {
        router.post(route('admin.delivery-men.store'), form.value, {
            onSuccess: closeForm,
        });
    }
};

const deleteMan = (man) => {
    if (confirm(`Remove ${man.name}?`)) {
        router.delete(route('admin.delivery-men.destroy', man.id));
    }
};
</script>
