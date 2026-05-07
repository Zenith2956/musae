<script setup>
import { ref } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue'
import ConversationListe from '@/components/messaging/ConversationListe.vue';
import MessageThread from '@/components/messaging/MessageThread.vue';
import MessageInput from '@/components/messaging/MessageInput.vue';

const props = defineProps({
    conversations: Array,
    users: Array,
    selectedConversation: Number,
    messages: Array,
});

const selected = ref(props.selectedConversation || null);
const thread = ref(props.messages || []);

async function openConversation(conv) {
    selected.value = conv.id;

    const { data } = await axios.get(`/messagerie/${conv.id}/messages`);
    thread.value = data.messages;
}

async function sendMessage(content) {
    if (!selected.value) return;

    const { data } = await axios.post(`/messagerie/${selected.value}/messages`, {
        content,
    });

    thread.value.push(data.message);
}

async function createConversation(userId) {
    const response = await axios.post('/messagerie', {
        user_ids: [userId],
    });

    window.location.href = response.request.responseURL;
}
</script>
<template>

    <Head title="Messagerie" />
    <AppLayout>
        <div class="flex h-full bg-gray-50">

            <!-- Sidebar -->
            <div class="w-80 border-r bg-white flex flex-col shadow-sm">

                <!-- Header -->
                <div class="p-4 border-b bg-gray-100">
                    <h2 class="font-bold text-lg mb-3 text-gray-700">Nouvelle conversation</h2>

                    <div class="space-y-1">
                        <div v-for="user in props.users" :key="user.id" class="p-2 px-3 cursor-pointer rounded-md transition
                               hover:bg-gray-200 active:bg-gray-300" @click="createConversation(user.id)">
                            <span class="text-gray-700">{{ user.name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Conversations list -->
                <div class="flex-1 overflow-y-auto">
                    <ConversationListe :conversations="props.conversations" @select="openConversation" />
                </div>
            </div>

            <!-- Thread -->
            <div class="flex flex-col flex-1">

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto bg-white">
                    <MessageThread :messages="thread" />
                </div>

                <!-- Input -->
                <div class="border-t bg-gray-100 p-3">
                    <MessageInput @send="sendMessage" />
                </div>
            </div>
        </div>
    </Applayout>
</template>