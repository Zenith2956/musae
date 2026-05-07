<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';


interface Training {
    id: number;
    name: string;
    date_training: string;
    duration: number;
    link?: string;
    instrument_id: number;
    sheet?: { id: number; name: string };
}

interface Instrument {
    id: number;
    name: string;
}

interface Sheet {
    id: number;
    name: string;
}

const props = defineProps<{
    sheets: Sheet[],
    trainings: Training[],
    instruments: Instrument[],
}>()

const instrumentMap = computed(() => {
    const map: Record<number, string> = {};
    Array.isArray(props.instruments) && props.instruments.forEach(i => {
        map[i.id] = i.name;
    });
    return map;
});
 
const sheetsMap = computed(() => {
    const map: Record<number, string> = {};
    Array.isArray(props.sheets) && props.sheets.forEach(s => {
        map[s.id] = s.name;
    });
    return map;
});

const grouped = computed(() => {
    const map: Record<string, Training[]> = {}
    Array.isArray(props.trainings) && props.trainings.forEach(t => {
        if (!map[t.name]) map[t.name] = [];
        map[t.name].push(t);
    });
    return map;
})

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<template>

    <Head title="Historique" />
    <AppLayout>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold">Historique de vos entraînements</h1>
            <div v-for="(trainings, name) in grouped" :key="name" style="margin-top: 1em;">
                <h2 class="text-xl font-semibold mb-2">{{ name }}</h2>
                <ul class="bg-white shadow rounded-lg space-y space-y-2 p-4">
                    <li v-for="training in trainings" :key="training.id" class="container mx-auto p-4 bg-gray-50 rounded">
                            <div class="min-w-0 container mx-auto p-4 bg-gray-50 rounded">
                                <p class="text-sm text-gray-500">{{ formatDate(training.date_training) }}</p>
                                <p class="text-sm text-gray-500">Feuille : {{ training.sheet ? sheetsMap[training.sheet.id] : 'Sans feuille' }}</p>
                                <p class="text-sm text-gray-500">Instrument : {{ instrumentMap[training.instrument_id] || 'Inconnu' }}</p>
                                <p class="text-sm text-gray-500">Durée : {{ training.duration }} minutes</p>
                                <p v-if="training.link" class="text-sm text-blue-500"><a :href="training.link" target="_blank">{{ training.link }}</a></p>
                            </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>