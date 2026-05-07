<script setup lang="ts">
import { ref } from 'vue'
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { addDatePrototypes } from 'vue-cal'
import AddEventForm from '@/components/AddEventForm.vue'

addDatePrototypes()

// ─── Types ────────────────────────────────────────────────────────────────────

interface CalendarEvent {
  id?: string
  title: string
  instrument_id?: number | null
  instrument?: string | null
  sheet_id?: number | null
  sheet?: string | null
  link?: string
  start: Date
  end: Date
}

// ─── Auth / Page ──────────────────────────────────────────────────────────────

const page = usePage<{ currentUserId: number }>()
const currentUserId = page.props.currentUserId

// ─── CSRF helper ──────────────────────────────────────────────────────────────

const csrf = () =>
  document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''

const jsonHeaders = () => ({
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-CSRF-TOKEN': csrf(),
})

// ─── Remote data ──────────────────────────────────────────────────────────────

const sheetOptions = ref<{ id: number; name: string }[]>([])
const instrumentOptions = ref<{ id: number; name: string }[]>([])

const fetchSheets = async () => {
  const res = await fetch('/calendar/sheets')
  sheetOptions.value = await res.json()
}

const fetchInstruments = async () => {
  try {
    const res = await fetch('/calendar/instruments')
    instrumentOptions.value = await res.json()
  } catch (e) {
    console.error('❌ FETCH INSTRUMENTS ERROR', e)
  }
}

fetchSheets()
fetchInstruments()

// ─── Events ───────────────────────────────────────────────────────────────────

const events = ref<CalendarEvent[]>([])
const currentView = ref<any>(null)
const vuecalRef = ref<any>(null)

const mapEvent = (e: any): CalendarEvent => ({
  id: String(e.id),
  title: e.title,
  instrument_id: e.instrument_id,
  instrument: e.instrument,
  sheet_id: e.sheet_id,
  sheet: e.sheet,
  link: e.link,
  start: new Date(e.start),
  end: new Date(e.end),
})

const fetchEvents = async (start: Date, end: Date) => {
  const res = await fetch(
    `/calendar/events?start=${start.toISOString()}&end=${end.toISOString()}`
  )
  events.value = (await res.json()).map(mapEvent)
}

const onReady = ({ view }: any) => {
  currentView.value = view
  fetchEvents(view.start, view.end)
}

const onViewChange = (view: any) => {
  currentView.value = view
  fetchEvents(view.start, view.end)
}

// ─── View dialog (read + edit existing event) ─────────────────────────────────

const showDialog = ref(false)
const isEditing = ref(false)
const selectedEvent = ref<CalendarEvent | null>(null)
const editingEvent = ref<CalendarEvent | null>(null)

const openDialog = (payload: any) => {
  const event = payload?.event ?? payload
  selectedEvent.value = { ...event }
  editingEvent.value = null
  isEditing.value = false
  showDialog.value = true
}

const startEdit = () => {
  if (!selectedEvent.value) return
  editingEvent.value = { ...selectedEvent.value }
  isEditing.value = true
}

const cancelDialog = () => {
  showDialog.value = false
  editingEvent.value = null
  isEditing.value = false
}

// ─── Save existing event ──────────────────────────────────────────────────────

const saveEvent = async () => {
  if (!editingEvent.value) return

  const isUpdate = !!editingEvent.value.id
  const url = isUpdate
    ? `/calendar/events/${editingEvent.value.id}`
    : `/calendar/events`

  const payload = {
    title: editingEvent.value.title,
    instrument_id: editingEvent.value.instrument_id ?? null,
    sheet_id: editingEvent.value.sheet_id ?? null,
    link: editingEvent.value.link,
    start: editingEvent.value.start.toISOString(),
    end: editingEvent.value.end.toISOString(),
  }

  const res = await fetch(url, {
    method: isUpdate ? 'PUT' : 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify(payload),
  })

  const data = await res.json()

  if (isUpdate) {
    const idx = events.value.findIndex(e => e.id === editingEvent.value!.id)
    if (idx !== -1) events.value[idx] = mapEvent(data)
  } else {
    events.value.push(mapEvent(data))
  }

  showDialog.value = false
  editingEvent.value = null
  isEditing.value = false
}

