<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: { type: Object, default: () => ({}) },
    sheetOptions: { type: Array as () => { id: number; name: string }[], default: () => [] },
    instrumentOptions: { type: Array as () => { id: number; name: string }[], default: () => [] },
    isNew: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'save', 'delete', 'cancel'])

// ─── Helpers ──────────────────────────────────────────────────────────────────

const toLocalDatetime = (d: Date | string | null): string => {
    if (!d) return ''
    const date = d instanceof Date ? d : new Date(d)
    if (isNaN(date.getTime())) return ''
    const pad = (n: number) => String(n).padStart(2, '0')
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

// ─── Local state ──────────────────────────────────────────────────────────────

const title = ref<string>(props.modelValue.title ?? '')
const instrument_id = ref<number | null>(props.modelValue.instrument_id ?? null)
const sheet_id = ref<number | null>(props.modelValue.sheet_id ?? null)
const link = ref<string>(props.modelValue.link ?? '')
const startStr = ref<string>(toLocalDatetime(props.modelValue.start ?? null))
const endStr = ref<string>(toLocalDatetime(props.modelValue.end ?? null))

// ─── Sync from parent (covers drag&drop reopening with new values) ────────────

watch(
    () => props.modelValue,
    (v) => {
        title.value = v.title ?? ''
        instrument_id.value = v.instrument_id ?? null
        sheet_id.value = v.sheet_id ?? null
        link.value = v.link ?? ''
        startStr.value = toLocalDatetime(v.start ?? null)
        endStr.value = toLocalDatetime(v.end ?? null)
    },
    { deep: true, immediate: true }
)

// ─── Emit changes upward ──────────────────────────────────────────────────────

const emitUpdate = () => {
    emit('update:modelValue', {
        ...props.modelValue,
        title: title.value,
        instrument_id: instrument_id.value,
        sheet_id: sheet_id.value,
        link: link.value,
        start: startStr.value ? new Date(startStr.value) : props.modelValue.start,
        end: endStr.value ? new Date(endStr.value) : props.modelValue.end,
    })
}

watch([title, instrument_id, sheet_id, link, startStr, endStr], emitUpdate)
</script>

<template>
    <div class="form-wrap">
        <!-- Header -->
        <div class="form-header">
            <h1 class="form-title">{{ isNew ? 'Nouvel événement' : "Modifier l'événement" }}</h1>
        </div>

        <!-- Body -->
        <div class="form-body">

            <!-- Titre -->
            <div class="form-field">
                <label>Titre</label>
                <input v-model="title" type="text" class="input" placeholder="Nom de l'événement" />
            </div>

            <!-- Début / Fin -->
            <div class="row-inline">
                <div class="form-field flex-1">
                    <label>Début</label>
                    <input v-model="startStr" type="datetime-local" class="input input-sm" />
                </div>
                <div class="form-field flex-1">
                    <label>Fin</label>
                    <input v-model="endStr" type="datetime-local" class="input input-sm" />
                </div>
            </div>

            <!-- Instrument -->
            <div class="form-field">
                <label>Instrument</label>
                <select v-model="instrument_id" class="input">
                    <option :value="null">Aucun instrument</option>
                    <option v-for="instr in instrumentOptions" :key="instr.id" :value="instr.id">
                        {{ instr.name }}
                    </option>
                </select>
            </div>

            <!-- Partition -->
            <div class="form-field">
                <label>Partition</label>
                <select v-model="sheet_id" class="input">
                    <option :value="null">Aucune partition</option>
                    <option v-for="sheet in sheetOptions" :key="sheet.id" :value="sheet.id">
                        {{ sheet.name }}
                    </option>
                </select>
            </div>

            <!-- Lien -->
            <div class="form-field">
                <label>Lien</label>
                <input v-model="link" type="url" class="input" placeholder="https://..." />
            </div>

            <!-- Actions -->
            <div class="ef-actions">
                <button v-if="!isNew && modelValue.id" class="btn btn-danger" type="button"
                    @click="emit('delete', modelValue.id)">
                    Supprimer
                </button>
                <div class="btn-group" :style="(!isNew && modelValue.id) ? '' : 'margin-left: auto'">
                    <button class="btn btn-ghost" type="button" @click="emit('cancel')">Annuler</button>
                    <button class="btn-submit ef-submit" type="button" @click="emit('save')">
                        ENREGISTRER
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>