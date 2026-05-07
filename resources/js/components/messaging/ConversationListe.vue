<script setup>
const props = defineProps({
    conversations: Array,
});
const emit = defineEmits(['select']);
</script>

<template>
    <div class="conversation-list">
        <div
            v-for="conv in props.conversations"
            :key="conv.id"
            class="conversation-item"
            @click="emit('select', conv)"
        >
            <div class="conversation-title">
                {{ conv.participants.map(u => u.name).join(', ') }}
            </div>

            <div class="conversation-preview">
                {{ conv.last_message?.content ?? 'Aucun message' }}
            </div>
        </div>
    </div>
</template>
