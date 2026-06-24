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

interface City {
    id: number;
    name: string;
    status: string;
    agencies_count: number;
}

const props = defineProps<{
    cities: {
        data: City[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

const search = ref(props.filters?.search || '');

function doSearch() {
    router.get('/admin/cities', { search: search.value }, { preserveState: true, replace: true });
}

watch(search, (v, ov) => {
    if (v === '' && ov !== '') doSearch();
});

const showDialog = ref(false);
const editing = ref<City | null>(null);

const form = useForm({
    name: '',
    status: 'active',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
}

function openEdit(city: City) {
    editing.value = city;
    form.name = city.name;
    form.status = city.status;
    form.clearErrors();
    showDialog.value = true;
}

function submit() {
    if (editing.value) {
        form.put(`/admin/cities/${editing.value.id}`, {
            onSuccess: () => (showDialog.value = false),
        });
    } else {
        form.post('/admin/cities', {
            onSuccess: () => (showDialog.value = false),
        });
    }
}

function destroy(city: City) {
    if (!confirm(`Delete "${city.name}"? This cannot be undone.`)) return;
    router.delete(`/admin/cities/${city.id}`);
}
</script>

<template>
    <Head title="Cities" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Cities</h1>
                <Button @click="openCreate">Add City</Button>
            </div>

            <div class="flex items-center gap-2">
                <Input v-model="search" placeholder="Search cities..." class="max-w-md" @keyup.enter="doSearch" />
                <Button @click="doSearch">Search</Button>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Agencies</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="c in props.cities.data" :key="c.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ c.name }}</td>
                            <td class="px-4 py-3">{{ c.agencies_count }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="c.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                >
                                    <span class="size-2 rounded-full" :class="c.status === 'active' ? 'bg-green-500' : 'bg-gray-400'" />
                                    {{ c.status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" @click="openEdit(c)">Edit</Button>
                                    <Button variant="destructive" size="sm" @click="destroy(c)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.cities.data.length === 0">
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">No cities found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.cities.links?.length" class="flex flex-wrap gap-2">
                <Link
                    v-for="(link, i) in props.cities.links"
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
                        <DialogTitle>{{ editing ? 'Edit City' : 'Add City' }}</DialogTitle>
                    </DialogHeader>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" placeholder="City name" />
                            <InputError :message="form.errors.name" class="mt-1" />
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
