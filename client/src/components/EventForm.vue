<script setup lang="ts">
import {api, csrf} from '../lib/api.ts'
import {onMounted, ref, watch} from "vue";
import { useRouter } from "vue-router";
const imagURL = import.meta.env.VITE_API_URL + "/storage/"
const router = useRouter();
const getPreview = (file: File) => URL.createObjectURL(file);

const props = defineProps<{
  heading: string
  id:string
}>()

const msg = ref({
  text:'',
  status: false,
})

const form = ref({
  id:'',
  title:'',
  description:'',
  location: '',
  date: '',
  media: [] as { id?:'',type: string, file?: File, url?: string }[]
})

watch(() => msg.value.text, (newVal) => {
  if (newVal) {
    setTimeout(async () => {
      if (msg.value.status == true ) {
        await router.push('/dashboard/events')
      }
      msg.value = { text: '', status: false }

    }, 3000)
  }
})

// Add new media block

const addMedia = () => {
  form.value.media.push({type: 'image' })// default image

}

onMounted(async () => {
  if (props.id) {
    try {
      const { data } = await api.get(`/events/${props.id}`)
      form.value.title = data.title
      form.value.description = data.description
      form.value.date = data.date
      form.value.location = data.location
      // Check of media bestaat
      if (Array.isArray(data.media)) {
        form.value.media = data.media.map((m: any) => ({
          id: m.id,
          type: m.media_type,
          url: m.media_url,
          file: undefined
        }))
      } else {
        form.value.media = [] // geen media → lege array
      }

      console.log("database media:", form.value)
    } catch (err: any) {
      console.error("API error:", err)
      msg.value.status = false
      msg.value.text = err.message
    }
  }
})

const submit = async () => {
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description ?? '')
  formData.append('location', form.value.location ?? '')
  formData.append('date', form.value.date)
  form.value.media.map((m, index) => {

    if (m.id) {
      formData.append(`media[${index}][id]`, m.id)
    }
    formData.append(`media[${index}][media_type]`, m.type)
    if (m.type === 'image' && m.file) {
      formData.append(`media[${index}][file]`, m.file)
    } else if (m.type === 'image' && m.url) {
      // bestaand bestand behouden
      formData.append(`media[${index}][url]`, m.url)
    }
    if (m.type === 'video' && m.url) {
      formData.append(`media[${index}][url]`, m.url)
    }

  })

  try {
    await csrf()
    let res
    if (props.id) {
      formData.append('_method', 'PUT')
      console.log("pre sup: ",form.value)
      //  bijwerken
      res = await api.post(`/events/${props.id}`, formData)
    } else {
      //  nieuw item
      console.log("pre sup: ",form.value)
      res = await api.post('/events', formData)
      form.value.title = '';
      form.value.description = '';
      form.value.date = '';
      form.value.location = '';
      form.value.media = [];
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
  <div class="event-form">
    <h2 class="heading" > {{props.heading}}</h2>
    <form @submit.prevent="submit" class="form-inputs">
      <label v-if="props.id">Title:</label>
      <input v-model="form.title" type="text" placeholder="Title" required/>
      <label v-if="props.id">Description:</label>
      <textarea class="description" v-model="form.description" type="" placeholder="Description " />
      <div class="description">
        <label for="date">Kies een datum:</label>
        <input
          id="date"
          type="datetime-local"
          v-model="form.date"
          class="date"
          required
        />
      </div>
      <label for="location">Location:</label>
      <textarea id="location" class="description" v-model="form.location" type="" placeholder="Location " />

      <div v-for="(m, index) in form.media" :key="index" class="media">
        <!-- Media type select -->
        <select v-model="m.type" class="ipt">
          <option value="image">Image</option>
          <option value="video">Video</option>
        </select>

        <!-- Als het een image is -->
        <div v-if="m.type === 'image'" class="image-input">
          <label for="subject_image">subject image:</label>

          <!-- preview database image -->
          <div v-if="!m.file && m.url" class="previewImage">
            <img :src="imagURL + m.url" alt="subject image" class="image" />
            <button class="upload-button" type="button" @click=" form.media.splice(index,1) " >
              Remove
            </button>
          </div>

          <!-- preview nieuwe file -->
          <div v-else-if="m.file" class="previewImage">
            <img :src="getPreview(m.file)" alt="preview" class="image" />
            <button class="upload-button" type="button" @click="form.media.splice(index,1)">
              Remove
            </button>
          </div>

          <!-- upload nieuw bestand -->
          <label v-else class="upload-container upload-button">
            upload an image
            <input type="file" accept="image/*" multiple class="hidden" @change="(e:any) => {
              Array.from(e.target.files as FileList).forEach((file) => {
                form.media.push({type:'image', file:file} )
              })

            }" />
          </label>
        </div>

        <!-- VIDEO MEDIA -->
        <div v-if="m.type === 'video'" class="upload-container">
          <label>Subject video:</label>
          <input type="text" v-model="m.url" placeholder="YouTube URL"/>
          <button type="button" class="upload-button" @click="m.url = ''">Remove video</button>
        </div>
      </div>

      <!-- Voeg nieuw media blok toe -->
      <button type="button" class="button" @click="addMedia ">
        + Add Media
      </button>
      <p v-if="msg.text" :class=" msg.status ? `success` : `fout` ">
        {{ msg.text }}
      </p>
      <div class="buttons_wrapper">
        <RouterLink class="back" to="/dashboard/events" ><strong>Back</strong></RouterLink>
        <button class="button" type="submit"> submit </button>
      </div>
    </form>
  </div>
</template>
<style>

.event-form{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap:1rem;
}

.form-inputs {
  display: flex;
  flex-direction: column;
  gap: 20px;

}

.description{
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 1rem;
  min-height: 80px;
  text-wrap: wrap;
  width: 100%;
  overflow-wrap: break-word;
  white-space: wrap;
  line-break: normal;
  break-after: avoid;
}
.date {
  width: 100%;
}
.media {
  display: flex;
  flex-direction: column;
  border-top: solid 2px #b9dada; ;
  padding: 10px;
  gap:10px;

}
.image-input {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.upload-container{
  display: flex;
  flex-direction: column;
  width: 100%;


}
.previewImage {
  display: flex;
  gap: 10px;
  flex-direction: column;
  place-items: center;
  width: 100%;
}
.image {


}

.upload-button {
  cursor: pointer;
  background-color: white;

  border: none;
  border-radius: 7px;
  color: slategray;
  text-align: center;
  &:hover{
    scale: 1.04;
  }
  &:active{
    background-color: hsl(165, 14%, 95%);
    color: #2b2b2b;
    scale: 0.8;
  }
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
  margin-top: 40px;
  padding: 0 20px;
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
  .portfolio-form{
    justify-content: center;
    align-items: center;
  }
  .form-inputs {
    width: 50%;

  }
}

</style>
