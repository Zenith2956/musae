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

interface VueCalEventPayload {
  event: Event
}

const page = usePage<{ currentUserId: number }>()
const currentUserId = page.props.currentUserId ?? 1

const events = ref<Event[]>([])
const currentView = ref<any>(null)
const showDialog = ref(false)
const editingEvent = ref<Event | null>(null)
let resolveEvent: ((event: any) => void) | null = null

const unwrapEvent = (payload: Event | VueCalEventPayload) =>
  (payload as VueCalEventPayload).event ?? payload as Event

// =======================================
// FETCH EVENTS
// =======================================
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
      user_id: e.user_id || currentUserId
    }))
  } catch (e) {
    console.error('❌ FETCH ERROR', e)
  }
}

// =======================================
// VUE CAL EVENTS
// =======================================
const onReady = ({ view }: any) => { currentView.value = view; fetchEvents(view.start, view.end) }
const onViewChange = (view: any) => { currentView.value = view; fetchEvents(view.start, view.end) }
const onEventClick = (payload: VueCalEventPayload) => { editingEvent.value = { ...unwrapEvent(payload) }; resolveEvent = null; showDialog.value = true }
const onEventCreate = ({ event, resolve }: any) => { editingEvent.value = { title: '', instrument: '', link: '', start: new Date(event.start), end: new Date(event.end) }; resolveEvent = resolve; showDialog.value = true }

// =======================================
// SAVE EVENT
// =======================================
const saveEvent = async () => {
  if (!editingEvent.value) return
  const event = editingEvent.value

  try {
    if (event.id) {
      await fetch(`/calendar/events/${event.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '' },
        body: JSON.stringify({ ...event, user_id: currentUserId })
      })
      await fetchEvents(currentView.value.start, currentView.value.end)
      const idx = events.value.findIndex(e => e.id === event.id)
      if (idx !== -1) {

        events.value.splice(idx, 1, { ...event })
      } 
    } else {
      const res = await fetch(`/calendar/events`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '' },
        body: JSON.stringify({ ...event, user_id: currentUserId })
      })
      const data = await res.json()
      events.value.push({ id: String(data.id), title: data.title, instrument: data.instrument || '', link: data.link || '', start: new Date(data.start), end: new Date(data.end) })
      resolveEvent?.(data)
    }
  } catch (e) { console.error('❌ SAVE ERROR', e); resolveEvent?.(false) }

  showDialog.value = false
  editingEvent.value = null
}

// =======================================
// DELETE EVENT
// =======================================
const deleteEvent = async () => {
  if (!editingEvent.value?.id) return
  try {
    const res = await fetch(`/calendar/events/${editingEvent.value.id}`, { method: 'DELETE' })
    const data = await res.json()
    if (data.success) events.value = events.value.filter(e => e.id !== editingEvent.value!.id)
  } catch (e) { console.error('❌ DELETE ERROR', e) }
  showDialog.value = false
  editingEvent.value = null
}

// =======================================
// CANCEL DIALOG
// =======================================
const cancelDialog = () => { resolveEvent?.(false); showDialog.value = false; editingEvent.value = null }
</script>

<template>
  <Head title="Calendar" />
  <AppLayout>
    <div class="p-4">
      <vue-cal :events="events"
      :time-from="8*60"
      :time-to="19*60"
      :snap-to-interval="5"
      editable-events
      @ready="onReady"
      @view-change="onViewChange"
      @event-create="onEventCreate"
      @event-click="onEventClick" />
      <div v-if="showDialog" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-4 rounded shadow w-96">
          <h2 class="mb-2 font-bold">{{ editingEvent?.id ? 'Modifier' : 'Créer' }} un événement</h2>
          <input v-model="editingEvent!.title" placeholder="Nom" class="border p-1 w-full mb-2" />
          <input v-model="editingEvent!.instrument" placeholder="Instrument" class="border p-1 w-full mb-2" />
          <input v-model="editingEvent!.link" placeholder="Lien" class="border p-1 w-full mb-2" />
          <div class="flex justify-end gap-2">
            <button v-if="editingEvent?.id" @click="deleteEvent" class="px-3 py-1 bg-red-500 text-white rounded">Supprimer</button>
            <button @click="cancelDialog" class="px-3 py-1 border rounded">Annuler</button>
            <button @click="saveEvent" class="px-3 py-1 bg-blue-500 text-white rounded">Valider</button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.vuecal { height: 650px; }
</style>