<script setup>
defineProps({
    sheet: Object
})
</script>

<template>
  <div>
    <h1>{{ sheet.name }}</h1>
    <img class="sheet-image" :src="sheet.link" alt="">
    <p>Instrument : {{sheet.instrument_id}}</p>
  </div>
</template>