// ─── Update event (drag & drop / resize) ──────────────────────────────────────

const updateEvent = async (event: any) => {
  if (!event.id) return

  const res = await fetch(`/calendar/events/${event.id}`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({
      title: event.title,
      instrument_id: event.instrument_id || null,
      sheet_id: event.sheet_id || null,
      link: event.link,
      start: event.start.toISOString(),
      end: event.end.toISOString(),
      user_id: currentUserId,
    }),
  })

  const data = await res.json()

  if (!res.ok || data.errors) {
    throw new Error(data.message || 'Validation error')
  }

  await fetchEvents(currentView.value.start, currentView.value.end)
  return data

}

const onEventChange = async ({ event }: any) => {
  try {
    await updateEvent(event)
  } catch (err) {
    console.error('❌ UPDATE EVENT FAILED', err)
  } finally {
    await fetchEvents(currentView.value.start, currentView.value.end)
  }
}

// ─── Delete event ─────────────────────────────────────────────────────────────

const deleteEvent = async (id: string) => {
  try {
    await fetch(`/calendar/events/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf() },
    })
    events.value = events.value.filter(e => e.id !== id)
  } catch (err) {
    console.error('❌ DELETE ERROR', err)
  }
  showDialog.value = false
  editingEvent.value = null
  selectedEvent.value = null
  isEditing.value = false
  await fetchEvents(currentView.value.start, currentView.value.end)

}

// ─── Drag-to-create (inline VueCal handler) ───────────────────────────────────

const onEventCreate = ({ event }: any) => {
  editingEvent.value = {
    title: '',
    instrument_id: null,
    sheet_id: null,
    link: '',
    start: new Date(event?.start ?? Date.now()),
    end: new Date(event?.end ?? Date.now()),
  }
  isEditing.value = true
  showDialog.value = true 
}

// ─── Add dialog (AddEventForm button) ────────────────────────────────────────

const showAddDialog = ref(false)
const newEventData = ref<Record<string, any>>({})

// ─── Remplacer submitNewEvent dans Calendar.vue ───────────────────────────────
const DAY_MAP: Record<string, number> = {
  su: 0, mo: 1, tu: 2, we: 3, th: 4, fr: 5, sa: 6,
}

const submitNewEvent = async () => {
  showAddDialog.value = false
  const d = newEventData.value
  if (!d.name || !d.date || !d.time) return

  const durationMs = (d.duration ?? 60) * 60 * 1000

  // Build the base start datetime
  const makeStart = (dateStr: string) =>
    new Date(`${dateStr}T${d.time}:00`)

  const slots: { start: Date; end: Date }[] = []

  if (d.days?.length && d.repeatEnd) {
    // Répétition : générer un event par occurrence
    const selectedDayNums = (d.days as string[]).map((k: string) => DAY_MAP[k])
    const rangeEnd = new Date(d.repeatEnd + 'T23:59:59')
    const cursor = new Date(makeStart(d.date))

    while (cursor <= rangeEnd) {
      if (selectedDayNums.includes(cursor.getDay())) {
        const s = new Date(cursor)
        slots.push({ start: s, end: new Date(s.getTime() + durationMs) })
      }
      cursor.setDate(cursor.getDate() + 1)
    }
  } else {
    // Event unique
    const start = makeStart(d.date)
    slots.push({ start, end: new Date(start.getTime() + durationMs) })
  }

  for (const slot of slots) {
    const res = await fetch('/calendar/events', {
      method: 'POST',
      headers: jsonHeaders(),
      body: JSON.stringify({
        title: d.name,
        sheet_id: d.sheet_id ?? null,
        instrument_id: null,
        link: '',
        start: slot.start.toISOString(),
        end: slot.end.toISOString(),
      }),
    })

    const data = await res.json()
    events.value.push(mapEvent(data))
  }

  showAddDialog.value = false
  newEventData.value = {}
  await fetchEvents(currentView.value.start, currentView.value.end)

}

// ─── Mettre à jour openAddDialog ──────────────────────────────────────────────

const openAddDialog = () => {
  newEventData.value = {
    name: '',
    date: new Date().toISOString().slice(0, 10),
    time: '08:00',
    duration: 60,
    sheet_id: null,
    reminder: false,
    days: [],
    repeatEnd: '',
  }
  showAddDialog.value = true
}
</script>
<template>
  <Head title="Calendrier" />
  <AppLayout>
    <div class="calendar-page">

      <!-- ── Toolbar ───────────────────────────────────────── -->
      <div class="toolbar">
        <button class="btn-add" @click="openAddDialog">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="icon">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
          </svg>
          Ajouter un événement
        </button>
      </div>

      <!-- ── Calendar ──────────────────────────────────────── -->
      <vue-cal
        ref="vuecalRef"
        :events="events"
        :drag-to-create-event="true"
        :resizable-events="true"
        :drag-and-drop="true"
        :time-from="8 * 60"
        :time-to="21 * 60"
        :snap-to-interval="5"
        :editable-events="true"
        :views="['day', 'week', 'month']"
        events-on-month-view
        @event-drop="onEventChange"
        @event-resize="onEventChange"
        @ready="onReady"
        @view-change="onViewChange"
        @event-create="onEventCreate"
        @event-click="openDialog"
        @event-change="onEventChange"
        class="cal"
      />

      <!-- ── Dialog : view / edit existing event ───────────── -->
      <w-dialog
        v-if="selectedEvent || editingEvent"
        v-model="showDialog"
        :title="isEditing ? 'Modifier l\'événement' : (selectedEvent?.title ?? '')"
        class="event-dialog"
      >
        <!-- Read mode -->
        <div v-if="!isEditing && selectedEvent" class="dialog-body">
          <div class="field-row">
            <span class="field-label">Instrument</span>
            <span class="field-value">{{ selectedEvent.instrument || '—' }}</span>
          </div>
          <div class="field-row">
            <span class="field-label">Horaire</span>
            <span class="field-value">
              {{ selectedEvent.start.toLocaleString('fr-FR') }}
              <span class="arrow">→</span>
              {{ selectedEvent.end.toLocaleString('fr-FR') }}
            </span>
          </div>
          <div class="field-row">
            <span class="field-label">Partition</span>
            <span class="field-value">{{ selectedEvent.sheet || '—' }}</span>
          </div>
          <div v-if="selectedEvent.link" class="field-row">
            <span class="field-label">Lien</span>
            <a :href="selectedEvent.link" target="_blank" class="link">{{ selectedEvent.link }}</a>
          </div>
          <div class="dialog-actions">
            <button class="btn btn-primary" @click="startEdit">Modifier</button>
          </div>
        </div>

        <!-- Edit mode -->
        <div v-if="isEditing && editingEvent" class="dialog-body">
          <div class="form-field">
            <label>Titre</label>
            <input v-model="editingEvent.title" placeholder="Nom de l'événement" />
          </div>
          <div class="form-field">
            <label>Instrument</label>
            <select v-model="editingEvent.instrument_id">
              <option value="">Aucun instrument</option>
              <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">
                {{ instr.name }}
              </option>
            </select>
          </div>
          <div class="form-field">
            <label>Partition</label>
            <select v-model="editingEvent.sheet_id">
              <option value="">Aucune partition</option>
              <option v-for="sheet in sheetOptions" :key="sheet.id" :value="sheet.id">
                {{ sheet.name }}
              </option>
            </select>
          </div>
          <div class="form-field">
            <label>Lien</label>
            <input v-model="editingEvent.link" placeholder="https://..." />
          </div>
          <div class="dialog-actions between">
            <button
              v-if="editingEvent.id"
              class="btn btn-danger"
              @click="deleteEvent(editingEvent.id!)"
            >
              Supprimer
            </button>
            <div class="btn-group">
              <button class="btn btn-ghost" @click="cancelDialog">Annuler</button>
              <button class="btn btn-success" @click="saveEvent">Enregistrer</button>
            </div>
          </div>
        </div>
      </w-dialog>

      <!-- ── Dialog : AddEventForm ──────────────────────────── -->
      <w-dialog v-model="showAddDialog" title="" :width="380" class="add-dialog">
        <AddEventForm
          v-model="newEventData"
          :sheet-options="sheetOptions"
          :instrument-options="instrumentOptions"
          @submit="submitNewEvent"
        />
      </w-dialog>

    </div>
  </AppLayout>
</template>
