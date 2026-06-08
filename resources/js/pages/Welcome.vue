<script setup lang="ts">
import CarCard from '@/components/CarCard.vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { fleet } from '@/routes';
import { about } from '@/routes';
import CarHero from "@/components/CarHero.vue"


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
            <!--  Hero 3d cars-->
          <div>
  <CarHero />
</div>

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
