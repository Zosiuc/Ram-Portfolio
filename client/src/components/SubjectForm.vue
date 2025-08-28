<script setup lang="ts">
import {onMounted, ref, watch} from 'vue'
import {api, csrf} from "@/lib/api.ts";
import {useRouter} from "vue-router";

const router = useRouter();
const getPreview = (file: File) => URL.createObjectURL(file);

const props = defineProps<{
  heading: string
  id: string
}>()

const imagURL = import.meta.env.VITE_API_URL + "/storage/"
const msg = ref({
  text: '',
  status: false,
})

const portfolioItems = ref<any[]>([])// komt uit API

const form = ref({
  portfolio_item_id: null,
  id: null,
  title: '',
  description: '',
  media: [] as {id?:string, type: string, file?: File, url?: string }[]
})


onMounted(async () => {
  if (props.id) {
    try {
      await csrf()
      const {data} = await api.get(`/subjects/${props.id}`)
      form.value.id = data.id
      form.value.title = data.title
      form.value.portfolio_item_id = data.portfolio_item_id
      form.value.description = data.description
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

// Add new media block

const addMedia = () => {
  form.value.media.push({ type: 'image'})// default image

}

onMounted(async () => {
  try {
    const {data} = await api.get("/portfolio_items")
    // als je paginate gebruikt:
    // items.value = res.data.data
    portfolioItems.value = data.data
  } catch (err: any) {
    console.error("API error:", err)

  }
})

watch(() => msg.value.text, (newVal) => {
  if (newVal) {
    setTimeout(async () => {
      if(msg.value.status) {
        await router.push('/dashboard/portfolio')
      }
      msg.value = {text: '', status: false}

    }, 3000)
  }
})


const submit = async () => {
  const formData = new FormData()
  formData.append('portfolio_item_id', form.value.portfolio_item_id ?? '')
  formData.append('title', form.value.title)
  formData.append('description', form.value.description ?? '')
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
    console.log([...formData.entries()])
    if (props.id) {
      await csrf()
      formData.append('_method', 'PUT') // <-- belangrijk
      await api.post(`/subjects/${props.id}`, formData)
    } else {
      await api.post(`/subjects`, formData)
      form.value.title = '';
      form.value.description = '';
    }
    msg.value = {text: "Saved successfully!", status: true}
  } catch (err: any) {
    msg.value = {text: `Error saving subject\n${err}`, status: false}
    console.error(err.response?.data)
  }
}
</script>

<template>
  <main class="myForm ">
    <h2 class="heading"> {{ heading }}</h2>
    <form @submit.prevent="submit">

      <div class="portfolioItem">
        <label for="portfolioItem">select portfolio item:</label>
        <select class=" ipt" v-model="form.portfolio_item_id" required>
          <option disabled value="">Select Portfolio Item</option>
          <option v-for="item in portfolioItems" :key="item.id" :value="item.id">
            {{ item.title }}
          </option>
        </select>
      </div>

      <div class="veld">
        <label for="title">title:</label>
        <input id="title" class="ipt" v-model="form.title" type="text" placeholder="Subject Title"
               required/>
      </div>

      <div class="veld">
        <label for="description">description:</label>
        <input id="description" class="ipt" v-model="form.description" type="text"
               placeholder="Description"/>
      </div>

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
        <RouterLink class="back" to="/dashboard/portfolio"><strong>Back</strong></RouterLink>
        <button class="button" type="submit"> submit</button>
      </div>
    </form>

  </main>
</template>
<style>
.myForm {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 20px;
  background-color: #7ab6b5;


}

.veld {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.back {
  grid-column: 1;
  height: 30px;
}

.heading {
  grid-column-start: span 3;
}

form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  grid-column: span 4;


}

.media {
  display: flex;
  flex-direction: column;
  border-top: solid 2px #b9dada;;
  padding: 10px;
  gap: 10px;

}

.image-input {
  display: flex;
  gap:1rem;
  flex-direction: column;
  width: 100%;
}

.upload-container {
  display: flex;
  flex-direction: column;
  gap:1rem;
  width: 100%;


}

.previewImage {
  display: flex;
  gap: 10px;
  flex-direction: column;
  place-items: center;
  width: 100%;
}

.previewItem{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 1rem;
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

  &:hover {
    scale: 1.04;
  }

  &:active {
    background-color: hsl(165, 14%, 95%);
    color: #2b2b2b;
    scale: 0.8;
  }
}

.ipt {
  color: #1b1b18;
  text-align: center;
  width: 100%;
  background-color: hsl(173, 47%, 83%);
  border: none;
  border-radius: 16px;
  padding: 8px 20px;
  transition: 0.4s;
  cursor: pointer;
}
</style>
