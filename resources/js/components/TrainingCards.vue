<script setup>
defineProps({
    nextTraining: {
        type: Object,
        default: null
    }
});
</script>

<template>
    <!-- ── Prochain entraînement ──────────────────────────── -->
    <div v-if="nextTraining" class="training-card">

        <div class="training-card__header">
            <h2 style="color: var(--white);">Prochain entraînement</h2>
        </div>

        <div class="training-card__body">

            <p class="training-card__name">{{ nextTraining.name }}</p>

            <div class="training-card__meta">

                <div class="meta-row">
                    <span class="meta-icon">📅</span>
                    <span>{{ new Date(nextTraining.date_training).toLocaleString('fr-FR') }}</span>
                </div>

                <div v-if="nextTraining.duration" class="meta-row">
                    <span class="meta-icon">⏱</span>
                    <span>{{ nextTraining.duration }} min</span>
                </div>

                <div v-if="nextTraining.instrument" class="meta-row">
                    <span class="meta-icon">🎸</span>
                    <span>{{ nextTraining.instrument.name }}</span>
                </div>

                <div v-if="nextTraining.sheet" class="meta-row">
                    <span class="meta-icon">🎼</span>
                    <span>{{ nextTraining.sheet.name }}</span>
                </div>

            </div>
        </div>

    </div>

    <!-- ── Aucun entraînement ─────────────────────────────── -->
    <div v-else class="training-card training-card--empty">
        <span class="empty-icon"></span>
        <p>Aucun entraînement à venir</p>
    </div>
</template>

