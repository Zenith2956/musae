<script lang="ts">
import { ref, onMounted } from 'vue'
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

    return {
      form,
      submit,
      instrumentOptions,
      sheets: props.sheets
    }
  }
}

</script>

<template>
  <AppLayout>
    <section class="container">
      <h1>Partitions</h1>
      <div class="sheets-list">
        <div v-for="sheet in sheets" :key="sheet.id" class="sheet-card">
          <h2>{{ sheet.name }}</h2>
          <p>Instrument: {{ sheet.instrument?.name || 'Aucun' }}</p>
          <a :href="sheet.link" target="_blank">View Sheet</a>
        </div>
      </div>
    </section>

    <div class="form">
      <label for="sheetName">Name: </label>
      <input id="sheetName" type="text" v-model="form.name" />

      <label for="sheetLink">Link: </label>
      <input id="sheetLink" type="text" v-model="form.link" />

      <label for="sheetInstrument">Instrument: </label>
      <select id="sheetInstrument" v-model="form.instrument_id">
        <option value="">Aucun instrument</option>
        <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">
          {{ instr.name }}
        </option>
      </select>
      <label for="sheetComposer">Composer: </label>
      <input id="sheetComposer" type="text" v-model="form.composer" />

      <button @click="submit">Submit</button>
    </div>
  </AppLayout>
</template>