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
  title:'',
  description:'',
  location: '',
  date: '',
  media: [] as { type: string, file?: File, url: string }[]
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
  form.value.media.push({ type: 'image' })// default image

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
      console.log("event:", form.value)
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
  formData.append('description', form.value.description)
  formData.append('location', form.value.location)
  formData.append('date', form.value.date)
  form.value.media.forEach((m, index) => {
    formData.append(`media[${index}][media_type]`, m.type)
    if (m.type === 'image' && m.file) {
      formData.append(`media[${index}][file]`, m.file)
    } else if (m.type === 'video') {
      formData.append(`media[${index}][url]`, m.url)
    }
  })

  try {
    await csrf()
    let res
    if (props.id) {
      formData.append('_method', 'PUT')
      //  bijwerken
      res = await api.post(`/events/${props.id}`, formData)
    } else {
      //  nieuw item
      console.log(form.value)
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
      <input v-model="form.title" type="text" placeholder="Title"/>
      <label v-if="props.id">Description:</label>
      <textarea class="description" v-model="form.description" type="" placeholder="Description " />
      <div class="description">
        <label for="date">Kies een datum:</label>
        <input
          id="date"
          type="datetime-local"
          v-model="form.date"
          class="date"
        />
      </div>
      <label for="location">Location:</label>
      <textarea id="location" class="description" v-model="form.location" type="" placeholder="Location " />

      <div v-for="(m, index) in form.media" :key="index" class="media">
        <select v-if="form.media"  v-model="m.type" class="ipt">
          <option value="image">Image</option>
          <option value="video">Video</option>
        </select >

        <!-- Als het een image is -->
        <div v-if="m.type === 'image'" class="image-input">
          <label for="subject_image">subject image:</label>

          <!-- preview database image -->
          <div v-if="!m.file && m.url" class="previewImage">
            <img :src="imagURL+'storage/'+m.url" alt="subject image" class="image" />
            <button class="upload-button" type="button" @click="m.file = undefined; m.url = ''">
              Replace image
            </button>
          </div>

          <!-- preview nieuwe file -->
          <div v-else-if="m.file" class="previewImage">
            <img :src="getPreview(m.file)" alt="preview" class="image" />
            <button class="upload-button" type="button" @click="m.file = undefined">
              Remove
            </button>
          </div>

          <!-- upload nieuw bestand -->
          <label v-else class="upload-container upload-button">
            upload an image
            <input type="file" accept="image/*" class="hidden" @change="(e:any) => m.file = e.target.files[0]" />
          </label>
        </div>


        <!-- Als het een video is -->
        <div v-if="m.type === 'video'" class="upload-container">
          <label for="video" >subject video: </label>
          <input id="video" v-model="m.url" type="text" placeholder="YouTube URL" />
        </div>
      </div>

      <button class="button" type="button" @click="addMedia">+ Add Media</button>

      <p v-if="msg.text" :class=" msg.status ? `success` : `fout` " >
        {{ msg.text }}
      </p>
      <div class="buttons_wrapper">
        <button class="button" type="submit"> submit </button>
        <RouterLink class="back" to="/dashboard/portfolio" ><strong>Back</strong></RouterLink>
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
  width: 50%;

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
