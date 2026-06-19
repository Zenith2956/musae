<script lang="ts">
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import sheet from '@/customRoutes/sheet'



interface Sheet {
  id: number
  name: string
  link: string
  instrument_name?: string
  composer?: string
  user_id: number,
  instrument?: { id: number; name: string }
  bpm?: number
  gamme?: string
  proficiency_level?: number
  style?: string
}

export default {
  components: { Link },
  props: { sheets: {
    type: Array as () => Sheet[],
    required: true
    }},
  layout: AppLayout,

  setup(props: { sheets: Sheet[] }){
    const form = ref({
      name: '',
      link: '',
      instrument_id: '' as number | '',
      composer: '',
      })

    const instrumentOptions = ref<{ id: number; name: string }[]>([])

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
      router.post('/post', form.value)
    }

    const filters = ref({
      q: '',
      instrument_id: '' as number | '',
      min_bpm: 50,
      max_bpm: 200,
      gamme: '',
      min_level: 0,
      style: ''
    })

    const gammes = ['Do majeur','Do mineur','Ré majeur','Ré mineur','Mi majeur','Mi mineur','Fa majeur','Fa mineur','Sol majeur','Sol mineur','La majeur','La mineur','Si majeur','Si mineur']
    const styles = ['acoustic','folk','rock','metal','jazz','pop','classical','other']

    const filteredSheets = computed(() => {
      return props.sheets.filter(s => {
        if (filters.value.q && !s.name.toLowerCase().includes(filters.value.q.toLowerCase())) return false
        if (filters.value.instrument_id && s.instrument?.id !== filters.value.instrument_id) return false
        if (s.bpm != null) {
          if (s.bpm < filters.value.min_bpm || s.bpm > filters.value.max_bpm) return false
        }
        if (filters.value.gamme && s.gamme !== filters.value.gamme) return false
        if (filters.value.min_level > 0 && (s.proficiency_level ?? 0) < filters.value.min_level) return false
        if (filters.value.style && s.style !== filters.value.style) return false
        return true
      })
    })

    return {
      form,
      submit,
      instrumentOptions,
      sheets: props.sheets,
      filters,
      filteredSheets,
      gammes,
      styles
    }
  }
}

</script>

<template>
  <AppLayout>
    <section class="container">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <h1>Bibliothèque</h1>
        <Link href="/sheet/add" class="btn-primary">+</Link>
      </div>
      
      <div class="filters">
        <input placeholder="Rechercher..." v-model="filters.q" />
        <select v-model="filters.instrument_id">
          <option value="">Tous les instruments</option>
          <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">{{ instr.name }}</option>
        </select>
        <label>BPM</label>
        <input type="number" min="50" max="200" step="5" v-model.number="filters.min_bpm" style="width:6rem" />
        <input type="number" min="50" max="200" step="5" v-model.number="filters.max_bpm" style="width:6rem" />
        <select v-model="filters.gamme">
          <option value="">Toutes les gammes</option>
          <option v-for="g in gammes" :key="g" :value="g">{{ g }}</option>
        </select>
        <select v-model.number="filters.min_level">
          <option :value="0">Niveau quelconque</option>
          <option v-for="n in 5" :key="n" :value="n">Niveau minimum: {{ n }}</option>
        </select>
        <select v-model="filters.style">
          <option value="">Tous les styles</option>
          <option v-for="s in styles" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>
      
      <div class="sheets-list">
        <div v-for="sheet in filteredSheets" :key="sheet.id" class="sheet-card">
          <h2>{{ sheet.name ?? 'Inconnu' }}</h2>
          <a :href="`/sheet/${sheet.id}`">
            <h3>{{ sheet.composer ?? 'Inconnu' }}</h3>
            <img :class="sheet.link ? 'sheet-image' : 'no-display'" :src="sheet.link" alt="" />
          </a>
          <!-- <div class="sheet-details">
            <p>Instrument: <span>{{ sheet.instrument?.name || 'Aucun' }}</span></p>
            <p>BPM: <span>{{ sheet.bpm ?? '—' }}</span></p>
            <p>Gamme: <span>{{ sheet.gamme ?? '—' }}</span></p>
            <p>Niveau: <span>{{ sheet.proficiency_level ?? '—' }}</span></p>
            <p>Style: <span>{{ sheet.style ?? '—' }}</span></p>
          </div> -->
          <!-- <a :href="`/sheet/${sheet.id}`" target="_blank" class="btn-sheet">Voir la partition</a> -->
        </div>
      </div>
    </section>
  </AppLayout>
</template>