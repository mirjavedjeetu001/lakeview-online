<template>
    <AdminLayout active-menu="users" page-title="User Management">
        <div class="space-y-5">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[.2em] text-brand-400 font-bold">Access control</p>
                    <h2 class="font-serif text-3xl font-bold text-brand-900 mt-2">Users & team access</h2>
                    <p class="text-sm text-brand-500 mt-2">Create staff accounts, manage customer profiles, and control admin access.</p>
                </div>
                <button @click="openModal()" class="bg-brand-700 text-white rounded-full px-5 py-3 text-sm font-bold hover:bg-brand-600 transition">+ Add user</button>
            </div>

            <div class="bg-white rounded-2xl border border-brand-100 shadow-soft p-4 flex flex-col sm:flex-row gap-3">
                <input v-model="search" @input="debouncedSearch" type="search" placeholder="Search name, phone or email..." class="flex-1 rounded-xl border border-brand-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" />
                <select v-model="roleFilter" @change="doFilter" class="rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-brand-700 outline-none focus:border-brand-500">
                    <option value="">All roles</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super admin</option>
                </select>
            </div>

            <div class="bg-white rounded-2xl border border-brand-100 shadow-soft overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr><th class="table-head">User</th><th class="table-head">Phone</th><th class="table-head">Role</th><th class="table-head">Branch</th><th class="table-head">Status</th><th class="table-head">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-brand-100">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-cream-50 transition">
                                <td class="table-cell"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-gold-100 text-brand-700 flex items-center justify-center font-bold">{{ user.name?.charAt(0)?.toUpperCase() }}</div><div><div class="font-semibold text-brand-900">{{ user.name }}</div><div class="text-xs text-brand-400">{{ user.email || 'No email added' }}</div></div></div></td>
                                <td class="table-cell text-brand-600">{{ user.phone || '—' }}</td>
                                <td class="table-cell"><span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">{{ roleLabel(user.role) }}</span></td>
                                <td class="table-cell text-sm text-brand-600">{{ user.role === 'super_admin' ? 'All branches' : (user.branch?.name || 'All branches') }}</td>
                                <td class="table-cell"><span :class="user.is_active ? 'status-success' : 'status-danger'">{{ user.is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="table-cell"><button @click="openModal(user)" class="text-brand-600 hover:text-brand-500 font-semibold text-sm mr-4">Edit</button><button @click="deleteUser(user)" class="text-red-500 hover:text-red-700 font-semibold text-sm">Delete</button></td>
                            </tr>
                            <tr v-if="!users.data?.length"><td colspan="6" class="p-12 text-center text-sm text-brand-500">No users found.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="users.links?.length > 1" class="p-4 flex justify-center gap-2 border-t border-brand-100"><Link v-for="(link, i) in users.links" :key="i" :href="link.url || '#'" :class="link.active ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-brand-700 border-brand-200'" class="rounded-full border px-3 py-1.5 text-xs font-bold" v-html="link.label" :preserve-scroll="true" /></div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] bg-brand-950/60 backdrop-blur-sm p-4 flex items-center justify-center" @click.self="showModal = false">
            <div class="w-full max-w-2xl max-h-[92vh] overflow-y-auto rounded-3xl bg-cream-50 shadow-2xl p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4 mb-6"><div><p class="text-xs uppercase tracking-[.2em] text-brand-400 font-bold">Access control</p><h3 class="font-serif text-2xl font-bold text-brand-900 mt-2">{{ editing ? 'Edit user' : 'Create user' }}</h3></div><button @click="showModal = false" class="w-9 h-9 rounded-full bg-brand-100 text-brand-600">×</button></div>
                <form @submit.prevent="saveUser" class="space-y-4">
                    <label class="field-label">Name<input v-model="form.name" required class="field-input" /></label>
                    <div class="grid sm:grid-cols-2 gap-4"><label class="field-label">Phone <span class="font-normal text-brand-400">(optional)</span><input v-model="form.phone" type="tel" class="field-input" /></label><label class="field-label">Email <span class="font-normal text-brand-400">(optional)</span><input v-model="form.email" type="email" class="field-input" /></label></div>
                    <label class="field-label">Password <span class="font-normal text-brand-400">{{ editing ? '(leave blank to keep current)' : '' }}</span><input v-model="form.password" type="password" :required="!editing" minlength="6" class="field-input" /></label>
                    <div class="grid sm:grid-cols-2 gap-4"><label class="field-label">Role<select v-model="form.role" class="field-input"><option value="customer">Customer</option><option value="admin">Admin</option><option value="super_admin">Super admin</option></select></label><label class="flex items-center gap-2 text-sm font-semibold text-brand-700 sm:pt-7"><input v-model="form.is_active" type="checkbox" class="rounded border-brand-300 text-brand-600" /> Active account</label></div>
                    <div v-if="form.role !== 'super_admin'" class="space-y-3">
                        <label class="field-label">Assigned branch <span class="font-normal text-brand-400">(blank = all branches)</span><select v-model="form.branch_id" class="field-input"><option :value="null">All branches</option><option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option></select></label>
                        <div v-if="form.role === 'admin'" class="rounded-2xl border border-brand-200 bg-white p-4">
                            <div class="flex items-center justify-between gap-3 mb-3"><div><p class="text-sm font-bold text-brand-900">Module permissions</p><p class="text-xs text-brand-400 mt-1">Choose which admin sections this user can open and manage.</p></div><button type="button" @click="selectAllPermissions" class="text-xs font-bold text-brand-600">Select all</button></div>
                            <div class="grid sm:grid-cols-2 gap-2"><label v-for="(label, key) in permissions" :key="key" class="flex items-center gap-2 rounded-xl border border-brand-100 px-3 py-2 text-sm text-brand-700"><input v-model="form.permissions" :value="key" type="checkbox" class="rounded border-brand-300 text-brand-600" />{{ label }}</label></div>
                        </div>
                    </div>
                    <div v-else class="rounded-2xl border border-gold-200 bg-gold-50 px-4 py-3 text-xs text-brand-700">Super admin has full access to every branch and module.</div>
                    <div v-if="formError || Object.keys(errors).length" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <div v-if="formError">{{ formError }}</div>
                        <div v-for="(messages, key) in errors" :key="key">{{ Array.isArray(messages) ? messages[0] : messages }}</div>
                    </div>
                    <p class="rounded-xl bg-brand-50 px-4 py-3 text-xs text-brand-600">Inactive users cannot log in. Branch access limits product, stock, order and report data to the selected outlet.</p>
                    <div class="flex gap-3 pt-2"><button type="submit" :disabled="saving" class="flex-1 rounded-full bg-brand-700 py-3 text-sm font-bold text-white hover:bg-brand-600 disabled:opacity-60">{{ saving ? 'Saving...' : editing ? 'Save changes' : 'Create user' }}</button><button type="button" @click="showModal = false" class="rounded-full border border-brand-200 px-6 py-3 text-sm font-bold text-brand-700 hover:bg-white">Cancel</button></div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ users: Object, filters: Object, branches: Array, permissions: Object });
