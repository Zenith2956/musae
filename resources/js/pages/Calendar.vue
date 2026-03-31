
<script setup lang="ts">
import { ref } from 'vue'
import { VueCal } from 'vue-cal'
import 'vue-cal/style'

import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { calendar } from '@/routes'

import { addDatePrototypes } from 'vue-cal'

// nécessaire pour certaines fonctions date de vue-cal
addDatePrototypes()

const breadcrumbs: BreadcrumbItem[] = [{
    title: 'Calendar',
    href: calendar(),
}, ]
interface Event {
    id: string;
    title: string;
    start: Date;
    end: Date;
}
const events = ref<Event[]>([])

const loading = ref(false)

/*
Charge les événements depuis Laravel
*/
const fetchEvents = async (start: string, end: string) => {
    loading.value = true;

    try {
        const response = await fetch(`/calendar/events?start=${start}&end=${end}`);
        const data = await response.json();

        console.log("Données récupérées:", data);

        // Typage explicite pour 'events'
        events.value = data.map((event: any) => {
            console.log("Event Start:", event.start);
            console.log("Event End:", event.end);
            return {
                id: String(event.id),  // Convertir l'ID en string
                title: String(event.title),  // Convertir le titre en string
                start: new Date(event.start),
                end: new Date(event.end)
                };
            });

    } catch (error) {
        console.error("Erreur lors du chargement des événements:", error);
    } finally {
        loading.value = false;
    }
};

/*
Quand le calendrier est prêt
*/
const onReady = ({ view }: any) => {

    fetchEvents(view.start.format('YYYY-MM-DD HH:mm:ss'), view.end.format('YYYY-MM-DD HH:mm:ss'));
}

/*
Quand on change de semaine/mois/jour
*/
const onViewChange = (view: any) => {

    fetchEvents(view.start.format('YYYY-MM-DD HH:mm:ss'), view.end.format('YYYY-MM-DD HH:mm:ss'));

}

</script>

<template>
    <Head title="Calendar" />
    
    
    <AppLayout :breadcrumbs="breadcrumbs">
    
        <div class="p-4">
    
            <vue-cal
                :events="events"
                :time-from="8 * 60"
                :time-to="19 * 60"
                :views-bar="true"
                :events-on-month-view="true"
                :snap-to-interval="5"
                editable-events @ready="onReady" @view-change="onViewChange" />
    
        </div>
    
    </AppLayout>
</template>

<style scoped>
.vuecal {
    height: 650px;
}
</style>