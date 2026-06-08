<script setup lang="ts">
import CarCard from '@/components/CarCard.vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { fleet } from '@/routes';
import { about } from '@/routes';

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
</script>

<template>
    <Head>
        <title>Real Rent Car - Premium Car Rental Service</title>
        <meta
            name="description"
            content="Real Rent Car is a premium car rental platform providing reliable transportation solutions. We offer a wide range of cars for rent, from economy to luxury, for short and long term rentals."
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
                        <div class="space-y-10">
                            <div class="space-y-6">
                                <div
                                    class="inline-flex items-center rounded-full bg-secondary px-4 py-2 text-sm font-medium text-foreground"
                                >
                                    Premium Car Rental Experience
                                </div>

                                <h1
                                    class="text-4xl leading-tight font-semibold text-foreground lg:text-7xl"
                                >
                                    Drive Your <br />
                                    <span class="text-primary">Dreams</span>
                                </h1>

                                <p
                                    class="max-w-lg text-lg leading-relaxed text-foreground/70"
                                >
                                    Experience luxury and reliability with our
                                    premium fleet. From business meetings to
                                    weekend adventures, find the perfect vehicle
                                    for every journey.
                                </p>
                            </div>

                            <div class="flex flex-col gap-4 sm:flex-row">
                                <a
                                    :href="fleet.url()"
                                    class="group cursor-pointer inline-flex items-center justify-center rounded-lg bg-primary px-8 py-3 text-md font-medium text-primary-foreground shadow-sm transition-all duration-200 hover:opacity-90"
                                >
                                    Browse Fleet
                                </a>
                                <a
                                    :href="about.url()"
                                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-border bg-transparent px-8 py-3 text-md font-medium text-foreground transition-all duration-200 hover:bg-secondary"
                                >
                                    Learn More
                                </a>
                            </div>

                            <!--  Stats -->
                            <div
                                class="grid grid-cols-3 gap-8 border-t border-border pt-10"
                            >
                                <div class="text-left">
                                    <div class="text-3xl font-semibold text-foreground">
                                        1000+
                                    </div>
                                    <div
                                        class="mt-1 text-sm font-medium text-foreground/60"
                                    >
                                        Happy Customers
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="text-3xl font-semibold text-foreground">
                                        150+
                                    </div>
                                    <div
                                        class="mt-1 text-sm font-medium text-foreground/60"
                                    >
                                        Premium Cars
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="text-3xl font-semibold text-foreground">
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
                        <div class="flex justify-center lg:justify-end">
                            <img
                                src="/images/hero_image.png"
                                alt="Premium Car Garage - Isometric View"
                                class="h-auto max-w-full rounded-2xl shadow-xl"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!--  Featured Cars Section -->
            <section id="fleet" class="bg-background py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-20 text-center">
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
                        <CarCard
                            v-for="car in homeCars"
                            :key="car.id"
                            :car="car"
                        />
                    </div>
                    
                    <div class="mt-16 text-center">
                        <a
                            :href="fleet.url()"
                            class="inline-flex cursor-pointer items-center rounded-lg border border-border bg-transparent px-8 py-3 text-md font-medium text-foreground transition-all duration-200 hover:bg-secondary"
                        >
                            View Complete Fleet
                        </a>
                    </div>
                </div>
            </section>

            <!--  Features Section -->
            <section id="services" class="bg-secondary/50 py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-20 text-center">
                        <h2
                            class="mb-6 text-3xl font-semibold text-foreground lg:text-5xl"
                        >
                            Why Choose RealRent?
                        </h2>
                        <p class="mx-auto max-w-2xl text-lg text-foreground/70">
                            We're committed to providing an unparalleled car
                            rental experience with premium service at every
                            touchpoint.
                        </p>
                    </div>

                    <div class="grid gap-12 md:grid-cols-3">
                        <div class="group text-center">
                            <h3 class="mb-4 text-xl font-semibold text-foreground">
                                Premium Quality
                            </h3>
                            <p class="leading-relaxed text-foreground/70">
                                Every vehicle undergoes comprehensive inspection
                                and maintenance to guarantee your safety,
                                comfort, and peace of mind.
                            </p>
                        </div>

                        <div class="group text-center">
                            <h3 class="mb-4 text-xl font-semibold text-foreground">
                                24/7 Support
                            </h3>
                            <p class="leading-relaxed text-foreground/70">
                                Our dedicated support team is available around
                                the clock to assist you with any questions or
                                concerns during your rental.
                            </p>
                        </div>

                        <div class="group text-center">
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
