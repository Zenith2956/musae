<script lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

export default {
  layout: AppLayout,
  setup() {
    const form = ref({
      name: '',
      link: '',
      instrument_id: '' as number | '',
      composer: '',
      bpm: 120,
      gamme: '',
      proficiency_level: 1,
      style: ''
    })

    const instrumentOptions = ref<{ id: number; name: string }[]>([])

    const gammes = ['Do majeur','Do mineur','Ré majeur','Ré mineur','Mi majeur','Mi mineur','Fa majeur','Fa mineur','Sol majeur','Sol mineur','La majeur','La mineur','Si majeur','Si mineur']
    const styles = ['acoustic','folk','rock','metal','jazz','pop','classical','other']

    const fetchInstruments = async () => {
      try {
        const res = await fetch('/calendar/instruments')
        const data = await res.json()
        instrumentOptions.value = data
      } catch (e) {
        console.error('❌ FETCH INSTRUMENTS ERROR', e)
      }
    }

    onMounted(fetchInstruments)

    function submit() {
      router.post('/sheet/store', form.value)
    }

    function setProficiency(n: number) { form.value.proficiency_level = n }

    return { form, submit, instrumentOptions, gammes, styles, setProficiency }
  }
}
</script>

<template>
  <AppLayout>
    <section class="container">
      <div class="sheet-edit">
      <h1>Ajouter une partition</h1>

      <label>Nom</label>
      <input v-model="form.name" type="text" />
      
      <label>Compositeur</label>
      <input v-model="form.composer" type="text" />

      <label>Lien</label>
      <input v-model="form.link" type="text" />

      <label>Instrument</label>
      <select v-model="form.instrument_id">
        <option value="">Aucun instrument</option>
        <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">{{ instr.name }}</option>
      </select>


      <label>BPM: {{ form.bpm }}</label>
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
          <span :style="{ color: n <= form.proficiency_level ? 'gold' : '#ccc', fontSize: '1.5rem' }">★</span>
        </button>
      </div>

      <label>Style</label>
      <select v-model="form.style">
        <option value="">-- Choisir --</option>
        <option v-for="s in styles" :key="s" :value="s">{{ s }}</option>
      </select>

      <div>
        <button class="btn-primary" @click="submit">Créer</button>
      </div>
    </div>
    </section>
  </AppLayout>
</template>