const page = usePage();
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const formError = ref('');
const errors = computed(() => page.props.errors || {});
const search = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role || '');
let debounceTimer;

const branches = computed(() => props.branches || []);
const permissions = computed(() => props.permissions || {});
const blankForm = () => ({ name: '', phone: '', email: '', password: '', role: 'customer', branch_id: null, permissions: [], is_active: true });
const form = ref(blankForm());
const roleLabel = (role) => ({ customer: 'Customer', admin: 'Admin', super_admin: 'Super admin' }[role] || role);
const doFilter = () => router.get(route('admin.users.index'), { search: search.value, role: roleFilter.value }, { preserveState: true, preserveScroll: true });
const debouncedSearch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(doFilter, 350); };
const openModal = (user = null) => { editing.value = user; formError.value = ''; form.value = user ? { name: user.name, phone: user.phone || '', email: user.email || '', password: '', role: user.role, branch_id: user.branch_id || null, permissions: [...(user.permissions || [])], is_active: !!user.is_active } : blankForm(); showModal.value = true; };
const selectAllPermissions = () => { form.value.permissions = Object.keys(permissions.value); };
const saveUser = () => { formError.value = ''; if (!form.value.phone.trim() && !form.value.email.trim()) { formError.value = 'Please provide at least a phone number or an email address.'; return; } saving.value = true; const options = { onFinish: () => saving.value = false, onSuccess: () => showModal.value = false }; if (editing.value) router.put(route('admin.users.update', editing.value.id), form.value, options); else router.post(route('admin.users.store'), form.value, options); };
const deleteUser = (user) => { if (confirm('Delete ' + user.name + '? This cannot be undone.')) router.delete(route('admin.users.destroy', user.id)); };
</script>
