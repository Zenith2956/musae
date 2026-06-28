<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import Description from './Description.vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <!-- <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head> -->
    <meta
        name="description"
        content="Musae est la plateforme numérique complète pour les musiciens. Centralisez vos ressources, suivez votre progression et restez concentré sur votre art."
    />
    <meta name="keywords" content="musique, application, progression musicale, partitions" />
    <meta property="og:title" content="Musae - Le tempo de votre progression" />
    <meta
        property="og:description"
        content="La plateforme numérique pour musiciens"
    />
    <nav>
        <header>
            <div>
                <a href="/"><img src="../../assets/logo.png" alt="Musae Logo"/></a>
            </div>
            <div>

                <Link
                class="btn-primary"
                v-if="$page.props.auth.user"
                :href="dashboard()"
            >
                Dashboard
            </Link>
            <template v-else>
                <Link
                    class="btn-log"
                    :href="login()"
                    >
                    Se connecter
                </Link>
                <Link
                class="btn-log" 
                v-if="canRegister"
                :href="register()"
                >
                S'inscrire
            </Link>
            </template>
            </div>
        </header>
    </nav>
    <main class="flex-1">
        <Description />
    </main>
</template>