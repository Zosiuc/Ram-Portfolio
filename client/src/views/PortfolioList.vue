<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {api, csrf} from '../lib/api';
import DashboardList from "@/components/DashboardList.vue";


type PortfolioItem = {
  id: number;
  title: string;
  description?: string | null;
  created_at: string;
  updated_at: string;
};

type Subject = {
  id: number;
  portfolio_item_id: number;
  title: string;
  description?: string | null;
  created_at: string;
  updated_at: string;
};

const items = ref<PortfolioItem[]>([]);
const subjects = ref<Subject[]>([]);
const error = ref(null)

const handleDelete = async (table: string, id: number) => {
  try {
    await csrf()
    await api.delete(`/${table}/${id}`);
    window.document.location.reload();
    console.log(table + 'is bijwerkt !');
  } catch (error) {
    console.log(error);
  }

}

const scrollToView = () => {
  setTimeout(() => {
    const el = document.getElementById('view');
    if (el)
      el.scrollIntoView({behavior: 'smooth'});
  }, 300)

}

onMounted(async () => {
  try {
    const {data} = await api.get("/portfolio_items")
    // als je paginate gebruikt:
    // items.value = res.data.data
    items.value = data.data
    console.log("Portfolio items: ", items)
  } catch (err: any) {
    console.error("API error:", err)
    error.value = err.message
  }
})
onMounted(async () => {
  try {
    const {data} = await api.get("/subjects")

    subjects.value = data.data
    console.log("subjects: ", subjects)
  } catch (err: any) {
    console.error("API error:", err)
    error.value = err.message
  }
})
</script>
<template>
  <main class=" portfolio_dashboard ">
    <DashboardList class="portfolio_items">
      <template #heading>
        Portfolio Items
      </template>
      <template #new>
        <RouterLink to="/dashboard/portfolio_item/new" @click.native="scrollToView">+ New Item
        </RouterLink>
      </template>

      <template #list>
        <div v-if="error && !items" class="">Fout: {{ error }}</div>
        <ul v-else class="items">
          <li class="item" v-for="item in items" :key="item.id">
            <h3>{{ item.title }}</h3>
            <i>{{ item.description }}</i>
            <nav class="buttons_nav">
              <RouterLink class="pi_button green" :to="`/dashboard/portfolio_item/${item.id}`" @click.native="scrollToView">
                Edit
              </RouterLink>
              |
              <button class="pi_button green" @click="handleDelete('portfolio_items',item.id)">
                Delete
              </button>
            </nav>
          </li>
        </ul>
      </template>
    </DashboardList>
    <DashboardList class="subject">
      <template #heading>
        Subjects
      </template>
      <template #new>
        <RouterLink to="/dashboard/subject/new" @click.native="scrollToView">+ New
          Subject
        </RouterLink>
      </template>
      <template #list>
        <div v-if="error && !subjects " class="">Fout: {{ error }}</div>
        <ul v-else class="items">
          <li class="item" v-for="subject in subjects" :key="subject.id">
            <h3>{{ subject.title }}</h3>
            <i>{{ subject.description }}</i>
            <nav class="buttons_nav">
              <RouterLink class="pi_button green"  :to="`/dashboard/subject/${subject.id}`" @click.native="scrollToView">Edit
              </RouterLink>
              |
              <button class="pi_button green" @click="handleDelete('subjects',subject.id)">
                Delete
              </button>
            </nav>

          </li>
        </ul>
      </template>
    </DashboardList>

  </main>

</template>

<style scoped>

.portfolio_dashboard {
  display: flex;
  flex-direction: column;
  justify-content: start;
  width: 100%;

  padding: 1rem 2rem 8rem 2rem;
  gap: 1.5rem;

}


.portfolio_items {
  background: var(--color-background-mute);


}

.subject {
  background: var(--color-background-mute);

}


.items {
  display: flex;
  flex-direction: column;
  gap: 1rem;

}

.item {
  border-bottom: 1px solid var(--color-border);
  border-radius: 7px;
}

.buttons_nav {
  display: flex;
  justify-content: start;
  gap: 1rem;
  align-items: center;
  padding: 5px;
  margin-top: 20px;
}








</style>
