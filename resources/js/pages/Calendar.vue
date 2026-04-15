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
  instrument_id?: number | null
  instrument?: string | null
  link?: string
  start: Date
  end: Date
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
  const res = await fetch(`/calendar/events?start=${start.toISOString()}&end=${end.toISOString()}`)
  const data = await res.json()

  events.value = data.map((e: any) => ({
    id: String(e.id),
    title: e.title,
    instrument_id: e.instrument_id,
    instrument: e.instrument,
    link: e.link,
    start: new Date(e.start),
    end: new Date(e.end),
  }))
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
    instrument_id: null,
    link: '',
    start: new Date(event?.start ?? Date.now()),
    end: new Date(event?.end ?? Date.now())
  }

  isEditing.value = true
  showDialog.value = true
}

const onEventChange = async ({ event }: any) => {
  try {
    // 1. update backend
    await updateEvent(event)

    // 2. refresh calendrier (source de vérité)
    await fetchEvents(currentView.value.start, currentView.value.end)

  } catch (err) {
    console.error('❌ UPDATE EVENT FAILED', err)

    // rollback propre
    await fetchEvents(currentView.value.start, currentView.value.end)
  }
}



// -------- Save Event --------
const saveEvent = async () => {
  if (!editingEvent.value) return

  const isUpdate = !!editingEvent.value.id

  const url = isUpdate
    ? `/calendar/events/${editingEvent.value.id}`
    : `/calendar/events`

  const method = isUpdate ? 'PUT' : 'POST'

  const payload = {
    title: editingEvent.value.title,
    instrument_id: editingEvent.value.instrument_id ?? null,
    link: editingEvent.value.link,
    start: editingEvent.value.start.toISOString(),
    end: editingEvent.value.end.toISOString(),
  }

  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? ''
    },
    body: JSON.stringify(payload)
  })

  const data = await res.json()

  if (isUpdate) {
    await updateEvent({
      ...editingEvent.value,
      id: editingEvent.value.id
    })
  } else {
    events.value.push({
      id: String(data.id),
      title: data.title,
      instrument_id: data.instrument_id,
      instrument: data.instrument,
      link: data.link,
      start: new Date(data.start),
      end: new Date(data.end),
    })
  }

  showDialog.value = false
  editingEvent.value = null
  isEditing.value = false
}


const updateEvent = async (event: any) => {
  
  if (!event.id) return

  const payload = {
    title: event.title,
    instrument_id: event.instrument_id || null,
    link: event.link,
    start: event.start.toISOString(),
    end: event.end.toISOString(),
    user_id: currentUserId
  }

  const res = await fetch(`/calendar/events/${event.id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN':
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
    },
    body: JSON.stringify(payload)
  })

  const data = await res.json()

  if (!res.ok || data.errors) {
    throw new Error(data.message || 'Validation error')
  }

  return data
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
      <vue-cal
      :events="events"
      :drag-to-create-event="false"
      :resizable-events="true"
      :drag-and-drop="true"
      @event-drop="onEventChange"
      @event-resize="onEventChange"
      :time-from="8 * 60"
      :time-to="21 * 60"
      :snap-to-interval="5"
      events-on-month-view
      :editable-events="true"
      @ready="onReady"
      @view-change="onViewChange"
      @event-create="onEventCreate"
      @event-click="openDialog"
      @event-change="onEventChange"/>

      <w-dialog v-if="selectedEvent || editingEvent" v-model="showDialog"
        :title="isEditing ? 'Modifier l’événement' : selectedEvent?.title">
        <!-- MODE LECTURE -->
        <div v-if="!isEditing && selectedEvent" class="space-y-4 pb-6" style="height: 9em; max-height: 50%;">

          <div class="space-y-1">
            <p class="text-sm text-gray-500">Instrument</p>
            <p class="text-base font-medium">
              {{ selectedEvent.instrument || '—' }}
            </p>
          </div>

          <div class="space-y-1">
            <p class="text-sm text-gray-500">Horaire</p>
            <p class="text-base">
              {{ selectedEvent.start.toLocaleString() }}
              <span class="text-gray-400">→</span>
              {{ selectedEvent.end.toLocaleString() }}
            </p>
          </div>

          <div v-if="selectedEvent.link" class="space-y-1">
            <p class="text-sm text-gray-500">Lien</p>
            <a :href="selectedEvent.link" target="_blank" class="text-blue-600 hover:underline break-all">
              {{ selectedEvent.link }}
            </a>
          </div>

          <div class="flex justify-end mt-10">
            <button @click="startEdit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
              Modifier
            </button>
          </div>
        </div>

        <!-- MODE ÉDITION -->
        <div v-if="isEditing && editingEvent" class="space-y-4">

          <div class="space-y-1">
            <label class="text-sm text-gray-500">Titre</label>
            <input v-model="editingEvent.title" placeholder="Nom de l’événement"
              class="w-full border rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>

          <div class="space-y-1">
            <label class="text-sm text-gray-500">Instrument</label>
            <select v-model="editingEvent.instrument_id"
              class="w-full border rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
              <option value="">Aucun instrument</option>
              <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">
                {{ instr.name }}
              </option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="text-sm text-gray-500">Lien</label>
            <input v-model="editingEvent.link" placeholder="https://..."
              class="w-full border rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>

          <div class="flex justify-between pt-4 border-t">

            <button v-if="editingEvent.id" @click="deleteEvent(editingEvent.id)"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
              Supprimer
            </button>

            <div class="flex gap-2 ml-auto">
              <button @click="cancelDialog" class="px-4 py-2 border rounded-md hover:bg-gray-50 transition">
                Annuler
              </button>

              <button @click="saveEvent"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                Enregistrer
              </button>
            </div>
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