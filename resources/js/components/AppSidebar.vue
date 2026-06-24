<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as carsIndex } from "@/routes/admin/cars/index";
import { index as reservationsIndex } from "@/routes/admin/reservations/index";
import { index as clientsIndex } from "@/routes/admin/clients/index";
import { index as paymentsIndex } from "@/routes/admin/payments/index";
import { index as reportsIndex } from "@/routes/admin/reports/index";
import { index as supportIndex } from "@/routes/admin/support/index";
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Car, Calendar, User, CreditCard, BarChart, LifeBuoy, LayoutDashboard, MapPin, Building2, UserCog, ScrollText } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import { home } from '@/routes';

const page = usePage();
const role = computed(() => page.props.auth.user?.role);
const isSuperAdmin = computed(() => role.value === 'super_admin');

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/admin/dashboard',
            icon: LayoutDashboard,
        },
    ];

    if (isSuperAdmin.value) {
        items.push(
            {
                title: 'Cities',
                href: '/admin/cities',
                icon: MapPin,
            },
            {
                title: 'Agencies',
                href: '/admin/agencies',
                icon: Building2,
            },
            {
                title: 'Agency Admins',
                href: '/admin/agency-admins',
                icon: UserCog,
            },
        );
    }

    items.push(
        {
            title: 'Cars',
            href: carsIndex(),
            icon: Car,
        },
        {
            title: 'Reservations',
            href: reservationsIndex(),
            icon: Calendar,
        },
        {
            title: 'Clients',
            href: clientsIndex(),
            icon: User,
        },
        {
            title: 'Payments',
            href: paymentsIndex(),
            icon: CreditCard,
        },
        {
            title: 'Reports',
            href: reportsIndex(),
            icon: BarChart,
        },
        {
            title: 'Support',
            href: supportIndex(),
            icon: LifeBuoy,
        },
        {
            title: 'Activity Logs',
            href: '/admin/activity-logs',
            icon: ScrollText,
        },
    );

    return items;
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
