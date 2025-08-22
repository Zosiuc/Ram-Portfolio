<script setup lang="ts">
import {onMounted, ref} from "vue";
import {api} from "@/lib/api.ts"
import DateTimeFormat = Intl.DateTimeFormat;
import {format} from "pathe";

const imagURL = import.meta.env.VITE_API_URL + '/storage/'
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
  } catch (err) {
    console.log(err);
  }
})


</script>

<template>

  <main class="events">
    <div class="event" v-for="event in events" :key="event.id">
      <div class="event_header">
        <h3>{{ event.title }}</h3>
        <i>{{ event.description }}</i>
        <i>{{ event.location}}</i>
        <i>{{ event.date}}</i>
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
            <img :src="imagURL + item.media_url" alt=""/>
          </div>
        </div>
      </div>

    </div>

  </main>

</template>
<style scoped>

.events {
  display: flex;
  flex-direction: column;
  gap: 3rem;
}

.event {
  display: flex;
  flex-direction: column;
  border-bottom: 2px solid #e4e4e4;
  border-radius: 5px;
  gap: 1rem;
  background-color: rgba(218, 227, 227, 0.1);
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
