<script setup lang="ts">
import { ref, watch, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  sheetOptions: { type: Array as () => { id: number; name: string }[], default: () => [] },
  instrumentOptions: { type: Array as () => { id: number; name: string }[], default: () => [] },
})

const emit = defineEmits(['update:modelValue', 'submit'])

const name = ref<string>(props.modelValue.name ?? '')
const date = ref<string>(props.modelValue.date ?? '')
const time = ref<string>(props.modelValue.time ?? '08:00')
const duration = ref<number>(props.modelValue.duration ?? 60)
const link = ref<string>(props.modelValue.link ?? '')
const sheet_id = ref<number | null>(props.modelValue.sheet_id ?? null)
const instrument_id = ref<number | null>(props.modelValue.instrument_id ?? null)
const reminder = ref<boolean>(props.modelValue.reminder ?? false)
const showRepeat = ref<boolean>(false)
const days = ref<string[]>(props.modelValue.days ?? [])
const repeatEnd = ref<string>(props.modelValue.repeatEnd ?? '')

const weekDays = [
  { key: 'mo', label: 'Lu' }, { key: 'tu', label: 'Ma' },
  { key: 'we', label: 'Me' }, { key: 'th', label: 'Je' },
  { key: 'fr', label: 'Ve' }, { key: 'sa', label: 'Sa' },
  { key: 'su', label: 'Di' }, { key: 'all', label: 'Tous' },
]

function toggleDay(key: string) {
  if (key === 'all') {
    days.value = days.value.length === 7 ? [] : weekDays.filter(d => d.key !== 'all').map(d => d.key)
    return
  }
  days.value = days.value.includes(key)
    ? days.value.filter(d => d !== key)
    : [...days.value, key]
}

const endTimeLabel = computed(() => {
  if (!date.value || !time.value) return null
  const start = new Date(`${date.value}T${time.value}:00`)
  const end = new Date(start.getTime() + duration.value * 60 * 1000)
  return end.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
})

watch([name, date, time, duration, link, sheet_id, instrument_id, reminder, days, repeatEnd, showRepeat], () => {
  emit('update:modelValue', {
    name: name.value,
    date: date.value,
    time: time.value,
    duration: duration.value,
    link: link.value,
    sheet_id: sheet_id.value,
    instrument_id: instrument_id.value,
    reminder: reminder.value,
    days: showRepeat.value ? days.value : [],
    repeatEnd: showRepeat.value ? repeatEnd.value : '',
  })
}, { deep: true })
</script>

<template>
  <div class="form-wrap">

    <!-- Header -->
    <div class="form-header">
      <h1 class="form-title">Entrainement</h1>
    </div>

    <!-- Body -->
    <div class="form-body">

      <!-- Nom -->
      <div class="row-inline">
        <input v-model="name" type="text" placeholder="Nom" class="input flex-1" />
      </div>
      
      <!-- Horaire + Durée + Date -->
      <div class="row-inline">
        <div class="date-group">
          <span class="label-inline">Date :</span>
          <input v-model="date" type="date" class="input input-sm" />
        </div>
        <div class="time-group">
          <span class="label-inline">Heure :</span>
          <input v-model="time" type="time" class="input input-sm" />
        </div>
        <div class="duration-group">
          <span class="label-inline">Durée :</span>
          <input v-model.number="duration" type="number" min="5" max="480" step="5" class="input input-sm input-dur" />
          <span class="label-inline">min</span>
          <span v-if="endTimeLabel" class="end-hint">→ {{ endTimeLabel }}</span>
        </div>
      </div>

      <!-- Instrument -->
      <select v-model="instrument_id" class="input flex-1">
        <option :value="null">Instrument</option>
        <option v-for="i in instrumentOptions" :key="i.id" :value="i.id">
          {{ i.name }}
        </option>
      </select>

      <!-- Partition -->
      <select v-model="sheet_id" class="input flex-1">
        <option :value="null">Partition</option>
        <option v-for="s in sheetOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>

      <!-- Lien -->
      <div class="row-inline">
        <span class="label-inline">Lien :</span>
        <input v-model="link" type="url" class="input flex-1" placeholder="https://..." />
      </div>

      <!-- Répétition (toggle) -->
      <div class="repeat-header" @click="showRepeat = !showRepeat">
        <span class="section-title">Répétition</span>
        <button type="button" class="toggle-btn" :class="{ active: showRepeat }">
          <svg v-if="!showRepeat" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <rect x="3" y="3" width="14" height="14" rx="3" fill="none" stroke="currentColor" stroke-width="1.5" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
            <rect x="3" y="3" width="14" height="14" rx="3" stroke="#0d9488" stroke-width="1.5" />
            <path d="M6 10.5l2.5 2.5 5-5" stroke="#0d9488" stroke-width="1.8" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
      </div>

      <!-- Répétition expandable -->
      <div v-if="showRepeat" class="repeat-body">

        <!-- Jours -->
        <div class="days-row">
          <button v-for="d in weekDays" :key="d.key" type="button" @click="toggleDay(d.key)" class="day-btn"
            :class="{ selected: d.key === 'all' ? days.length === 7 : days.includes(d.key) }">
            {{ d.label }}
          </button>
        </div>

        <!-- Date de fin répétition -->
        <div class="row-inline">
          <span class="label-inline">Jusqu'au :</span>
          <input v-model="repeatEnd" type="date" class="input input-sm flex-1" />
        </div>

        <!-- Rappels -->
        <div class="reminder-row">
          <span class="label-inline">Rappels</span>
          <button type="button" @click="reminder = !reminder" class="toggle-btn" :class="{ active: reminder }">
            <svg v-if="!reminder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
              <rect x="3" y="3" width="14" height="14" rx="3" stroke="#9ca3af" stroke-width="1.5" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
              <rect x="3" y="3" width="14" height="14" rx="3" stroke="#0d9488" stroke-width="1.5" />
              <path d="M6 10.5l2.5 2.5 5-5" stroke="#0d9488" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Bouton AJOUTER -->
      <button type="button" class="btn-submit" @click="emit('submit')">
        AJOUTER
      </button>

    </div>
  </div>
</template>