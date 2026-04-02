
<script setup>
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    sheet: Object
})
</script>

<template>
  <app-layout>
    <div>
      <h1>{{ sheet.name }}</h1>
      <img class="sheet-image" :src="sheet.link" alt="">
      <p>Instrument : {{sheet.instrument_id}}</p>
    </div>
  </app-layout>
</template>
