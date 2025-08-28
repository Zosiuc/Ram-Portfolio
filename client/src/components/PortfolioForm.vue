<script setup lang="ts">
import {api, csrf} from '../lib/api.ts'
import {onMounted, ref, watch} from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const props = defineProps<{
  heading: string
  id:string
}>()

const msg = ref({
  text:'',
  status: false,
})

const form = ref({
  title:'',
  description:'',
})

onMounted(async () => {
  if (props.id) {
    try {
      const { data } = await api.get(`/portfolio_items/${props.id}`)
      form.value.title = data.title
      form.value.description = data.description
      console.log("Portfolio item:", form.value)
    } catch (err: any) {
      console.error("API error:", err)
    }
  }
})

watch(() => msg.value.text, (newVal) => {
  if (newVal) {
    setTimeout(async () => {
      msg.value = { text: '', status: false }
      await router.push('/dashboard/portfolio')
    }, 3000)
  }
})

const submit = async () => {
  try {
    await csrf()

    let res
    if (props.id) {
      //  bijwerken
      res = await api.put(`/portfolio_items/${props.id}`, form.value)
    } else {
      //  nieuw item
      res = await api.post('/portfolio_items', form.value)
      form.value.title = '';
      form.value.description = '';
    }

    console.log("Saved:", res.data)
    msg.value.status = true
    msg.value.text = 'Saved ! '




  } catch (err: any) {
    if (err.response?.status === 422) {
      console.log('Validation errors:', err.response.data.errors)
      msg.value.status = false
      msg.value.text = `Validation errors: ${err.response.data.errors}`
    } else {
      console.error(err)
      msg.value.status = false
      msg.value.text = `Error: ${err}`
    }
  }
}

</script>

<template>
  <div class="portfolio-form">
    <h2 class="heading" > {{props.heading}}</h2>
    <form @submit.prevent="submit" class="form-inputs">
      <label v-if="props.id">Title:</label>
      <input v-model="form.title" type="text" placeholder="Title"/>
      <label v-if="props.id">Description:</label>
      <textarea class="description" v-model="form.description" type="" placeholder="Description " />
      <p v-if="msg.text" :class=" msg.status ? `success` : `fout` " >
        {{ msg.text }}
      </p>
      <div class="buttons_wrapper">
        <RouterLink class="back" to="/dashboard/portfolio" ><strong>Back</strong></RouterLink>
        <button class="button" type="submit"> submit </button>
      </div>
    </form>
  </div>
</template>
<style>

.portfolio-form{
  display: flex;
  flex-direction: column;
  gap:1rem;
}

.form-inputs {
  display: flex;
  flex-direction: column;
  gap: 20px;

}

.description{
  min-height: 80px;
  text-wrap: wrap;
  width: 100%;
  overflow-wrap: break-word;
  white-space: wrap;
  line-break: normal;
  break-after: avoid;
}

.success, .fout{
  display: flex;
  place-items: center;
  place-content: center;
}

.success {
  background-color: #98e398;
}

.fout {
  background-color: #dc7777;

}

.buttons_wrapper {
  display: flex;
  flex-direction: row;
  padding: 0 20px;
  margin-top: 40px;
  justify-content: space-between;
}

.back {
  cursor: pointer;
  transition: 0.6s;
  &:hover{
    background-color: #e2faf9;
    color: #2d3748;
    border-radius: 16px;
    padding: 4px 12px;

  }
}

@media (min-width: 450px) {


}

</style>
