<script setup lang="ts">
import {ref, onMounted, watch} from 'vue'
import {api, csrf} from "@/lib/api.ts";
import {useRouter} from "vue-router";

const router = useRouter();
const getPreview = (file: File) => URL.createObjectURL(file);

const props = defineProps<{
  heading: string
  id: string
}>()

const imagURL = import.meta.env.VITE_API_URL + "/"
const msg = ref({
  text:'',
  status: false,
})

const portfolioItems = ref<any[]>([])// komt uit API

const form = ref({
  portfolio_item_id: null,
  id:null,
  title: '',
  description: '',
  media: [] as { type: string, file?: File, url?: string }[]
})

onMounted(async () => {
  if (props.id) {
    try {
      await csrf()
      const { data } = await api.get(`/subjects/${props.id}`)
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

      console.log("Form gevuld:", form.value.media)

      console.log("Portfolio item:", form.value)
    } catch (err: any) {
      console.error("API error:", err)
      msg.value.status = false
      msg.value.text = err.message
    }
  }
})

// Add new media block

const addMedia = () => {
  form.value.media.push({ type: 'image' })// default image

}

onMounted(async () => {
  try {
    const {data} = await api.get("/portfolio")
    // als je paginate gebruikt:
    // items.value = res.data.data
    portfolioItems.value = data.data
    console.log("Portfolio_item_id's: ", portfolioItems)
  } catch (err: any) {
    console.error("API error:", err)

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
  const formData = new FormData()
  formData.append('portfolio_item_id', form.value.portfolio_item_id ?? '')
  formData.append('title', form.value.title)
  formData.append('description', form.value.description)
  form.value.media.forEach((m, index) => {
    formData.append(`media[${index}][media_type]`, m.type)
    if (m.type === 'image' && m.file) {
      formData.append(`media[${index}][file]`, m.file)
    } else if (m.type === 'video' && m.url) {
      formData.append(`media[${index}][url]`, m.url)
    }
  })


  try {
    if (props.id){
      formData.append('_method', 'PUT') // <-- belangrijk
      await api.post(`/subjects/${props.id}`, formData)
    } else {
      await api.post(`/subjects`, formData)
      form.value.title = '';
      form.value.description = '';
    }
    msg.value = { text: "Saved successfully!", status: true }
  } catch (err: any) {
    msg.value = { text: `Error saving subject\n${err}`, status: false }
    console.error(err.response?.data)
  }
}
</script>

<template>
  <main class="myForm ">
    <h2 class="heading" > {{ heading }}</h2>
    <form @submit.prevent="submit" >

      <div class="portfolioItem">
        <label for="portfolioItem">select portfolio item:</label>
        <select class=" ipt" v-model="form.portfolio_item_id" required>
          <option  disabled value="" >Select Portfolio Item</option>
          <option v-for="item in portfolioItems" :key="item.id" :value="item.id">
            {{ item.title }}
          </option>
        </select>
      </div>

      <div class="veld">
        <label for="title">title:</label>
        <input id="title" class="ipt" v-model="form.title" type="text" placeholder="Subject Title" required />
      </div>

      <div class="veld">
        <label for="description">description:</label>
        <input id="description" class="ipt" v-model="form.description" type="text" placeholder="Description" />
      </div>

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
