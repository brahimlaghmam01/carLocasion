<script setup lang="ts">
import CarCard from '@/components/CarCard.vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { fleet } from '@/routes';
import { about } from '@/routes';
import { ref, onMounted } from 'vue';

interface Car {
    id: number;
    make: string;
    model: string;
    year: number;
    price_per_day: string;
    description: string;
    fuel_type: string;
    image_url: string;
    color?: string;
    status?: string;
    license_plate?: string;
    image?: string;
}

const $page = usePage();
const homeCars = $page.props.homeCars as Car[];

// Intersection Observer for scroll animations
const observedElements = ref<Map<Element, boolean>>(new Map());

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const classList = entry.target.classList;
                    if (classList.contains('animate-on-scroll-up')) {
                        classList.add('animate-slide-in-up');
                    } else if (classList.contains('animate-on-scroll-left')) {
                        classList.add('animate-slide-in-left');
                    } else if (classList.contains('animate-on-scroll-right')) {
                        classList.add('animate-slide-in-right');
                    }
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1, rootMargin: '50px' }
    );

    document.querySelectorAll('.animate-on-scroll-up, .animate-on-scroll-left, .animate-on-scroll-right').forEach((el) => {
        // ensure element is initially hidden to avoid flash
        el.classList.add('will-animate');
        observer.observe(el);
    });
});
</script>

