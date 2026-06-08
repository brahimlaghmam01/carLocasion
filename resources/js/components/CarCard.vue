<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { show } from '@/routes/fleet';
import { ref } from 'vue';

interface Car {
    id: number;
    make: string;
    model: string;
    year: number;
    price_per_day: string;
    description: string;
    fuel_type: string;
    image_url: string;
}

interface Props {
    car: Car;
}

const bookCar = (carId: number) => {
    router.get(show(carId).url);
};

const isHovered = ref(false);

defineProps<Props>();
</script>

<template>
    <div
        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg transition-all duration-300 hover:shadow-2xl card-hover-effect"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Car Image -->
        <div
            class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100"
        >
            <img
                :src="car.image_url"
                :alt="`${car.make} ${car.model}`"
                class="h-full w-full object-cover transition-all duration-500 group-hover:scale-110"
            />

            <!-- Price Badge -->
            <div
                class="absolute top-4 right-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 shadow-lg transition-all duration-300 transform group-hover:scale-110 group-hover:shadow-xl"
            >
                <span class="text-sm font-bold text-white"
                    >DH{{ car.price_per_day }}</span
                >
                <span class="text-xs text-blue-100">/day</span>
            </div>

            <!-- Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
            ></div>

            <!-- Special Badge -->
            <div
                class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-semibold text-blue-600 transition-all duration-300 transform group-hover:translate-y-0 translate-y-10 group-hover:opacity-100 opacity-0"
            >
                ✨ Premium Available
            </div>
        </div>

        <!--  Car Details -->
        <div class="space-y-4 p-4">
            <!-- Header -->
            <div class="space-y-2">
                <h3
                    class="text-xl font-bold text-gray-900 transition-colors duration-300 group-hover:text-blue-600"
                >
                    {{ car.make }} <span class="text-blue-500">{{ car.model }}</span> 
                </h3>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">{{ car.year }}</div>
                    <div class="text-sm text-gray-500 font-medium">ID: {{ car.id }}</div>
                </div>
            </div>

            <!-- Features -->
            <div class="flex flex-wrap gap-2 items-center">
                <div class="flex items-center gap-1 capitalize px-2 py-1 rounded-lg bg-blue-50 transition-all duration-300 group-hover:bg-blue-100">
                    <svg
                        class="h-4 w-4 text-blue-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        ></path>
                    </svg>
                    <span class="font-medium text-sm">{{ car.fuel_type }}</span>
                </div>
                <div class="text-xs bg-slate-400 px-2 py-1 rounded-md text-white transition-all duration-300 hover:bg-slate-500">
                    <p>🛰️ GPS</p>
                </div>
                <div class="text-xs bg-slate-400 px-2 py-1 rounded-md text-white transition-all duration-300 hover:bg-slate-500">
                    <p>🛡️ Insurance</p>
                </div>
            </div>

            <!-- Description -->
            <p class="line-clamp-2 text-sm leading-relaxed text-gray-600">
                {{ car.description }}
            </p>

            <!-- Quick Stats -->
            <div 
                v-if="isHovered"
                class="grid grid-cols-3 gap-2 text-center bg-gray-50 p-2 rounded-lg animate-fade-in"
            >
                <div>
                    <div class="text-lg font-bold text-blue-500">✓</div>
                    <div class="text-xs text-gray-500">Verified</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-blue-500">24h</div>
                    <div class="text-xs text-gray-500">Support</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-blue-500">★</div>
                    <div class="text-xs text-gray-500">Premium</div>
                </div>
            </div>
        </div>
        <!--  Book Button -->
        <div class="p-4 space-y-2">
            <button
                @click="bookCar(car.id)"
                class="group/btn w-full cursor-pointer rounded-xl bg-gradient-to-r from-slate-700 to-slate-900 px-6 py-3.5 font-semibold text-white shadow-lg transition-all duration-300 hover:from-blue-600 hover:to-blue-700 hover:scale-105 hover:shadow-xl focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none active:scale-95"
            >
                <span
                    class="flex items-center justify-center gap-2 text-blue-500 group-hover/btn:text-white transition-colors"
                >
                    <svg
                        class="h-5 w-5 transition-transform duration-300 group-hover/btn:scale-110 group-hover/btn:rotate-12"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        ></path>
                    </svg>
                    Book Now
                </span>
            </button>
            <a
                :href="`/fleet/${car.id}`"
                class="block w-full text-center px-6 py-2.5 font-medium text-gray-700 border-2 border-gray-200 rounded-xl transition-all duration-300 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 group-hover:bg-blue-50"
            >
                View Details
            </a>
        </div>
    </div>
</template>
