<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';
import { CalendarCheck, Car, CreditCard, Users } from 'lucide-vue-next';

Chart.register(...registerables);

interface Activity {
    id: number;
    user: string;
    action: string;
    model: string;
    record_id: number | null;
    description: string | null;
    created_at: string | null;
}

const props = defineProps<{
    agency: { id: number; name: string; city?: { id: number; name: string } | null } | null;
    kpis: { revenue: string; reservations: number; vehicles: number; customers: number };
    monthlyRevenue: { labels: string[]; data: number[] };
    monthlyReservations: { labels: string[]; data: number[] };
    vehicleAvailability: { available: number; rented: number; unavailable: number };
    recentActivities: Activity[];
}>();

const revenueCanvas = ref<HTMLCanvasElement | null>(null);
const reservationsCanvas = ref<HTMLCanvasElement | null>(null);
const availabilityCanvas = ref<HTMLCanvasElement | null>(null);

const kpiCards = [
    { key: 'revenue', label: 'Revenue', icon: CreditCard, color: 'text-emerald-600' },
    { key: 'reservations', label: 'Reservations', icon: CalendarCheck, color: 'text-blue-600' },
    { key: 'vehicles', label: 'Vehicles', icon: Car, color: 'text-indigo-600' },
    { key: 'customers', label: 'Customers', icon: Users, color: 'text-amber-600' },
] as const;

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

onMounted(() => {
    if (revenueCanvas.value) {
        new Chart(revenueCanvas.value, {
            type: 'line',
            data: {
                labels: props.monthlyRevenue.labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: props.monthlyRevenue.data,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
        });
    }

    if (reservationsCanvas.value) {
        new Chart(reservationsCanvas.value, {
            type: 'bar',
            data: {
                labels: props.monthlyReservations.labels,
                datasets: [
                    {
                        label: 'Reservations',
                        data: props.monthlyReservations.data,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                    },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
        });
    }

    if (availabilityCanvas.value) {
        new Chart(availabilityCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Rented', 'Unavailable'],
                datasets: [
                    {
                        data: [
                            props.vehicleAvailability.available,
                            props.vehicleAvailability.rented,
                            props.vehicleAvailability.unavailable,
                        ],
                        backgroundColor: ['#10b981', '#6366f1', '#f59e0b'],
                    },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%' },
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div>
                <h1 class="text-2xl font-semibold">{{ props.agency?.name || 'Agency' }} Dashboard</h1>
                <p v-if="props.agency?.city" class="text-sm text-muted-foreground">{{ props.agency.city.name }}</p>
            </div>

            <!-- KPI cards -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div v-for="card in kpiCards" :key="card.key" class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ card.label }}</span>
                        <component :is="card.icon" class="size-4" :class="card.color" />
                    </div>
                    <div class="mt-2 text-2xl font-semibold">{{ props.kpis[card.key] }}</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-lg border bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Revenue (last 6 months)</h2>
                    <div class="relative h-64">
                        <canvas ref="revenueCanvas"></canvas>
                    </div>
                </div>
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Vehicle Availability</h2>
                    <div class="relative h-64">
                        <canvas ref="availabilityCanvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Reservations (last 6 months)</h2>
                    <div class="relative h-64">
                        <canvas ref="reservationsCanvas"></canvas>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Recent Activity</h2>
                    <ul class="divide-y divide-gray-100">
                        <li v-for="activity in props.recentActivities" :key="activity.id" class="flex items-center gap-3 py-2 text-sm">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="actionClass(activity.action)">
                                {{ activity.action }}
                            </span>
                            <span class="flex-1 text-gray-600">{{ activity.description || activity.model }}</span>
                            <span class="text-xs text-gray-400">{{ activity.created_at }}</span>
                        </li>
                        <li v-if="props.recentActivities.length === 0" class="py-4 text-center text-gray-500">No activity recorded.</li>
                    </ul>
                </div>
            </div>
        </main>
    </AdminLayout>
</template>
