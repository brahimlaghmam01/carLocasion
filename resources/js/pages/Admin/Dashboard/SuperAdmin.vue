<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';
import { Building2, CalendarCheck, Car, CreditCard, MapPin, Users } from 'lucide-vue-next';

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
    kpis: {
        totalRevenue: string;
        totalReservations: number;
        totalVehicles: number;
        totalCustomers: number;
        totalCities: number;
        totalAgencies: number;
    };
    topAgencies: Array<{ id: number; name: string; city: string | null; formatted_revenue: string }>;
    revenueByCity: Array<{ name: string; revenue: number }>;
    reservationsByCity: Array<{ name: string; reservations: number }>;
    monthlyReservations: { labels: string[]; data: number[] };
    vehicleUtilization: { total: number; rented: number; rate: number };
    recentActivities: Activity[];
}>();

const monthlyCanvas = ref<HTMLCanvasElement | null>(null);
const cityRevenueCanvas = ref<HTMLCanvasElement | null>(null);
const utilizationCanvas = ref<HTMLCanvasElement | null>(null);

const kpiCards = [
    { key: 'totalRevenue', label: 'Total Revenue', icon: CreditCard, color: 'text-emerald-600' },
    { key: 'totalReservations', label: 'Reservations', icon: CalendarCheck, color: 'text-blue-600' },
    { key: 'totalVehicles', label: 'Vehicles', icon: Car, color: 'text-indigo-600' },
    { key: 'totalCustomers', label: 'Customers', icon: Users, color: 'text-amber-600' },
    { key: 'totalCities', label: 'Cities', icon: MapPin, color: 'text-rose-600' },
    { key: 'totalAgencies', label: 'Agencies', icon: Building2, color: 'text-cyan-600' },
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
    if (monthlyCanvas.value) {
        new Chart(monthlyCanvas.value, {
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

    if (cityRevenueCanvas.value) {
        new Chart(cityRevenueCanvas.value, {
            type: 'bar',
            data: {
                labels: props.revenueByCity.map((c) => c.name),
                datasets: [
                    {
                        label: 'Revenue',
                        data: props.revenueByCity.map((c) => c.revenue),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            },
        });
    }

    if (utilizationCanvas.value) {
        const free = Math.max(props.vehicleUtilization.total - props.vehicleUtilization.rented, 0);
        new Chart(utilizationCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ['In Use', 'Available'],
                datasets: [
                    {
                        data: [props.vehicleUtilization.rented, free],
                        backgroundColor: ['#6366f1', '#e5e7eb'],
                    },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%' },
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <h1 class="text-2xl font-semibold">Overview</h1>

            <!-- KPI cards -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
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
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Reservations (last 6 months)</h2>
                    <div class="relative h-64">
                        <canvas ref="monthlyCanvas"></canvas>
                    </div>
                </div>
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Fleet Utilization</h2>
                    <div class="relative h-64">
                        <canvas ref="utilizationCanvas"></canvas>
                    </div>
                    <p class="mt-3 text-center text-sm text-gray-500">
                        <span class="font-semibold text-gray-900">{{ props.vehicleUtilization.rate }}%</span> of fleet in use
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Revenue by City</h2>
                    <div class="relative h-64">
                        <canvas ref="cityRevenueCanvas"></canvas>
                    </div>
                </div>

                <!-- Top agencies -->
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Top Agencies</h2>
                    <ul class="divide-y divide-gray-100">
                        <li v-for="agency in props.topAgencies" :key="agency.id" class="flex items-center justify-between py-2">
                            <div>
                                <div class="font-medium">{{ agency.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ agency.city || '—' }}</div>
                            </div>
                            <span class="text-sm font-semibold text-emerald-600">{{ agency.formatted_revenue }}</span>
                        </li>
                        <li v-if="props.topAgencies.length === 0" class="py-4 text-center text-gray-500">No data yet.</li>
                    </ul>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="rounded-lg border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-gray-700">Recent Activity</h2>
                <ul class="divide-y divide-gray-100">
                    <li v-for="activity in props.recentActivities" :key="activity.id" class="flex items-center gap-3 py-2 text-sm">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="actionClass(activity.action)">
                            {{ activity.action }}
                        </span>
                        <span class="flex-1 text-gray-600">{{ activity.description || activity.model }}</span>
                        <span class="text-xs text-gray-400">{{ activity.user }} · {{ activity.created_at }}</span>
                    </li>
                    <li v-if="props.recentActivities.length === 0" class="py-4 text-center text-gray-500">No activity recorded.</li>
                </ul>
            </div>
        </main>
    </AdminLayout>
</template>
