<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import ConversationListe from '@/components/messaging/ConversationListe.vue';
import MessageThread from '@/components/messaging/MessageThread.vue';
import MessageInput from '@/components/messaging/MessageInput.vue';

const props = defineProps({
    conversations: Array,   // toujours présent
    users: Array,           // toujours présent
    selectedConversation: { type: Number, default: null },
    messages: { type: Array, default: () => [] },
});

const page = usePage();
const currentUserId = page.props.auth.user.id;
const userSearch = ref('');
const showSearch = ref(false);
const thread = ref([...props.messages]);

// Quand Inertia recharge la page avec de nouveaux messages (après show()),
// on sync le thread local
import { watch } from 'vue';
watch(() => props.messages, (val) => { thread.value = [...val]; });

const filteredUsers = computed(() =>
    userSearch.value.trim()
        ? (props.users ?? []).filter(u =>
            u.name.toLowerCase().includes(userSearch.value.toLowerCase()))
        : []
);

// Ouvrir une conversation = naviguer vers /messagerie/{id}
function openConversation(conv) {
    router.visit(`/messagerie/${conv.id}`, {
        preserveScroll: false,
    });
}

// Créer une conversation = POST /messagerie (le contrôleur redirige vers show)
function createConversation(userId) {
    userSearch.value = '';
    showSearch.value = false;
    router.post('/messagerie', { user_ids: [userId] }, {
        onSuccess: () => {
            // Inertia a rechargé la page après le redirect du contrôleur
            // rien à faire, la page se met à jour automatiquement
        },
        onError: (errors) => {
            console.error('Erreur création conversation:', errors);
        },
    });
}

// Envoyer un message = POST /messagerie/{id}/messages (retourne JSON)
async function sendMessage(content) {
    if (!props.selectedConversation) return;
    try {
        const { data } = await axios.post(
            `/messagerie/${props.selectedConversation}/messages`,
            { content }
        );
        thread.value.push(data.message);
    } catch (e) {
        console.error('Erreur envoi:', e.response?.data ?? e);
    }
}

// Nom à afficher pour la conversation ouverte
const activeContact = computed(() => {
    if (!props.selectedConversation) return null;
    const conv = props.conversations?.find(c => c.id === props.selectedConversation);
    const other = conv?.participants?.find(p => p.id !== currentUserId);
    return other ?? null;
});

function initials(name) {
    return (name ?? '?').split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}
</script>

