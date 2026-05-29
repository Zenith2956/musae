<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    disabled: { type: Boolean, default: false }
});
const emit    = defineEmits(['send']);
const content = ref('');

function submit() {
    if (!content.value.trim() || props.disabled) return;
    emit('send', content.value);
    content.value = '';
}
</script>

<template>
    <div style="display:flex; gap:0.6rem;">
        <input
            v-model="content"
            @keyup.enter="submit"
            :disabled="disabled"
            class="input"
            style="flex:1;"
            :placeholder="disabled ? 'Sélectionnez une conversation...' : 'Écrire un message...'"
        />
        <button
            @click="submit"
            :disabled="disabled"
            class="btn-yellow"
            :style="{
                borderRadius: '0.75rem',
                padding: '0.55rem 1.2rem',
                opacity: disabled ? 0.5 : 1,
                cursor: disabled ? 'not-allowed' : 'pointer',
            }"
        >
            Envoyer
        </button>
    </div>
</template>