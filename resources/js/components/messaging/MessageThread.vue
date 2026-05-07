<script setup>
const props = defineProps({
    messages: Array,
});
</script>

<template>
    <div class="message-thread">
        <div
            v-for="msg in props.messages"
            :key="msg.id"
            class="message-bubble"
            :class="msg.user_id === $page.props.auth.user.id ? 'bubble-me' : 'bubble-other'"
        >
            <div class="message-author">
                {{ msg.user.name }}
            </div>

            <div class="message-content">
                {{ msg.content }}
            </div>
        </div>
    </div>
</template>
