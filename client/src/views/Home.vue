<script setup lang="ts">
import {onMounted, ref} from "vue";
import {api} from "@/lib/api.ts"

const imagURL = import.meta.env.VITE_API_URL + '/storage/'
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

function convertYoutube(url: string) {
  const reg = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/;
  const match = url.match(reg);
  return match ? `https://www.youtube.com/embed/${match[1]}` : url;
}


onMounted(async () => {
  try {
    const {data} = await api.get('/portfolio')
    portfolio.value = data.data
  } catch (err) {
    console.log(err);
  }
})


</script>

<template>

  <main class="portfolio_items">
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
                  <img :src="imagURL + media.media_url" alt="" />
                </div>
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
  display: flex;
  flex-direction: column;
  gap: 3rem;
}

.portfolio_item {
  display: flex;
  flex-direction: column;
  border-bottom: 2px solid #e4e4e4;
  border-radius: 5px;
  gap: 1rem;
  background-color: rgba(218, 227, 227, 0.1);
}

.portfolio_item_header {
  display: flex;
  flex-direction: column;
  padding: .5rem;
  place-items: start;

}

.subjects {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 1rem;
  gap: 20px;

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
  column-count: 2;        /* aantal kolommen, responsief aanpassen */
  column-gap: 16px;
}

.media_image {
  break-inside: avoid;    /* voorkomt dat image wordt gebroken */
  margin-bottom: 16px;
}

.media_image img {
  width: 100%;
  height: auto;
  border-radius: 6px;
  display: block;
}



</style>
