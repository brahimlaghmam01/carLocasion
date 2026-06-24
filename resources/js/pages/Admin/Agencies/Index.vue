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

interface Agency {
    id: number;
    name: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    manager_name: string | null;
    status: string;
    city_id: number;
    city?: { id: number; name: string } | null;
    cars_count: number;
    reservations_count: number;
    users_count: number;
}

const props = defineProps<{
    agencies: {
        data: Agency[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    cities: Array<{ id: number; name: string }>;
    filters: { search?: string; city_id?: number | string | null };
}>();

const search = ref(props.filters?.search || '');
const cityFilter = ref(props.filters?.city_id ? String(props.filters.city_id) : '');

function doSearch() {
    router.get(
        '/admin/agencies',
        { search: search.value, city_id: cityFilter.value || null },
        { preserveState: true, replace: true },
    );
}

watch(search, (v, ov) => {
    if (v === '' && ov !== '') doSearch();
});

const showDialog = ref(false);
const editing = ref<Agency | null>(null);

const form = useForm({
    city_id: '' as number | string,
    name: '',
    address: '',
    phone: '',
    email: '',
    manager_name: '',
    status: 'active',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
}

function openEdit(a: Agency) {
    editing.value = a;
    form.city_id = a.city_id;
    form.name = a.name;
    form.address = a.address ?? '';
    form.phone = a.phone ?? '';
    form.email = a.email ?? '';
    form.manager_name = a.manager_name ?? '';
    form.status = a.status;
    form.clearErrors();
    showDialog.value = true;
}

function submit() {
    if (editing.value) {
        form.put(`/admin/agencies/${editing.value.id}`, { onSuccess: () => (showDialog.value = false) });
    } else {
        form.post('/admin/agencies', { onSuccess: () => (showDialog.value = false) });
    }
}

function destroy(a: Agency) {
    if (!confirm(`Delete "${a.name}"? This cannot be undone.`)) return;
    router.delete(`/admin/agencies/${a.id}`);
}
</script>

<template>
    <Head title="Agencies" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Agencies</h1>
                <Button @click="openCreate">Add Agency</Button>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <Input v-model="search" placeholder="Search agencies..." class="max-w-xs" @keyup.enter="doSearch" />
                <select
                    v-model="cityFilter"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                    @change="doSearch"
                >
                    <option value="">All cities</option>
                    <option v-for="city in props.cities" :key="city.id" :value="String(city.id)">{{ city.name }}</option>
                </select>
                <Button @click="doSearch">Search</Button>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Agency</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">City</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cars</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reservations</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="a in props.agencies.data" :key="a.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ a.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ a.manager_name || '—' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ a.city?.name || '—' }}</td>
                            <td class="px-4 py-3">{{ a.cars_count }}</td>
                            <td class="px-4 py-3">{{ a.reservations_count }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="a.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                >
                                    <span class="size-2 rounded-full" :class="a.status === 'active' ? 'bg-green-500' : 'bg-gray-400'" />
                                    {{ a.status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" @click="openEdit(a)">Edit</Button>
                                    <Button variant="destructive" size="sm" @click="destroy(a)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.agencies.data.length === 0">
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No agencies found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.agencies.links?.length" class="flex flex-wrap gap-2">
                <Link
                    v-for="(link, i) in props.agencies.links"
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
                        <DialogTitle>{{ editing ? 'Edit Agency' : 'Add Agency' }}</DialogTitle>
                    </DialogHeader>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" placeholder="Agency name" />
                                <InputError :message="form.errors.name" class="mt-1" />
                            </div>
                            <div>
                                <Label for="city_id">City</Label>
                                <select
                                    id="city_id"
                                    v-model="form.city_id"
                                    class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="" disabled>Select a city</option>
                                    <option v-for="city in props.cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                </select>
                                <InputError :message="form.errors.city_id" class="mt-1" />
                            </div>
                            <div>
                                <Label for="status">Status</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-1" />
                            </div>
                            <div>
                                <Label for="manager_name">Manager</Label>
                                <Input id="manager_name" v-model="form.manager_name" placeholder="Manager name" />
                                <InputError :message="form.errors.manager_name" class="mt-1" />
                            </div>
                            <div>
                                <Label for="phone">Phone</Label>
                                <Input id="phone" v-model="form.phone" placeholder="Phone" />
                                <InputError :message="form.errors.phone" class="mt-1" />
                            </div>
                            <div>
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" placeholder="Email" />
                                <InputError :message="form.errors.email" class="mt-1" />
                            </div>
                            <div>
                                <Label for="address">Address</Label>
                                <Input id="address" v-model="form.address" placeholder="Address" />
                                <InputError :message="form.errors.address" class="mt-1" />
                            </div>
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
