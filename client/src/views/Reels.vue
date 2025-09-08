<script setup lang="ts">
import {onMounted, onUpdated, nextTick, watch, ref} from "vue";
import {api} from "@/lib/api.js";

const loading = ref<boolean>(true);
const empty = ref<boolean>(false);

interface Reel {
  url: string;
}
declare global {
  interface Window {
    instgrm?: any;
  }
}

const props = defineProps<{
  reels: Reel[];
}>();

// Hulpfunctie om embed opnieuw te laden
const processInstagramEmbeds = () => {
  if (window.instgrm?.Embeds) {
    window.instgrm.Embeds.process();
  }
};

onMounted(async () => {
  await nextTick();
  processInstagramEmbeds();
});

// Wanneer de component update
onUpdated(async () => {
  await nextTick();
  processInstagramEmbeds();
});

// Optioneel: als reels array verandert
watch(
  () => props.reels,
  async () => {
    await nextTick();
    processInstagramEmbeds();
  },
  { deep: true }
);


const reels = ref<{
  id: string,
  title: string,
  description: string,
  url: string
}[]>([])




onMounted(async () => {
  try {
    const {data} = await api.get('/user-metas/1')
    reels.value = data.reels
    reels.value.map((obj) => {
      obj.url = obj.url.replace("https://www.instagram.com/reel/", "https://www.instagram.com/p/");
      obj.url = obj.url.replace("?utm_source=ig_web_copy_link", "?utm_source=ig_embed&amp;utm_campaign=loading");
    })

    loading.value = false;

  } catch (err) {
    loading.value = false;
    empty.value = true;
    console.log(err);
  }
})

</script>

<template>
  <main v-if="loading" class="loading">
    <h2 > Prepare data ...  </h2>
  </main>
  <main v-if="empty" class="loading">
    <h2> <router-link to="dashboard/profile">Let's build your portfolio!</router-link> </h2>
  </main>
  <main v-else class="main reels">
    <div v-for="(reel, index) in reels " :key="index" class="instagram-video-wrapper">
      <h2>{{reel.title}}</h2>
      <i>{{reel.description}}</i>
      <div class="reel">
        <blockquote class="instagram-media "
                    data-instgrm-captioned
                    :data-instgrm-permalink="reel.url"
                    data-instgrm-version="14">
        </blockquote>
      </div>

    </div>
  </main>

</template>


<style scoped>


.instagram-video-wrapper {
  display: flex;
  flex-direction: column;

  gap: 20px;


  h2{
    padding: .5rem;
    width: 100%;
    height: 60px;
    background-color: var(--color-background-heading);
    color: var(--color-text-heading);
  }
  i{
    padding: 12px;
  }
}

.reel {
  display: flex;
  flex-direction: column;
  padding: 1rem;


}

.reel blockquote {
  width: 100% !important;
}

@media (min-width: 950px) {
  .reel {
    max-width: 700px;
    margin: 0 25%;

  }
}
</style>
