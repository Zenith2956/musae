<script setup lang="ts">
const props = defineProps<{
    name: string
    trainings: any[]
}>()

const emit = defineEmits(['open'])

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit'
    })
}

const last = props.trainings[0] // le plus récent
</script>

<template>
    <div class="history-card">
        <div class="history-card-left">
            <p class="history-title">{{ name }}</p>
            <p class="history-meta">Dernière date : {{ formatDate(last.date_training) }}</p>
            <p class="history-meta">Durée : {{ last.duration }} min</p>
        </div>

        <button class="btn-primary" @click="emit('open')">
            Afficher
        </button>
    </div>
</template>
