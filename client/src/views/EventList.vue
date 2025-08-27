<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {api, csrf} from '../lib/api';
import DashboardList from "@/components/DashboardList.vue";


type Event = {
  id: number;
  title: string;
  description?: string | null;
  date: Date | null;
  location: string;
  created_at: string;
  updated_at: string;
};

const events = ref<Event[]>([]);

const error = ref(null)


const handleDelete = async (id: number) => {
  try {
    await csrf()
    await api.delete(`/events/${id}`);
    window.document.location.reload();
    console.log('event is verwijderd !');
  } catch (error) {
    console.log(error);
  }

}


onMounted(async () => {
  try {
    const {data} = await api.get("/events")
    // als je paginate gebruikt:
    // items.value = res.data.data
    events.value = data.data
    console.log("events: ", events)
  } catch (err: any) {
    console.error("API error:", err)
    error.value = err.message
  }
})


</script>

<template>
  <div class=" events-dashboard ">
    <DashboardList class="events">
      <template #heading>Events</template>
      <template #new>
        <RouterLink to="/dashboard/events/new">+ New event</RouterLink>
      </template>
      <template #list>
        <div v-if="error && !events" class="">Fout: {{ error }}</div>
        <ul class="items" v-else>
          <li class="item" v-for="event in events" :key="event.id">
            <h3>{{ event.title }}</h3>
            <i>{{ event.description }}</i>
            <i>{{ event.location }}</i>
            <i class="date">{{ event.date }}</i>
            <nav class="buttons_nav">
              <RouterLink class="pi_button" :to="`/dashboard/events/${event.id}`">
                Edit
              </RouterLink>
              |
              <button class="pi_button green" @click="handleDelete(event.id)">Delete</button>
            </nav>
          </li>
        </ul>
      </template>
    </DashboardList>

  </div>

</template>

<style scoped>

.events-dashboard {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
  padding: 1rem;
  gap: 1rem;
}

.events {
  display: flex;
  flex-direction: column;
  border-radius: 7px;
  gap: 1rem;
  padding: 1rem;
  background: var(--color-background-mute);

  backdrop-filter: blur(15px);
  width: 100%;

}
.items {
  display: flex;
  flex-direction: column;
  gap: 1rem;

}

.item {
  display: flex;
  flex-direction: column;
  border-bottom: 1px solid white;
  border-radius: 7px;
}

.buttons_nav {
  display: flex;
  justify-content: start;
  gap: 1rem;
  align-items: center;
  padding: 5px;
}


.date {
  margin: 10px;
  display: inline-block;
  inline-size: 13ch; /* 2 (- ) + 10 (YYYY-MM-DD) + 1 (spatie) */
  white-space: normal;
  font-size: 12px;
}



@media ( min-width: 732px ) {
  .events-dashboard {
    display: flex;
    flex-direction: row;

  }

}

</style>
