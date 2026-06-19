
<script setup>
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3'

const props = defineProps({
    sheet: Object
})

const form = ref({
  name: props.sheet?.name ?? '',
  link: props.sheet?.link ?? '',
  instrument_id: props.sheet?.instrument_id ?? '',
  composer: props.sheet?.composer ?? '',
  bpm: props.sheet?.bpm ?? 120,
  gamme: props.sheet?.gamme ?? '',
  proficiency_level: props.sheet?.proficiency_level ?? 1,
  style: props.sheet?.style ?? ''
})

const gammes = [
  'Do majeur','Do mineur','Ré majeur','Ré mineur','Mi majeur','Mi mineur',
  'Fa majeur','Fa mineur','Sol majeur','Sol mineur','La majeur','La mineur','Si majeur','Si mineur'
]

const styles = ['acoustic','folk','rock','metal','jazz','pop','classical','other']

function save() {
  router.put(`/sheet/${props.sheet.id}`, form.value)
}

function setProficiency(n) { form.value.proficiency_level = n }
</script>

<template>
  <app-layout>
    <section class="container">
      <h1>{{ form.name }}</h1>

      <div class="sheet-edit">
        <div>
          <label>Nom</label>
          <input type="text" v-model="form.name" />
          
          <label>Compositeur</label>
          <input type="text" v-model="form.composer" />
        </div>

        <label>Image</label>
        <img class="sheet-image" :src="form.link" alt="" />

        <label>Lien</label>
        <input type="text" v-model="form.link" />

        <label>BPM: <span class="bpm-label">{{ form.bpm }}</span></label>
        <input type="number" min="50" max="200" step="5" v-model.number="form.bpm" />
        <!-- <input type="range" min="50" max="200" step="5" v-model.number="form.bpm" /> -->

        <label>Gamme</label>
        <select v-model="form.gamme">
          <option value="">-- Choisir --</option>
          <option v-for="g in gammes" :key="g" :value="g">{{ g }}</option>
        </select>

        <label>Niveau de maitrise</label>
        <div class="stars">
          <button v-for="n in 5" :key="n" type="button" @click="setProficiency(n)" :aria-label="`Set ${n}`">
            <span :style="{ color: n <= form.proficiency_level ? 'gold' : '#ccc', fontSize: '1.5em' }">★</span>
          </button>
        </div>

        <label>Style</label>
        <select v-model="form.style">
          <option value="">-- Choisir --</option>
          <option v-for="s in styles" :key="s" :value="s">{{ s }}</option>
        </select>

        <div>
          <button class="btn-primary" @click="save">Sauvegarder</button>
        </div>
      </div>
    </section>
  </app-layout>
</template>
