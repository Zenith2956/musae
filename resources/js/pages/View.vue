<script>
import { router } from '@inertiajs/vue3';
export default {
    props:[
      'sheets'
    ],
    data(){
        return{
            form:{
                name: '',
                link: '',
                instrument_id: ''
            }
        }
    },

    methods:{
        submit(){
            router.post('/post', this.form)
        }
    }
  }
</script>

<template>
  <section class="container">
    <h1>Partitions</h1>
    <div class="sheets-blocks" v-for="sheet in sheets" :key="sheet.id">
        <p>{{ sheet.name }}</p>
        <img class="sheet-image" :src="sheet.link" alt="">
    </div>
  </section>
  <label for="name">Name: </label>
    <input id="name" type="text" v-model="form.name">
    <label for="name">Link: </label>
    <input id="name" type="text" v-model="form.link">
    <label for="name">Instrument: </label>
    <select id="name" v-model="form.instrument">
        <option value="1">Piano</option>
        <option value="2">Guitar</option>
        <option value="3">Drums</option>
    </select>
    <button @click="submit">Submit</button>
</template>

<style>
.container{
  width: 90%;
  margin: 0 auto;
}
.sheet-image {
  width: 200px;
  height: auto;
  object-fit: cover;
}
.sheets-blocks {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}
body{
  background-color: white;
  color:brown
}
</style>