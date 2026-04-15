<script setup lang="ts">
import { ref } from 'vue'
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import { Head, usePage } from '@inertiajs/vue3'
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
  user_id?: number
}

const page = usePage<{ currentUserId: number }>()
const currentUserId = page.props.currentUserId

// -------- State --------
const events = ref<Event[]>([])
const currentView = ref<any>(null)
const showDialog = ref(false)
const isEditing = ref(false)
const selectedEvent = ref<Event | null>(null)
const editingEvent = ref<Event | null>(null)

// -------- Helpers --------
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


// -------- Fetch Events --------
const fetchEvents = async (start: Date, end: Date) => {
  try {
    const res = await fetch(`/calendar/events?start=${start.toISOString()}&end=${end.toISOString()}&user_id=${currentUserId}`)
    const data = await res.json()
    events.value = data.map((e: any) => ({
      id: String(e.id),
      title: e.title,
      instrument: e.instrument || '',
      link: e.link || '',
      start: new Date(e.start),
      end: new Date(e.end),
    }))
  } catch (e) {
    console.error('❌ FETCH ERROR', e)
  }
}

const instrumentOptions = ref<{ id: number, name: string }[]>([])
const fetchInstruments = async () => {
  try {
    const res = await fetch('/calendar/instruments')
    const data = await res.json()
    instrumentOptions.value = data
  } catch (e) {
    console.error('❌ FETCH INSTRUMENTS ERROR', e)
  }
}

fetchInstruments()

// -------- VueCal Events --------
const onReady = ({ view }: any) => { currentView.value = view; fetchEvents(view.start, view.end) }
const onViewChange = (view: any) => { currentView.value = view; fetchEvents(view.start, view.end) }

const onEventCreate = ({ event }: any) => {
  editingEvent.value = {
    title: '',
    instrument: '',
    link: '',
    start: new Date(event?.start ?? Date.now()),
    end: new Date(event?.end ?? Date.now())
  }

  isEditing.value = true
  showDialog.value = true
}

const onEventChange = async ({ event }: any) => {
  if (!event.id) return
  try {
    const payload = {
      title: event.title,
      instrument: event.instrument,
      link: event.link,
      start: event.start.toISOString(),
      end: event.end.toISOString(),
      user_id: currentUserId
    }

    const res = await fetch(`/calendar/events/${event.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
      },
      body: JSON.stringify(payload)
    })

    const data = await res.json()
    if (data.success) {
      const idx = events.value.findIndex(e => e.id === event.id)
      if (idx !== -1) {
        events.value[idx] = {
          ...event,
          start: new Date(event.start),
          end: new Date(event.end)
        }
      }
    }
  } catch (err) {
    console.error('❌ DRAG/RESIZE ERROR', err)
  }
}

// -------- Save Event --------
const saveEvent = async () => {
  if (!editingEvent.value) return
  console.log('editingEvent:', editingEvent.value)
  try {
    console.log('STEP 1')

    const safeDate = (d: any) => {
      if (!d) throw new Error('Missing date')

      const date = d instanceof Date ? d : new Date(d)

      if (isNaN(date.getTime())) {
        console.error('INVALID DATE VALUE:', d)
        throw new Error('Invalid date')
      }

      return date
    }

    console.log('STEP 2')

    const normalizeDate = (d: any) => {
      const date = new Date(d)
      return isNaN(date.getTime()) ? new Date() : date
    }

    const payload = {
      title: editingEvent.value.title,
      instrument: editingEvent.value.instrument,
      link: editingEvent.value.link,
      start: normalizeDate(editingEvent.value.start).toISOString(),
      end: normalizeDate(editingEvent.value.end).toISOString(),
      user_id: currentUserId
    }

    console.log('STEP 3 payload', payload)

    const res = await fetch(`/calendar/events`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content') ?? ''
      },
      body: JSON.stringify(payload)
    })

    console.log('STEP 4 status', res.status)

    const text = await res.text()
    console.log('STEP 5 response', text)

    if (!res.ok) {
      throw new Error(text)
    }

    const data = JSON.parse(text)

    events.value.push({
      id: String(data.id),
      title: data.title,
      instrument: data.instrument || '',
      link: data.link || '',
      start: new Date(data.start),
      end: new Date(data.end)
    })

    showDialog.value = false
    editingEvent.value = null
    isEditing.value = false

  } catch (err) {
    console.error('SAVE FAILED:', err)
  }
}

// -------- Delete Event --------
const deleteEvent = async (id: string) => {
  try {
    await fetch(`/calendar/events/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
      }
    })
    events.value = events.value.filter(e => e.id !== id)
  } catch (err) {
    console.error('❌ DELETE ERROR', err)
  }
  showDialog.value = false
  editingEvent.value = null
  selectedEvent.value = null
  isEditing.value = false
}

// -------- Cancel Dialog --------
const cancelDialog = () => {
  showDialog.value = false
  editingEvent.value = null
  isEditing.value = false
}

</script>

<template>

  <Head title="Calendar" />
  <AppLayout>
    <div class="p-4">
      <vue-cal :events="events" :time-from="8 * 60" :time-to="21 * 60" :snap-to-interval="5" events-on-month-view
        :editable-events="true" @ready="onReady" @view-change="onViewChange" @event-create="onEventCreate"
        @event-click="openDialog" @event-change="onEventChange" />

      <w-dialog v-if="selectedEvent || editingEvent" v-model="showDialog"
        :title="isEditing ? 'Modifier' : selectedEvent?.title" width="400">
        <!-- Lecture -->
        <div v-if="!isEditing && selectedEvent">
          <p>{{ selectedEvent.instrument }}</p>
          <p>{{ selectedEvent.start.toLocaleString() }} - {{ selectedEvent.end.toLocaleString() }}</p>
          <p v-if="selectedEvent.link"><a :href="selectedEvent.link" target="_blank">{{ selectedEvent.link }}</a></p>
          <div class="flex justify-end gap-2 mt-4">
            <button @click="startEdit" class="px-3 py-1 bg-blue-500 text-white rounded">Modifier</button>
          </div>
        </div>

        <!-- Edition -->
        <div v-if="isEditing && editingEvent" class="flex flex-col gap-2">
          <input v-model="editingEvent.title" placeholder="Nom" class="border p-2 rounded" />
          <select v-model="editingEvent.instrument" class="border p-2 rounded">
            <option value="">Aucun instrument</option>
            <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">{{ instr.name }}</option>
          </select>

          <input v-model="editingEvent.link" placeholder="Lien" class="border p-2 rounded" />
          <div class="flex justify-end gap-2 mt-4">
            <button v-if="editingEvent.id" @click="deleteEvent(editingEvent.id)"
              class="px-3 py-1 bg-red-500 text-white rounded">
              Supprimer
            </button>
            <button @click="cancelDialog" class="px-3 py-1 border rounded">Annuler</button>
            <button @click="saveEvent" class="px-3 py-1 bg-green-500 text-white rounded">Enregistrer</button>
          </div>
        </div>
      </w-dialog>
    </div>
  </AppLayout>
</template>

<style scoped>
.vuecal {
  height: 650px;
}
</style>