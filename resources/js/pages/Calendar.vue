<script setup lang="ts">
import { ref } from 'vue'
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { addDatePrototypes } from 'vue-cal'

addDatePrototypes()
interface Event {
  id?: string
  title: string
  instrument?: string
  link?: string
  start: Date
  end: Date
}

interface CalendarEvent {
  id?: string
  title: string
  instrument?: string
  link?: string
  start: Date
  end: Date
}

interface VueCalEventPayload {
  event: CalendarEvent
  e?: Event
  overlaps?: any[]
  cell?: any
  external?: boolean
}

const unwrapEvent = (payload: CalendarEvent | VueCalEventPayload): CalendarEvent => {
  return (payload as VueCalEventPayload).event ?? payload as CalendarEvent
}

const events = ref<Event[]>([])
const currentView = ref<any>(null)
const showDialog = ref(false)
const editingEvent = ref<Event | null>(null)
let resolveEvent: ((event: any) => void) | null = null

// =======================================
// FETCH EVENTS
// =======================================
const fetchEvents = async (start: Date, end: Date) => {
  console.log('📡 FETCH EVENTS', start, end)

  try {
    const res = await fetch(`/calendar/events?start=${start.toISOString()}&end=${end.toISOString()}`)
    const data = await res.json()

    console.log('📥 DATA FROM API', data)

    events.value = data.map((e: any) => ({
      id: String(e.id),
      title: e.title,
      instrument: e.instrument || '',
      link: e.link || '',
      start: new Date(e.start),
      end: new Date(e.end)
    }))

    console.log('📅 EVENTS LOADED', events.value)

  } catch (e) {
    console.error('❌ FETCH ERROR', e)
  }
}

// =======================================
// VUE CAL
// =======================================
const onReady = ({ view }: any) => {
  console.log('🟢 CAL READY', view)
  currentView.value = view
  fetchEvents(view.start, view.end)
}

const onViewChange = (view: any) => {
  console.log('🔄 VIEW CHANGE', view)
  currentView.value = view
  fetchEvents(view.start, view.end)
}

// =======================================
// CREATE EVENT (drag)
// =======================================
const onEventCreate = ({ event, resolve }: any) => {
  console.log('✨ CREATE EVENT TRIGGERED', event)

  editingEvent.value = {
    title: '',
    instrument: '',
    link: '',
    start: new Date(event.start),
    end: new Date(event.end)
  }

  resolveEvent = resolve
  showDialog.value = true
}

// =======================================
// CLICK EVENT (edit)
// =======================================
const onEventClick = (payload: CalendarEvent | VueCalEventPayload) => {
  const event = unwrapEvent(payload)
  console.log('🖱 CLICK EVENT', event)

  editingEvent.value = { ...event }
  console.warn(editingEvent.value)
  resolveEvent = null
  showDialog.value = true
}

// =======================================
// SAVE (CREATE OR UPDATE)
// =======================================
const saveEvent = async () => {
  if (!editingEvent.value) return

  console.log('💾 SAVE EVENT', editingEvent.value)

  const event = editingEvent.value
  const start = new Date(event.start)
  const end = new Date(event.end)

  try {
    if (event.id) {
      // ================= UPDATE =================
      console.log('✏️ UPDATE EVENT')

      await fetch(`/calendar/events/${event.id}`, {
        method: 'PUT',
        // credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          title: event.title,
          instrument: event.instrument,
          link: event.link,
          start: event.start.toISOString(),
          end: event.end.toISOString()
        })
      })

      const idx = events.value.findIndex(e => e.id === editingEvent.value!.id)
      if (idx !== -1) {
        events.value[idx] = { ...editingEvent.value }
      }

      console.log('✅ EVENT UPDATED')

    } else {
      // ================= CREATE =================
      console.log('🆕 CREATE EVENT')

      const res = await fetch('/calendar/events', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          title: event.title,
          instrument: event.instrument,
          link: event.link,
          start: event.start.toISOString(),
          end: event.end.toISOString()
        })
      })

      const data = await res.json()

      console.log('📥 CREATED EVENT FROM API', data)
      const createdEvent: Event = {
        id: String(data.id),
        title: data.title,
        instrument: data.instrument || '',
        link: data.link || '',
        start: new Date(data.start),
        end: new Date(data.end)
      }

      events.value.push(createdEvent)

      resolveEvent?.(createdEvent)

      console.log('✅ EVENT CREATED')
    }

  } catch (e) {
    console.error('❌ SAVE ERROR', e)
    resolveEvent?.(false)
  }

  showDialog.value = false
  editingEvent.value = null
}

// =======================================
// DRAG / RESIZE
// =======================================
const onEventChange = async (payload: CalendarEvent | VueCalEventPayload) => {
  const event = unwrapEvent(payload)
  console.log('🔁 EVENT CHANGED (drag/resize)', event)

  if (!event.start || !event.end) {
    console.warn('⚠️ INVALID EVENT DATES', event)
    return
  }

  const start = new Date(event.start)
  const end = new Date(event.end)

  if (isNaN(start.getTime()) || isNaN(end.getTime())) {
    console.warn('⚠️ INVALID DATE FORMAT', event)
    return
  }

  try {
    await fetch(`/calendar/events/${event.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        title: event.title,
        start: event.start.toISOString(),
        end: event.end.toISOString(),
      })
    })

    const idx = events.value.findIndex(e => e.id === event.id)
    if (idx !== -1) events.value[idx] = { ...event, start, end }

    console.log('✅ EVENT UPDATED (drag)')

  } catch (e) {
    console.error('❌ DRAG ERROR', e)
  }
}

// =======================================
const cancelDialog = () => {
  console.log('❌ CANCEL')
  resolveEvent?.(false)
  showDialog.value = false
  editingEvent.value = null
}
</script>

<template>

  <Head title="Calendar" />
  <AppLayout>
    <div class="p-4">

      <vue-cal
        :events="events"
        :time-from="8 * 60"
        :time-to="19 * 60"
        :snap-to-interval="5"
        editable-events
        @ready="onReady"
        @view-change="onViewChange"
        @event-create="onEventCreate"
        @event-click="onEventClick"
        @event-change="onEventChange"
        @event-drop="onEventChange"
        @event-resize-end="onEventChange"
        />

      <!-- DIALOG -->
      <div v-if="showDialog" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-4 rounded shadow w-96">

          <h2 class="mb-2 font-bold">
            {{ editingEvent?.id ? 'Modifier' : 'Créer' }} un événement
          </h2>

          <input v-model="editingEvent!.title" placeholder="Nom" class="border p-1 w-full mb-2" />

          <input v-model="editingEvent!.instrument" placeholder="Instrument" class="border p-1 w-full mb-2" />

          <input v-model="editingEvent!.link" placeholder="Lien" class="border p-1 w-full mb-2" />

          <div class="flex justify-end gap-2">
            <button @click="cancelDialog" class="px-3 py-1 border rounded">
              Annuler
            </button>
            <button @click="saveEvent" class="px-3 py-1 bg-blue-500 text-white rounded">
              Valider
            </button>
          </div>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<style scoped>
.vuecal {
  height: 650px;
}
</style>