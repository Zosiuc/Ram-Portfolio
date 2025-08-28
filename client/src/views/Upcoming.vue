<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import {api} from "@/lib/api.ts"

const empty = ref<boolean>(false);

const imagURL = import.meta.env.VITE_API_URL + '/storage/'
const loading = ref<boolean>(true);
let upcomingEvents = ref()
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
    const {data} = await api.get('/events')
    events.value = data.data

    loading.value = false
    upcomingEvents = computed(() =>
      events.value?.filter((e:any) => new Date(e.date).getTime() > Date.now())
    );
  } catch (err) {
    loading.value = false
    empty.value = true;
    console.log(err);
  }
})


</script>

<template>
  <main v-if="loading" class="loading">
    <h2 > Prepare data ...  </h2>
  </main>
  <main v-if="empty" class="">
    <img src="/favicon.ico" alt="logo" class="logo" />
    <h2> No upcoming events right now !  </h2>
  </main>
  <main v-else class="main events">
    <div class="event" v-for="event in upcomingEvents" :key="event.id">
      <div class="event_header">
        <h3>{{ event.title }}</h3>
        <i>{{ event.description }}</i>
        <p>{{ event.location}}</p>
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
          v-for="item in event.media.filter((m:any) => m.media_type === 'video')"
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
            v-for="item in event.media.filter((m:any) => m.media_type === 'image')"
            :key="'image-' + item.id"
            class="media_image"
          >
            <img :src="imagURL + item.media_url" alt=""/>
          </div>
        </div>
      </div>

    </div>

  </main>

</template>
<style scoped>

.events {


}

.event {
  display: flex;
  flex-direction: column;
  padding: 2rem;
  border-bottom: 2px solid var(--color-border);
  border-radius: 5px;
  gap: 1rem;
  background-color: var(--color-background-soft-blur);
}

.event_header {
  display: flex;
  flex-direction: column;
  padding: .5rem;
  place-items: start;

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