<template>
    <Head>
        <title>Car Location - Premium Car Rental Service</title>
        <meta
            name="description"
            content="Car Location is a premium car rental platform providing reliable transportation solutions. We offer a wide range of cars for rent, from economy to luxury, for short and long term rentals."
        />
    </Head>

    <HomeLayout>
        <main>
            <!--  Hero Section with Neutral Background -->
            <section
                class="relative overflow-hidden bg-background py-20"
            >
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid items-center gap-16 lg:grid-cols-2">
                        <!--  Left Content -->
                        <div class="space-y-10 animate-fade-in-left">
                            <div class="space-y-6">
                                <div
                                    class="inline-flex items-center rounded-full bg-secondary px-4 py-2 text-sm font-medium text-foreground animate-stagger-1"
                                >
                                    Premium Car Rental Experience
                                </div>

                                <h1
                                    class="text-4xl leading-tight font-semibold text-foreground lg:text-7xl animate-stagger-2"
                                >
                                    Drive Your <br />
                                    <span class="text-primary">Dreams</span>
                                </h1>

                                <p
                                    class="max-w-lg text-lg leading-relaxed text-foreground/70 animate-stagger-3"
                                >
                                    Experience luxury and reliability with our
                                    premium fleet. From business meetings to
                                    weekend adventures, find the perfect vehicle
                                    for every journey.
                                </p>
                            </div>

                            <div class="flex flex-col gap-4 sm:flex-row animate-stagger-4">
                                <a
                                    :href="fleet.url()"
                                    class="group cursor-pointer inline-flex items-center justify-center rounded-lg bg-primary px-8 py-3 text-md font-medium text-primary-foreground shadow-sm transition-all duration-300 hover:opacity-90 hover-lift"
                                >
                                    Browse Fleet
                                    <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                                <a
                                    :href="about.url()"
                                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-border bg-transparent px-8 py-3 text-md font-medium text-foreground transition-all duration-300 hover:bg-secondary hover-lift relative overflow-hidden group"
                                >
                                    <span class="relative z-10">Learn More</span>
                                    <span class="absolute inset-0 bg-primary/5 scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></span>
                                </a>
                            </div>

                            <!--  Stats -->
                            <div
                                class="grid grid-cols-3 gap-8 border-t border-border pt-10 animate-stagger-5"
                            >
                                <div class="text-left group cursor-pointer">
                                    <div class="text-3xl font-semibold text-foreground transition-transform group-hover:scale-110">
                                        1000+
                                    </div>
                                    <div
                                        class="mt-1 text-sm font-medium text-foreground/60"
                                    >
                                        Happy Customers
                                    </div>
                                </div>
                                <div class="text-left group cursor-pointer">
                                    <div class="text-3xl font-semibold text-foreground transition-transform group-hover:scale-110">
                                        150+
                                    </div>
                                    <div
                                        class="mt-1 text-sm font-medium text-foreground/60"
                                    >
                                        Premium Cars
                                    </div>
                                </div>
                                <div class="text-left group cursor-pointer">
                                    <div class="text-3xl font-semibold text-foreground transition-transform group-hover:scale-110">
                                        24/7
                                    </div>
                                    <div
                                        class="mt-1 text-sm font-medium text-foreground/60"
                                    >
                                        Support
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Image -->
                        <div class="flex justify-center lg:justify-end animate-fade-in-right">
                            <img
                                src="/images/hero_image.png"
                                alt="Premium Car Garage - Isometric View"
                                class="h-auto max-w-full rounded-2xl shadow-xl transition-transform duration-500 hover:scale-105 hover:shadow-2xl"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!--  Featured Cars Section -->
            <section id="fleet" class="bg-background py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-20 text-center animate-on-scroll-up">
                        <h2
                            class="mb-6 text-3xl font-semibold text-foreground lg:text-5xl"
                        >
                            Discover Our Elite Fleet
                        </h2>
                        <p
                            class="mx-auto max-w-3xl text-lg leading-relaxed text-foreground/70"
                        >
                            Each vehicle in our collection is meticulously
                            maintained and equipped with premium features to
                            ensure your journey is nothing short of exceptional.
                        </p>
                    </div>

                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(car, index) in homeCars"
                            :key="car.id"
                            class="animate-on-scroll-up"
                            :style="{ animationDelay: `${index * 0.1}s` }"
                        >
                            <CarCard :car="car" />
                        </div>
                    </div>
                    
                    <div class="mt-16 text-center animate-on-scroll-up">
                        <a
                            :href="fleet.url()"
                            class="inline-flex cursor-pointer items-center rounded-lg border border-border bg-transparent px-8 py-3 text-md font-medium text-foreground transition-all duration-300 hover:bg-secondary hover-lift relative overflow-hidden group"
                        >
                            <span class="relative z-10">View Complete Fleet</span>
                            <span class="absolute inset-0 bg-primary/5 scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></span>
                        </a>
                    </div>
                </div>
            </section>

            <!--  Features Section -->
            <section id="services" class="bg-secondary/50 py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-20 text-center animate-on-scroll-up">
                        <h2
                            class="mb-6 text-3xl font-semibold text-foreground lg:text-5xl"
                        >
                            Why Choose CarLocation?
                        </h2>
                        <p class="mx-auto max-w-2xl text-lg text-foreground/70">
                            We're committed to providing an unparalleled car
                            rental experience with premium service at every
                            touchpoint.
                        </p>
                    </div>

                    <div class="grid gap-12 md:grid-cols-3">
                        <div class="group text-center animate-on-scroll-up card-hover-effect">
                            <div class="mb-4 flex justify-center">
                                <div class="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <svg class="h-7 w-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mb-4 text-xl font-semibold text-foreground">
                                Premium Quality
                            </h3>
                            <p class="leading-relaxed text-foreground/70">
                                Every vehicle undergoes comprehensive inspection
                                and maintenance to guarantee your safety,
                                comfort, and peace of mind.
                            </p>
                        </div>

                        <div class="group text-center animate-on-scroll-up card-hover-effect" style="animation-delay: 0.1s">
                            <div class="mb-4 flex justify-center">
                                <div class="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <svg class="h-7 w-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mb-4 text-xl font-semibold text-foreground">
                                24/7 Support
                            </h3>
                            <p class="leading-relaxed text-foreground/70">
                                Our dedicated support team is available around
                                the clock to assist you with any questions or
                                concerns during your rental.
                            </p>
                        </div>

                        <div class="group text-center animate-on-scroll-up card-hover-effect" style="animation-delay: 0.2s">
                            <div class="mb-4 flex justify-center">
                                <div class="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <svg class="h-7 w-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mb-4 text-xl font-semibold text-foreground">
                                Best Value
                            </h3>
                            <p class="leading-relaxed text-foreground/70">
                                Transparent pricing with no hidden fees. Get
                                premium car rental services at competitive rates
                                with exceptional value.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </HomeLayout>
</template>
