<script setup>
import { ref, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({ messages: Array });
const currentUserId = usePage().props.auth.user.id;
const threadEl = ref(null);

watch(
    () => props.messages?.length,
    async () => {
        await nextTick();
        if (threadEl.value) {
            threadEl.value.scrollTop = threadEl.value.scrollHeight;
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="message-thread" ref="threadEl">
        <template v-if="messages?.length">
            <div
                v-for="msg in messages"
                :key="msg.id"
                class="message-bubble"
                :class="msg.user_id === currentUserId ? 'bubble-me' : 'bubble-other'"
            >
                <div class="message-author">{{ msg.user?.name }}</div>
                <div class="message-content">{{ msg.content }}</div>
            </div>
        </template>
        <div v-else style="margin: auto; color: #9ca3af; font-size: 0.9rem; text-align: center;">
            Aucun message. Commencez la conversation !
        </div>
    </div>
</template>