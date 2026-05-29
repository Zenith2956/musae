<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import HistoriqueItem from '@/components/HistoriqueItem.vue';
import HistoriqueDetail from '@/components/HistoriqueDetail.vue'

import { ref } from 'vue'

const selected = ref<string | null>(null)


function toggle(name: string) {
    selected.value = selected.value === name ? null : name
}

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

</script>

<template>
    <Head title="Historique" />
    <AppLayout>
        <div class="container mx-auto p-4">

            <h1 class="text-2xl font-bold">Historique de vos entraînements</h1>

            <div v-for="(trainings, name) in grouped" :key="name" class="mt-4">

                <HistoriqueItem
                    :name="name"
                    :trainings="trainings"
                    @open="toggle(name)"
                />

                <HistoriqueDetail
                    v-if="selected === name"
                    :name="name"
                    :trainings="trainings"
                    :instrumentMap="instrumentMap"
                    :sheetsMap="sheetsMap"
                />


            </div>

        </div>
    </AppLayout>
</template>

