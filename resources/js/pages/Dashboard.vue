<script setup lang="ts">

import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
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

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('fr-FR', {
        dateStyle: 'medium',
        timeStyle: 'short'
    });
};

</script>

<template>
    <!-- <h1>Dashboard</h1>
    <h2></h2>
    <p v-for="sheet in sheets" :key="sheet.id">
        {{ sheet.name }}
    </p> -->

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-if="nextTraining"
                    class="rounded-xl border p-4 bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
                    <h2 class="text-lg font-semibold mb-2">
                        Prochain entraînement 🎯
                    </h2>

                    <p class="text-sm">
                        <strong>{{ nextTraining.name }}</strong>
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ new Date(nextTraining.date_training).toLocaleString() }}
                    </p>

                    <p v-if="nextTraining.duration" class="text-sm">
                        Durée : {{ nextTraining.duration }} min
                    </p>

                    <p v-if="nextTraining.instrument" class="text-sm">
                        Instrument : {{ nextTraining.instrument.name }}
                    </p>

                    <p v-if="nextTraining.sheet" class="text-sm">
                        Partition : {{ nextTraining.sheet.name }}
                    </p>
                </div>

                <div v-else class="rounded-xl border p-4 text-gray-500">
                    Aucun entraînement à venir 😢
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
