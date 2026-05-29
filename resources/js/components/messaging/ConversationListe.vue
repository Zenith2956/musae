<script setup>
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    conversations: Array,
    currentUserId: Number,
});
const emit = defineEmits(['select']);
const page = usePage();

// La conv active = celle dont l'id est dans selectedConversation passé par le contrôleur
const selectedId = page.props.selectedConversation ?? null;

function displayName(conv) {
    const others = conv.participants ?? [];

    if (others.length === 1) return others[0].name;
    if (others.length === 2) return `${others[0].name}, ${others[1].name}`;
    if (others.length > 2) return `${others[0].name}, ${others[1].name} +${others.length - 2}`;

    return 'Conversation';
}

function initials(name) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}
</script>

<template>
    <div class="conv-list">
        <div v-for="conv in conversations" :key="conv.id" class="conv-item" :class="{ active: conv.id === selectedId }"
            @click="emit('select', conv)">
            <div class="conv-avatar">
                {{ conv.participants.length > 1 ? 'G' : initials(displayName(conv)) }}
            </div>

            <div class="conv-info">
                <div class="conv-name">{{ displayName(conv) }}</div>
                <div class="conv-preview">{{ conv.last_message?.content ?? 'Nouvelle conversation' }}</div>
            </div>
            <div class="conv-meta">
                <span class="conv-time">{{ conv.updated_at_human ?? '' }}</span>
                <span v-if="conv.unread_count" class="conv-badge">{{ conv.unread_count }}</span>
            </div>
        </div>
    </div>
</template>