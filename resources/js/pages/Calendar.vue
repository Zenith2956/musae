<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { calendar } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { VueCal } from 'vue-cal'
import 'vue-cal/style'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Calendar',
        href: calendar(),
    },
];
const specialHours = {
  const specialHours = {
  mon: { from: 9 * 60, to: 18 * 60, class: 'business-hours' },
  tue: { from: 9 * 60, to: 18 * 60, class: 'business-hours' },
  wed: [
    { from: 9 * 60, to: 12 * 60, class: 'business-hours' },
    { from: 14 * 60, to: 18 * 60, class: 'business-hours' }
  ],
  thu: { from: 9 * 60, to: 18 * 60, class: 'business-hours' },
  fri: { from: 9 * 60, to: 18 * 60, class: 'business-hours' }
  }
};
</script>


<template>
    <Head title="Calendar" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <vue-cal
            view="month"
            :views="['day', 'days', 'month', 'year', 'years']"
            time-at-cursor
            week-numbers watch-real-time
            :special-hours="specialHours"
            />
    </AppLayout>
</template>
