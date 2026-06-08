<script setup lang="ts">
import { login, register } from '@/routes';
import { index as adminCarsIndex } from '@/routes/admin/cars/index';
import { index as clientReservationsIndex } from '@/routes/client/reservations/index';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { home } from '@/routes';
import { fleet } from '@/routes';
import { about } from '@/routes';
import { contact } from '@/routes';
import { ref, onMounted } from 'vue';
import WhatsAppButton from '@/components/WhatsAppButton.vue';

const $page = usePage();

const role = $page.props.auth.user?.role;

const dashboardLink = role === 'admin' ? adminCarsIndex() : clientReservationsIndex();

// For scroll effect
const isScrolled = ref(false);
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 0;
    });
}

onMounted(() => {
    // Auto-add animate-on-scroll to top-level page sections so pages
    // get a subtle reveal when scrolling without editing every page.
    try {
        document.querySelectorAll('#page-content > *').forEach((el) => {
            if (!el.classList.contains('animate-on-scroll')) {
                el.classList.add('animate-on-scroll');
                // default animation if none specified
                if (!(el as HTMLElement).dataset.anim) (el as HTMLElement).dataset.anim = 'animate-fade-in-up';
            }
        });
    } catch (e) {
        // noop in SSR / environments without document
    }
});
</script>

<template>
    <div>
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur-md transition-all duration-300"
            :class="isScrolled ? 'shadow-lg' : 'shadow-sm'"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <nav class="flex h-16 items-center justify-between">
                    <!--  Logo -->
                    <div class="flex flex-col items-center space-x-2 animate-fade-in-left">
                        <img 
                            src="/logo/logo.png" 
                            alt="logo" 
                            class="h-6 transition-transform duration-300 hover:scale-110" 
                        />
                        <p class="font-bold">
                            CAR<span class="text-blue-500">LOCA</span>TION
                        </p>
                    </div>

                    <!--  Navigation -->
                    <div class="hidden items-center space-x-8 md:flex">
                        <Link 
                            :href="home()" 
                            :class="{ 'text-blue-500': $page.url === home().url, 'text-gray-700': $page.url !== home().url }" 
                            class="font-medium transition-all duration-300 hover:text-blue-500 relative group animate-fade-in"
                        >
                            Home
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                        </Link>
                        <Link 
                            :href="fleet()" 
                            :class="{ 'text-blue-500': $page.url.startsWith('/fleet'), 'text-gray-700': !$page.url.startsWith('/fleet') }" 
                            class="font-medium transition-all duration-300 hover:text-blue-500 relative group animate-fade-in"
                            style="animation-delay: 0.05s"
                        >
                            Fleet
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                        </Link>
                        <Link 
                            :href="about()" 
                            :class="{ 'text-blue-500': $page.url === '/about', 'text-gray-700': $page.url !== '/about' }" 
                            class="font-medium transition-all duration-300 hover:text-blue-500 relative group animate-fade-in"
                            style="animation-delay: 0.1s"
                        >
                            About
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                        </Link>
                        <Link 
                            :href="contact()" 
                            :class="{ 'text-blue-500': $page.url === '/contact', 'text-gray-700': $page.url !== '/contact' }" 
                            class="font-medium transition-all duration-300 hover:text-blue-500 relative group animate-fade-in"
                            style="animation-delay: 0.15s"
                        >
                            Contact
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                        </Link>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3 animate-fade-in-right">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboardLink"
                            class="inline-flex items-center rounded-xl bg-gray-50 px-6 py-2.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-100 hover:shadow-md hover-lift"
                        >
                            <svg
                                class="mr-2 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                ></path>
                            </svg>
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="inline-flex items-center px-6 py-2.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:text-blue-600 relative group"
                            >
                                Sign In
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                            </Link>
                            <Link
                                :href="register()"
                                class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-blue-600 hover:to-blue-700 hover:shadow-xl hover-lift"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <WhatsAppButton whatsappNumber="+212770876664" />

        <main id="page-content">
            <slot />
        </main>

        <!--  Footer -->
        <footer class="bg-gray-900 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 md:grid-cols-4">
                    <div class="space-y-6 animate-stagger-1">
                        <div class="flex items-center space-x-2 group hover-lift">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 transition-transform duration-300 group-hover:scale-110"
                            >
                                <svg
                                    class="h-6 w-6 text-white"
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
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">
                                    REAL<span class="text-blue-500"
                                        >RENT</span
                                    >
                                </h3>
                                <p class="text-xs font-medium text-gray-400">
                                    PREMIUM CARS
                                </p>
                            </div>
                        </div>
                        <p class="leading-relaxed text-gray-400">
                            Premium car rental service providing luxury and
                            reliable vehicles for all your transportation needs
                            with exceptional customer service.
                        </p>
                    </div>

                    <div class="space-y-6 animate-stagger-2">
                        <h4 class="text-lg font-semibold">Services</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Luxury Car Rental</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Long Term Rental</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Corporate Solutions</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Airport Transfers</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-6 animate-stagger-3">
                        <h4 class="text-lg font-semibold">Support</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li>
                                <a
                                    :href="contact.url()"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Contact Us</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Help Center</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Terms & Conditions</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all duration-300 hover:text-blue-500 hover:translate-x-1 inline-block"
                                    >Privacy Policy</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-6 animate-stagger-4">
                        <h4 class="text-lg font-semibold">Contact Info</h4>
                        <div class="space-y-3 text-gray-400">
                            <div class="flex items-center space-x-3 transition-all duration-300 hover:text-blue-500 hover:translate-x-1 cursor-pointer">
                                <svg
                                    class="h-5 w-5 text-blue-500 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    ></path>
                                </svg>
                                <span>+212 6 123-4567</span>
                            </div>
                            <div class="flex items-center space-x-3 transition-all duration-300 hover:text-blue-500 hover:translate-x-1 cursor-pointer">
                                <svg
                                    class="h-5 w-5 text-blue-500 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <span>car@location.com</span>
                            </div>
                            <div class="flex items-center space-x-3 transition-all duration-300 hover:text-blue-500 hover:translate-x-1 cursor-pointer">
                                <svg
                                    class="h-5 w-5 text-blue-500 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                </svg>
                                <span>Mont fleurie 2, Fes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 border-t border-gray-800 pt-8 animate-fade-in">
                   
                        <p class="text-gray-400 text-center">
                            &copy; 2025 Car Location. All rights reserved.
                        </p>
                       
                </div>
            </div>
        </footer>
    </div>
</template>
