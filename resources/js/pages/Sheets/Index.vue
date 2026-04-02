<script>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },

  props: {
    sheets: Array
  },

  setup() {
    const form = ref({
      name: '',
      link: '',
      instrument_id: ''
    })

    function submit() {
      router.post('/post', form.value)
    }

    return {
      form,
      submit
    }
  }
}
</script>

<template>
  <section class="container">
    <h1>Partitions</h1>
    <div v-for="sheet in sheets" :key="sheet.id" class="sheets-blocks">
      <Link :href="`/sheet/${sheet.id}`" class="sheet-card">
        <p>{{ sheet.name }}</p>
        <img class="sheet-image" :src="sheet.link" alt="">
      </Link>
    </div>
  </section>
  <label for="name">Name: </label>
    <input id="name" type="text" v-model="form.name">
    <label for="name">Link: </label>
    <input id="name" type="text" v-model="form.link">
    <label for="name">Instrument: </label>
    <select id="name" v-model="form.instrument">
        <option value="1">Guitare</option>
        <option value="2">Piano</option>
        <option value="3">Batterie</option>
        <option value="4">Violon</option>
        <option value="5">Saxophone</option>
        <option value="6">Flûte</option>
        <option value="7">Contrebasse</option>
        <option value="8">Clarinette</option>
        <option value="9">Trompette</option>
        <option value="10">Harpe</option>
    </select>
    <button @click="submit">Submit</button>
</template>

<style>

</style>