<template>

    <Head title="Messagerie" />
    <AppLayout>
        <div style="display:flex; height:100%; background:#f3f4f6; overflow:hidden;">

            <!-- ── Panneau gauche ─────────────────────────────── -->
            <aside style="
                width: 300px; flex-shrink: 0;
                display: flex; flex-direction: column;
                background: #fff;
                border-right: 1.5px solid #e5e7eb;
            ">
                <!-- Titre + bouton nouvelle conv -->
                <div style="padding: 1.25rem; border-bottom: 1.5px solid #e5e7eb;">
                    <h1 style="
                        font-family: 'Playfair Display', serif;
                        font-size: 1.35rem; font-weight: 900;
                        color: #111827; margin: 0 0 0.85rem;
                    ">Messagerie</h1>

                    <button @click="showSearch = !showSearch" style="
                            width: 100%; display: flex; align-items: center;
                            justify-content: center; gap: 0.4rem;
                            padding: 0.6rem 1rem; background: #facc15;
                            border: none; border-radius: 0.75rem;
                            font-family: 'DM Sans', sans-serif;
                            font-size: 0.875rem; font-weight: 800;
                            color: #000; cursor: pointer; transition: background 0.15s;
                        " @mouseenter="e => e.currentTarget.style.background = '#eab308'"
                        @mouseleave="e => e.currentTarget.style.background = '#facc15'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Nouvelle conversation
                    </button>

                    <!-- Recherche dépliable -->
                    <div v-if="showSearch" style="margin-top: 0.75rem;">
                        <div style="position: relative;">
                            <svg style="position:absolute;left:0.65rem;top:50%;transform:translateY(-50%);opacity:0.35;"
                                width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5"
                                stroke-linecap="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            <input v-model="userSearch" autofocus placeholder="Rechercher un utilisateur…" style="
                                    width: 100%; box-sizing: border-box;
                                    padding: 0.55rem 0.75rem 0.55rem 2rem;
                                    border: 1.5px solid #d1d5db; border-radius: 0.65rem;
                                    font-size: 0.875rem; font-family: 'DM Sans', sans-serif;
                                    outline: none; background: #f9fafb; color: #111;
                                " @focus="e => e.target.style.borderColor = '#0d9488'"
                                @blur="e => e.target.style.borderColor = '#d1d5db'" />
                        </div>

                        <!-- Résultats -->
                        <div v-if="filteredUsers.length" style="
                            margin-top: 0.4rem; border: 1.5px solid #e5e7eb;
                            border-radius: 0.65rem; overflow: hidden; background: #fff;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                        ">
                            <div v-for="user in filteredUsers" :key="user.id" @click="createConversation(user.id)"
                                style="
                                    display: flex; align-items: center; gap: 0.6rem;
                                    padding: 0.6rem 0.85rem; cursor: pointer;
                                    font-size: 0.875rem; font-family: 'DM Sans', sans-serif;
                                    transition: background 0.1s;
                                " @mouseenter="e => e.currentTarget.style.background = '#f0fdfa'"
                                @mouseleave="e => e.currentTarget.style.background = '#fff'">
                                <div style="
                                    width: 28px; height: 28px; border-radius: 50%;
                                    background: #ccfbf1; color: #0f766e;
                                    display: flex; align-items: center; justify-content: center;
                                    font-size: 0.68rem; font-weight: 700; flex-shrink: 0;
                                ">{{ initials(user.name) }}</div>
                                <span style="color: #111827; font-weight: 500;">{{ user.name }}</span>
                            </div>
                        </div>

                        <p v-else-if="userSearch.trim()" style="
                            margin-top: 0.5rem; font-size: 0.8rem; color: #9ca3af;
                            text-align: center; font-family: 'DM Sans', sans-serif;
                        ">Aucun utilisateur trouvé</p>
                    </div>
                </div>

                <!-- Liste conversations -->
                <div style="flex:1; overflow-y:auto;">
                    <ConversationListe :conversations="props.conversations" :current-user-id="currentUserId"
                        @select="openConversation" />
                </div>
            </aside>

            <!-- ── Zone thread ────────────────────────────────── -->
            <main style="flex:1; display:flex; flex-direction:column; min-width:0;">

                <!-- Header thread -->
                <div style="
                    padding: 0.9rem 1.5rem;
                    background: #fff; border-bottom: 1.5px solid #e5e7eb;
                    display: flex; align-items: center; gap: 0.75rem; min-height: 62px;
                ">
                    <template v-if="activeContact">
                        <div style="
                            width: 36px; height: 36px; border-radius: 50%;
                            background: #ccfbf1; color: #0f766e;
                            display: flex; align-items: center; justify-content: center;
                            font-size: 0.72rem; font-weight: 700; flex-shrink: 0;
                        ">{{ initials(activeContact.name) }}</div>
                        <div>
                            <div style="
                                font-weight: 700; font-size: 0.95rem; color: #111827;
                                font-family: 'DM Sans', sans-serif;
                            ">{{ activeContact.name }}</div>
                        </div>
                    </template>
                    <span v-else style="font-size:0.9rem; color:#9ca3af; font-family:'DM Sans',sans-serif;">
                        Sélectionnez une conversation
                    </span>
                </div>

                <!-- Messages -->
                <div style="flex:1; overflow-y:auto; background:#f9fafb;">
                    <MessageThread :messages="thread" />
                </div>

                <!-- Input -->
                <div style="padding:0.85rem 1.25rem; background:#fff; border-top:1.5px solid #e5e7eb;">
                    <MessageInput :disabled="!selectedConversation" @send="sendMessage" />
                </div>
            </main>

        </div>
    </AppLayout>
</template>