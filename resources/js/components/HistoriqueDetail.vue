<script setup lang="ts">
const props = defineProps<{
    name: string
    trainings: any[]
    instrumentMap: Record<number, string>
    sheetsMap: Record<number, string>
}>()

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const count = props.trainings.length
const last = props.trainings[0]
</script>

<template>
    <div class="history-detail-card">

        <h2 class="history-detail-title">{{ name }}</h2>

        <p class="history-detail-meta">
            Nombre d’entraînements : <strong>{{ count }}</strong>
        </p>

        <p class="history-detail-meta">
            Dernier : {{ formatDate(last.date_training) }}
        </p>

        <p class="history-detail-meta">
            Instrument : {{ instrumentMap[last.instrument_id] || 'Inconnu' }}
        </p>

        <p class="history-detail-meta">
            Partition : {{ last.sheet ? sheetsMap[last.sheet.id] : 'Sans feuille' }}
        </p>

        <h3 class="history-detail-subtitle">Toutes les dates</h3>

        <ul class="history-detail-list">
            <li v-for="t in trainings" :key="t.id">
                {{ formatDate(t.date_training) }}
            </li>
        </ul>

    </div>
</template>
