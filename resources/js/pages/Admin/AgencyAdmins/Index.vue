<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Admin {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    agency_id: number | null;
    agency?: { id: number; name: string } | null;
}

const props = defineProps<{
    admins: {
        data: Admin[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    agencies: Array<{ id: number; name: string }>;
    filters: { search?: string };
}>();

const search = ref(props.filters?.search || '');

function doSearch() {
    router.get('/admin/agency-admins', { search: search.value }, { preserveState: true, replace: true });
}

watch(search, (v, ov) => {
    if (v === '' && ov !== '') doSearch();
});

const showDialog = ref(false);
const editing = ref<Admin | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    agency_id: '' as number | string,
    is_active: true as boolean,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
}

function openEdit(a: Admin) {
    editing.value = a;
    form.name = a.name;
    form.email = a.email;
    form.password = '';
    form.agency_id = a.agency_id ?? '';
    form.is_active = a.is_active;
    form.clearErrors();
    showDialog.value = true;
}

function submit() {
    if (editing.value) {
        form.put(`/admin/agency-admins/${editing.value.id}`, { onSuccess: () => (showDialog.value = false) });
    } else {
        form.post('/admin/agency-admins', { onSuccess: () => (showDialog.value = false) });
    }
}

function destroy(a: Admin) {
    if (!confirm(`Delete "${a.name}"? This cannot be undone.`)) return;
    router.delete(`/admin/agency-admins/${a.id}`);
}
</script>

<template>
    <Head title="Agency Admins" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Agency Admins</h1>
                <Button @click="openCreate">Add Admin</Button>
            </div>

            <div class="flex items-center gap-2">
                <Input v-model="search" placeholder="Search name or email..." class="max-w-md" @keyup.enter="doSearch" />
                <Button @click="doSearch">Search</Button>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Agency</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="a in props.admins.data" :key="a.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ a.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ a.email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ a.agency?.name || '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="a.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                >
                                    <span class="size-2 rounded-full" :class="a.is_active ? 'bg-green-500' : 'bg-gray-400'" />
                                    {{ a.is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" @click="openEdit(a)">Edit</Button>
                                    <Button variant="destructive" size="sm" @click="destroy(a)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.admins.data.length === 0">
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">No agency admins found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.admins.links?.length" class="flex flex-wrap gap-2">
                <Link
                    v-for="(link, i) in props.admins.links"
                    :key="i"
                    :href="link.url || ''"
                    :class="[
                        'rounded px-3 py-1 text-sm',
                        link.active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700',
                        !link.url && 'pointer-events-none opacity-50',
                    ]"
                >
                    <span v-html="link.label" />
                </Link>
            </nav>

            <Dialog v-model:open="showDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{{ editing ? 'Edit Agency Admin' : 'Add Agency Admin' }}</DialogTitle>
                    </DialogHeader>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" placeholder="Full name" />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        <div>
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="Email address" />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                        <div>
                            <Label for="password">
                                Password
                                <span v-if="editing" class="text-xs text-muted-foreground">(leave blank to keep current)</span>
                            </Label>
                            <Input id="password" v-model="form.password" type="password" placeholder="Password" />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>
                        <div>
                            <Label for="agency_id">Agency</Label>
                            <select
                                id="agency_id"
                                v-model="form.agency_id"
                                class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="" disabled>Select an agency</option>
                                <option v-for="agency in props.agencies" :key="agency.id" :value="agency.id">{{ agency.name }}</option>
                            </select>
                            <InputError :message="form.errors.agency_id" class="mt-1" />
                        </div>
                        <div v-if="editing" class="flex items-center gap-2">
                            <input id="is_active" v-model="form.is_active" type="checkbox" class="size-4 rounded border-gray-300" />
                            <Label for="is_active" class="!mb-0">Active</Label>
                            <InputError :message="form.errors.is_active" class="mt-1" />
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showDialog = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : editing ? 'Save Changes' : 'Create' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </main>
    </AdminLayout>
</template>
