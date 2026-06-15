<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupContent,
} from '@/components/ui/sidebar';
import { dashboard, calendar, library, historique, messagerie } from '@/customRoutes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Bibliothèque',
        href: library.url(),
    },
    {
        title: 'Calendrier',
        href: calendar.url(),
    },
    {
        title: 'Historique',
        href: historique.url(),
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Mentions légales',
        href: '/mentions-legales',
    },
    {
        title: 'Politique de confidentialité',
        href: '/politique-de-confidentialite',
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <section class="sidebar-layout">
        <SidebarHeader class="flex flex-col items-center gap-4 px-4 py-6">
            <!-- <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard.url()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu> -->

            <!-- Mon compte -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <NavUser class="primary-btn" />
                    <!-- <SidebarMenuButton as-child class="rounded-full bg-[#f0b429] px-4 py-2 text-center font-semibold text-white hover:bg-[#e0a419] hover:text-white">

                        <Link :href="dashboard.url()" class="flex items-center justify-center gap-2" style="color: black;">
                            Mon compte
                        </Link>
                    </SidebarMenuButton> -->
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Avatar -->
            <div class="h-16 w-16 rounded-full bg-gray-300" />

            <!-- Messagerie -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child class="rounded-full bg-[#f0b429] px-4 py-2 font-semibold text-white hover:bg-[#e0a419] hover:text-white">
                        <Link :href="messagerie.url()" class="flex items-center justify-center gap-2" style="color: black;">
                            <Mail class="h-4 w-4" />
                            Messagerie
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="px-4 py-4">
            <SidebarGroup>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in mainNavItems">
                            <SidebarMenuButton as-child class="text-xl font-light text-white hover:bg-transparent hover:opacity-80">
                                <Link :href="item.href">
                                    <h2>{{ item.title }}</h2>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="px-4 py-4">
            <SidebarMenu>
                <SidebarMenuItem v-for="item in footerNavItems">
                    <SidebarMenuButton as-child class="text-xs text-white/70 hover:bg-transparent hover:text-white">
                        <Link :href="item.href">
                            <h3>{{ item.title }}</h3>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
        </section>
    </Sidebar>
    <slot />
</template>