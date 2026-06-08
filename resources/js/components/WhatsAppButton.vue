<template>
    <!-- Fixed WhatsApp Button -->
    <a
        :href="`https://wa.me/${whatsappNumber}?text=Hello%20RealRent%20Car`"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed bottom-8 right-8 z-50 group hover-lift animate-fade-in"
    >
        <!-- Button -->
        <div
            class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-2xl transition-all duration-300 hover:from-green-500 hover:to-green-700 hover:scale-110 hover:shadow-2xl"
        >
            <!-- WhatsApp Icon -->
            <svg
                class="h-8 w-8 text-white"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-4.99 1.516c-2.73 1.813-4.335 4.589-4.335 7.654 0 1.355.27 2.688.787 3.937L.5 23.5l4.093-1.351c1.185.634 2.475.963 3.777.963 5.366 0 9.733-4.367 9.733-9.733 0-2.56-1.025-4.97-2.889-6.78-1.864-1.81-4.342-2.805-6.769-2.805m9.733 17.866c-1.175 0-2.336-.244-3.435-.694l-.246-.13-2.56.844.866-2.578-.167-.268c-.468-.74-.725-1.588-.725-2.466 0-4.264-3.47-7.733-7.733-7.733-2.057 0-3.99.812-5.44 2.263-1.45 1.451-2.264 3.384-2.264 5.44 0 4.264 3.47 7.733 7.733 7.733 1.882 0 3.639-.695 5.04-1.957l.219-.198 2.657.876-.856-2.56.237-.385"
                ></path>
            </svg>
        </div>

        <!-- Tooltip -->
        <div
            class="absolute bottom-0 right-20 mb-2 hidden transform whitespace-nowrap rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-xl transition-all duration-300 group-hover:block group-hover:animate-fade-in"
        >
            Chat with us on WhatsApp
            <div
                class="absolute right-0 top-1/2 h-0 w-0 -translate-y-1/2 border-8 border-transparent border-l-gray-900"
            ></div>
        </div>
    </a>
</template>

<script setup lang="ts">
defineProps<{
    whatsappNumber: string;
}>();
</script>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}
</style>
