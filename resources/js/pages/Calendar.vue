<script setup lang="ts">
import { ref } from 'vue'
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { addDatePrototypes } from 'vue-cal'
import AddEventForm from '@/components/AddEventForm.vue'
import EditEventForm from '@/components/EditEventForm.vue'

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

// ─── Dialog state ─────────────────────────────────────────────────────────────


type DialogMode = null | 'view' | 'edit' | 'create'

const dialogMode = ref<DialogMode>(null)
const showDialog = ref(false)
const selectedEvent = ref<CalendarEvent | null>(null)
const editingEvent = ref<CalendarEvent | null>(null)

const closeDialog = () => {
  showDialog.value = false
  dialogMode.value = null
  selectedEvent.value = null
  editingEvent.value = null
}

// ─── Clic sur un événement → mode lecture ────────────────────────────────────

const openDialog = (payload: any) => {
  const event = payload?.event ?? payload
  selectedEvent.value = { ...event }
  editingEvent.value = null
  dialogMode.value = 'view'
  showDialog.value = true
}

// ─── Depuis le mode lecture → passer en édition ───────────────────────────────

const startEdit = () => {
  if (!selectedEvent.value) return
  editingEvent.value = { ...selectedEvent.value }
  dialogMode.value = 'edit'
}

// ─── Drag-to-create → mode création ──────────────────────────────────────────

const onEventCreate = ({ event }: any) => {
  selectedEvent.value = null
  editingEvent.value = {
    title: '',
    instrument_id: null,
    sheet_id: null,
    link: '',
    start: new Date(event?.start ?? Date.now()),
    end: new Date(event?.end ?? Date.now()),
  }
  dialogMode.value = 'create'
  showDialog.value = true
}

// ─── Save (create or update) ──────────────────────────────────────────────────

const toISO = (d: any) =>
  (d instanceof Date ? d : new Date(d)).toISOString()

const saveEvent = async () => {
  if (!editingEvent.value) return

  const ev = editingEvent.value
  const isUpdate = !!ev.id
  const url = isUpdate ? `/calendar/events/${ev.id}` : `/calendar/events`

  const payload = {
    title: ev.title,
    instrument_id: ev.instrument_id ?? null,
    sheet_id: ev.sheet_id ?? null,
    link: ev.link,
    start: toISO(ev.start),
    end: toISO(ev.end),
  }

  const res = await fetch(url, {
    method: isUpdate ? 'PUT' : 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify(payload),
  })
  const data = await res.json()

  if (isUpdate) {
    const idx = events.value.findIndex(e => e.id === ev.id)
    if (idx !== -1) events.value[idx] = mapEvent(data)
  } else {
    events.value.push(mapEvent(data))
  }

  closeDialog()
  await fetchEvents(currentView.value.start, currentView.value.end)
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
  closeDialog()
  await fetchEvents(currentView.value.start, currentView.value.end)
}

// ─── Drag & drop / resize ─────────────────────────────────────────────────────

const onEventChange = async ({ event }: any) => {
  if (!event.id) return
  try {
    await fetch(`/calendar/events/${event.id}`, {
      method: 'PUT',
      headers: jsonHeaders(),
      body: JSON.stringify({
        title: event.title,
        instrument_id: event.instrument_id || null,
        sheet_id: event.sheet_id || null,
        link: event.link,
        start: toISO(event.start),
        end: toISO(event.end),
        user_id: currentUserId,
      }),
    })
  } catch (err) {
    console.error('❌ UPDATE EVENT FAILED', err)
  } finally {
    await fetchEvents(currentView.value.start, currentView.value.end)
  }
}

// ─── Add dialog (bouton toolbar) ─────────────────────────────────────────────

const showAddDialog = ref(false)
const newEventData = ref<Record<string, any>>({})

const DAY_MAP: Record<string, number> = {
  su: 0, mo: 1, tu: 2, we: 3, th: 4, fr: 5, sa: 6,
}

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

const submitNewEvent = async () => {
  showAddDialog.value = false
  const d = newEventData.value
  if (!d.name || !d.date || !d.time) return

  const durationMs = (d.duration ?? 60) * 60 * 1000
  const makeStart = (dateStr: string) => new Date(`${dateStr}T${d.time}:00`)
  const slots: { start: Date; end: Date }[] = []

  if (d.days?.length && d.repeatEnd) {
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
        instrument_id: d.instrument_id ?? null,
        link: d.link ?? '',
        start: slot.start.toISOString(),
        end: slot.end.toISOString(),
      }),
    })
    const data = await res.json()
    events.value.push(mapEvent(data))
  }

  newEventData.value = {}
  await fetchEvents(currentView.value.start, currentView.value.end)
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
            <path
              d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
          </svg>
          Ajouter un événement
        </button>
      </div>

      <!-- ── Calendar ──────────────────────────────────────── -->
      <vue-cal ref="vuecalRef" :events="events" :drag-to-create-event="true" :resizable-events="true"
        :drag-and-drop="true" :time-from="8 * 60" :time-to="21 * 60" :snap-to-interval="5" :editable-events="true"
        :views="['day', 'week', 'month']" events-on-month-view @event-drop="onEventChange" @event-resize="onEventChange"
        @ready="onReady" @view-change="onViewChange" @event-create="onEventCreate" @event-click="openDialog"
        @event-change="onEventChange" class="cal" />

      <!-- ── Dialog principale (lecture / édition / création) ── -->
      <w-dialog v-model="showDialog" title="" :width="440" class="add-dialog">
        <!-- Mode lecture -->
        <div v-if="dialogMode === 'view' && selectedEvent" class="form-wrap" style="box-shadow: none;">
          <div class="form-header">
            <h1 class="form-title">{{ selectedEvent.title }}</h1>
          </div>
          <div class="form-body">
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
            <div class="dialog-actions" style="padding-top: .75rem; border-top: 1px solid #f3f4f6; margin-top: .25rem;">
              <div class="btn-group">
                <button class="btn btn-ghost" @click="closeDialog">Fermer</button>
                <button class="btn-submit" style="width: auto; padding: .65rem 1.6rem; margin-top: 0;"
                  @click="startEdit">
                  MODIFIER
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Mode édition ou création -->
        <EditEventForm v-if="(dialogMode === 'edit' || dialogMode === 'create') && editingEvent"
          :model-value="editingEvent" @update:model-value="editingEvent = $event" :sheet-options="sheetOptions"
          :instrument-options="instrumentOptions" :is-new="dialogMode === 'create'" @save="saveEvent"
          @delete="deleteEvent" @cancel="closeDialog" />
      </w-dialog>

      <!-- ── Dialog : AddEventForm (bouton toolbar) ─────────── -->
      <w-dialog v-model="showAddDialog" title="" :width="380" class="add-dialog">
        <AddEventForm v-model="newEventData" :sheet-options="sheetOptions" :instrument-options="instrumentOptions"
          @submit="submitNewEvent" />
      </w-dialog>

    </div>
  </AppLayout>
</template>