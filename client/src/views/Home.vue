<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import {api} from "@/lib/api.ts"
import Hero from "@/components/Hero.vue";
import {useAuth} from "@/stores/auth.ts";
const logo = ref('/logo.png')
const imagURL = import.meta.env.VITE_API_URL + '/storage/'

const loading = ref<boolean>(true);
const empty = ref<boolean>(false)

const auth = useAuth();
const admin = ref({
  first_name:'',
  job_title:""
})


onMounted(async () => {
  try {
    const {data} = await api.get(`/user-metas/1`)
    admin.value.first_name = data.first_name
    admin.value.job_title = data.job_title
    loading.value = false
  }catch(err: any) {
    loading.value = false;
    empty.value = true
    console.log(err)
  }
});

const portfolio = ref<{
  id: number,
  title: string,
  description: string,
  subjects: {
    id: number
    title: string,
    description: string,
    media: {
      id: number
      media_type: string,
      media_url: string
    }[]
  }[]
}[]>();

const events = ref<{
  id: number,
  title: string,
  description: string,
  date: string,
  location: string,
  media: {
    id: number
    media_type: string,
    media_url: string
  }[]
}[]>();

function convertYoutube(url: string) {
  const reg = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/;
  const match = url.match(reg);
  return match ? `https://www.youtube.com/embed/${match[1]}` : url;
}


onMounted(async () => {
  try {
    const {data} = await api.get('/portfolio_items')
    portfolio.value = data.data
    loading.value = false
  } catch (err) {
    loading.value = false
    empty.value = true;
    console.log(err);
  }
})

onMounted(async () => {
  try {
    const {data} = await api.get('/events')
    events.value = data.data
    loading.value = false
    loading.value = false
  } catch (err) {
    console.log(err);
  }
})


</script>

<template>
  <main v-if="loading" class="loading">
    <h2> Prepare data ... </h2>
  </main>
  <main v-if="empty" class="loading">
    <h2> <router-link to="dashboard/portfolio">Let's build your portfolio!</router-link> </h2>
  </main>
  <main v-else class=" main portfolio_items">
    <div v-if="!auth.user" class="wrapper">
      <img :alt="logo" class="logo " :src="logo"  />

      <div class="hero">
        <Hero
          :name="admin.first_name"
          :title="admin.job_title"
        />
      </div>
    </div>
    <div class="portfolio_item" v-for="portfolio_item in portfolio" :key="portfolio_item.id">
      <div class="portfolio_item_header">
        <h2>{{ portfolio_item.title }}</h2>
        <p>{{ portfolio_item.description }}</p>
      </div>

      <div class="subjects">
        <div v-for="subject in portfolio_item.subjects">
          <div class="subject_item" :key="subject.id">
            <h3 class="subject_title"> {{ subject.title }}</h3>
            <p class="subject_description"> {{ subject.description }}</p>
            <div class="media">
              <!-- VIDEO sectie -->
              <div
                v-for="media in subject.media.filter(m => m.media_type === 'video')"
                :key="'video-' + media.id"
                class="media_video"
              >
                <iframe
                  class="video"
                  :src="convertYoutube(media.media_url)"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                ></iframe>


              </div>

              <!-- IMAGE sectie -->
              <div class="media_gallery">
                <div
                  v-for="media in subject.media.filter(m => m.media_type === 'image')"
                  :key="'image-' + media.id"
                  class="media_image"
                >
                  <img :src="imagURL + media.media_url" alt="media_image">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="events?.filter((e:any) => new Date(e.date).getTime() < Date.now())" class="portfolio_item">
      <div class="portfolio_item_header">
        <h2>Work experiences</h2>
      </div>
      <div  class="subjects">
        <div  v-for="event in events?.filter((e:any) => new Date(e.date).getTime() < Date.now())" :key="event.id">
          <div class="subject_item">
            <h3 class="subject_title">{{ event.title }}</h3>
            <i class="subject_description">{{ event.description }}</i>
            <p>{{ event.location }}</p>
            <i>{{ new Date(event.date).toLocaleString("en", {
              weekday: "long",
              year: "numeric",
              month: "2-digit",
              day: "2-digit",
              hour: "2-digit",
              minute: "2-digit"
            }) }}</i>
          </div>

          <div class="media">
            <!-- VIDEO sectie -->
            <div
              v-for="item in event.media.filter(m => m.media_type === 'video')"
              :key="'video-' + item.id"
              class="media_video"
            >
              <iframe
                class="video"
                :src="convertYoutube(item.media_url)"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>


            </div>

            <!-- IMAGE sectie -->
            <div class="media_gallery">
              <div
                v-for="item in event.media.filter(m => m.media_type === 'image')"
                :key="'image-' + item.id"
                class="media_image"
              >
                <img :src="imagURL + item.media_url" alt="media_image">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </main>

</template>
<style scoped>

.portfolio_items {


}
.wrapper{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.logo {
  display: block;
  margin: 0 auto 2rem;
  max-height: 300px;
}
.hero{
  margin-top: -50px;
  place-items: center;
}


.portfolio_item {
  display: flex;
  flex-direction: column;
  border-bottom: 4px double var(--color-border);
  border-radius: 5px;
  gap: 1rem;
}

.portfolio_item_header {
  display: flex;
  flex-direction: column;

  place-items: start;

  h2 {
    padding: .5rem;
    width: 100%;
    height: 60px;
    background-color: var(--color-background-heading);
    color: var(--color-text-heading);
  }

  p {
    padding: 10px;
  }

}

.subjects {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 50px;

  backdrop-filter: opacity(0.9) blur(5px);
}

.subject_item {

  padding: .5rem;
}

.subject_title {


}

.subject_description {

}

.media {
  margin-top: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;


}

/* video altijd full width */
.media_video {
  width: 100%;
  aspect-ratio: 16/9; /* zorgt voor mooie verhouding */
}

.media_video .video {
  width: 100%;
  height: 100%;
  border-radius: 8px;

}

/* images in een gallery */
.media_gallery {
  column-count: 2; /* aantal kolommen, responsief aanpassen */
  column-gap: 16px;
}

.media_image {
  break-inside: avoid; /* voorkomt dat image wordt gebroken */
  margin-bottom: 16px;
}

.media_image img {
  width: 100%;
  height: auto;
  border-radius: 6px;
  display: block;
}


</style>
