<script setup lang="ts">

import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import TrainingCards from '@/components/TrainingCards.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const page = usePage();

const nextTraining = page.props.nextTraining as any;

</script>

<template>
    <!-- <h1>Dashboard</h1>
    <h2></h2>
    <p v-for="sheet in sheets" :key="sheet.id">
        {{ sheet.name }}
    </p> -->

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <training-cards :next-training="nextTraining" />

                <div
                    >
                    
                </div>
                <div
                    >
                </div>
            </div>
            <div
                >
            </div>
        </div>
    </AppLayout>
</template>
