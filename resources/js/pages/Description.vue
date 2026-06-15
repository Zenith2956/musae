<script setup lang="ts">
const missions = [
    {
        title: 'Organisation',
        text: 'Le coffre-fort numérique qui centralise partitions, enregistrements, annotations et planning de pratique.',
    },
    {
        title: 'Accompagnement',
        text: 'Un partenaire de progression qui aide à structurer les sessions, suivre les progrès et maintenir la motivation.',
    },
    {
        title: 'Transmission',
        text: 'Un espace collaboratif pour les professeurs, élèves et parents, avec un suivi pédagogique plus fluide.',
    },
];

const values = [
    'Progression — documenter le parcours pas à pas pour donner au musicien le sentiment de maîtrise.',
    'Accessibilité — une expérience intuitive et claire, pour tous les niveaux.',
    'Passion — une solution conçue par des musiciens, pour des musiciens.',
];

const pricing = [
    {
        name: 'SOLO',
        price: 'Gratuit',
        description: 'Pour l’organisation personnelle de base.',
    },
    {
        name: 'VIRTUOSE',
        price: '4,99 € / mois',
        description: 'Outils avancés, statistiques de progression et mode focus.',
    },
    {
        name: 'ACADEMY',
        price: '20 € / élève / an',
        description: 'Solution B2B pour les écoles de musique et le suivi pédagogique.',
    },
];

const faqs = [
    {
        question: 'Pourquoi Musae ?',
        answer: 'Parce que la pratique musicale reste souvent fragmentée entre supports physiques et outils numériques non dédiés. Musae centralise tout dans un environnement structuré.',
    },
    {
        question: 'À qui s’adresse Musae ?',
        answer: 'Aux musiciens solos, aux élèves, aux enseignants et aux écoles qui veulent mieux organiser, suivre et transmettre leur pratique.',
    },
    {
        question: 'Quelles sont les offres proposées ?',
        answer: 'SOLO, VIRTUOSE et ACADEMY, avec une approche adaptée à l’usage personnel, à la progression avancée et au suivi institutionnel.',
    },
    {
        question: 'Musae remplace-t-il mes outils actuels ?',
        answer: 'Il les centralise. Vous gardez votre univers musical dans un seul espace, plus clair, plus accessible et plus orienté progression.',
    },
];
</script>

<template>
    <section class="description-page">
        <div class="description-container">
            <header class="description-header">
                <p class="eyebrow">Présentation</p>
                <h1>Musae — le tempo de votre progression</h1>
                <p class="lead">
                    Malgré la numérisation de l’éducation, la pratique musicale reste fragmentée. Un musicien doit jongler entre des supports physiques et des outils numériques non dédiés, ce qui génère une charge mentale nuisible à la progression.
                </p>
            </header>

            <article class="description-card">
                <h2>Constat</h2>
                <p>
                    La pratique musicale reste encore trop dispersée. Les partitions, les enregistrements, les annotations et les suivis pédagogiques sont souvent disséminés dans plusieurs espaces, ce qui complique la progression et la concentration.
                </p>
            </article>

            <article class="description-card">
                <h2>Problématique</h2>
                <p>
                    Comment transformer l’outil numérique, souvent perçu comme une source de distraction, en un environnement de travail structuré et immersif capable de soutenir efficacement le progrès du musicien ?
                </p>
            </article>

            <article class="description-card">
                <h2>Propositions</h2>
                <p>
                    Musae a été pensé comme un sanctuaire numérique dédié au musicien. Il centralise l’écosystème musical autour de trois offres ciblées : SOLO, VIRTUOSE et ACADEMY.
                </p>
            </article>

            <article class="description-card">
                <h2>Supports</h2>
                <p>
                    L’application Musae est l’outil central pour la gestion des partitions, les annotations et le mode focus, tandis que le site vitrine permet de découvrir la marque, de souscrire aux offres et de fédérer une communauté de pratiquants.
                </p>
            </article>

            <article class="description-card">
                <h2>Storytelling</h2>
                <p>
                    L’idée de Musae est née d’une observation commune au sein de notre équipe, composée de profils créatifs et techniques, mais surtout de musiciens. Nous avons voulu créer une solution à la fois élégante, utile et profondément alignée avec le quotidien du musicien.
                </p>
            </article>

            <article class="description-card">
                <h2>Nos missions</h2>
                <div class="grid-card">
                    <div v-for="item in missions" :key="item.title" class="mini-card">
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.text }}</p>
                    </div>
                </div>
            </article>

            <article class="description-card">
                <h2>Nos valeurs</h2>
                <ul class="value-list">
                    <li v-for="item in values" :key="item">{{ item }}</li>
                </ul>
            </article>

            <article class="description-card">
                <h2>Tarifs</h2>
                <div class="grid-card pricing-grid">
                    <div v-for="plan in pricing" :key="plan.name" class="mini-card pricing-card">
                        <h3>{{ plan.name }}</h3>
                        <p class="price">{{ plan.price }}</p>
                        <p>{{ plan.description }}</p>
                    </div>
                </div>
            </article>

            <article class="description-card">
                <h2>FAQ</h2>
                <div v-for="item in faqs" :key="item.question" class="faq-item">
                    <h3>{{ item.question }}</h3>
                    <p>{{ item.answer }}</p>
                </div>
            </article>
        </div>
    </section>
</template>

<style scoped>
.description-page {
    padding: 2rem 1rem 4rem;
    background: linear-gradient(180deg, #fff 0%, #f8fbfc 100%);
}
.description-container {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    gap: 1.25rem;
}
.description-header h1 {
    font-size: 2.4rem;
    color: #0091A7;
    margin-bottom: 0.75rem;
}
.eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.85rem;
    color: #FFC545;
    font-weight: 700;
}
.lead {
    font-size: 1.05rem;
    color: #404040;
}
.description-card {
    background: #fff;
    border: 1px solid #edf2f4;
    border-radius: 18px;
    padding: 1.25rem;
    box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
}
.description-card h2 {
    color: #50320F;
    margin-bottom: 0.5rem;
}
.grid-card {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}
.mini-card {
    border: 1px solid #edf2f4;
    border-radius: 14px;
    padding: 1rem;
    background: #fff;
}
.mini-card h3 {
    color: #0091A7;
    margin-bottom: 0.35rem;
}
.price {
    font-weight: 700;
    color: #50320F;
}
.value-list,
.faq-item {
    display: grid;
    gap: 0.75rem;
}
.value-list li,
.faq-item p {
    color: #404040;
}
.faq-item h3 {
    color: #0091A7;
    margin-bottom: 0.1rem;
}
</style>
