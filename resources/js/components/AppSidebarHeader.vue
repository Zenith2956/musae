<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="navbar flex h-16 shrink-0 items-center justify-between gap-2 px-4 text-white shadow-sm md:hidden">
        <div class="flex items-center gap-3">
            <SidebarTrigger class="-ml-1" />
        </div>
        <img class="logo" src="../../assets/logo.png" alt="Logo"/>
        <template v-if="breadcrumbs && breadcrumbs.length > 0">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </template>
    </header>
</template>
