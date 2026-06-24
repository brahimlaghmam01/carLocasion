<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Log {
    id: number;
    user: string;
    action: string;
    model: string;
    record_id: number | null;
    description: string | null;
    created_at: string | null;
}

const props = defineProps<{
    logs: {
        data: Log[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    actions: string[];
    filters: { search?: string; action?: string };
}>();

const search = ref(props.filters?.search || '');
const actionFilter = ref(props.filters?.action || '');

function doSearch() {
    router.get(
        '/admin/activity-logs',
        { search: search.value, action: actionFilter.value || null },
        { preserveState: true, replace: true },
    );
}

watch(search, (v, ov) => {
    if (v === '' && ov !== '') doSearch();
});

const actionColors: Record<string, string> = {
    created: 'bg-green-100 text-green-700',
    updated: 'bg-blue-100 text-blue-700',
    deleted: 'bg-red-100 text-red-700',
    suspended: 'bg-amber-100 text-amber-700',
    activated: 'bg-emerald-100 text-emerald-700',
};

function actionClass(action: string) {
    return actionColors[action] || 'bg-gray-100 text-gray-700';
}
</script>

<template>
    <Head title="Activity Logs" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Activity Logs</h1>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <Input v-model="search" placeholder="Search description..." class="max-w-xs" @keyup.enter="doSearch" />
                <select
                    v-model="actionFilter"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                    @change="doSearch"
                >
                    <option value="">All actions</option>
                    <option v-for="a in props.actions" :key="a" :value="a">{{ a }}</option>
                </select>
                <Button @click="doSearch">Search</Button>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Model</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">When</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="log in props.logs.data" :key="log.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ log.user }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                    :class="actionClass(log.action)"
                                >
                                    {{ log.action }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ log.model }}<span v-if="log.record_id" class="text-muted-foreground"> #{{ log.record_id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ log.description || '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ log.created_at }}</td>
                        </tr>
                        <tr v-if="props.logs.data.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No activity recorded.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.logs.links?.length" class="flex flex-wrap gap-2">
                <Link
                    v-for="(link, i) in props.logs.links"
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
        </main>
    </AdminLayout>
</template>
