<script setup>
import { ref } from 'vue';

const emit = defineEmits(['send']);
const content = ref('');

function submit() {
    if (!content.value.trim()) return;

    emit('send', content.value);
    content.value = '';
}
</script>

<template>
    <div class="message-input">
        <input
            v-model="content"
            @keyup.enter="submit"
            class="message-input-field"
            placeholder="Écrire un message…"
        />

        <button
            @click="submit"
            class="message-input-button"
        >
            Envoyer
        </button>
    </div>
</template>
