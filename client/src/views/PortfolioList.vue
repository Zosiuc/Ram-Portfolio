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
    const {data} = await api.get("/portfolio")
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
  <div class=" portfolio_dashboard ">
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
              <RouterLink :to="`/dashboard/portfolio_item/${item.id}`" @click.native="scrollToView">
                Edit
              </RouterLink>
              |
              <button class="delete_button green" @click="handleDelete('portfolio_items',item.id)">
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
        <ul v-else>
          <li class="item" v-for="subject in subjects" :key="subject.id">
            <h3>{{ subject.title }}</h3>
            <i>{{ subject.description }}</i>
            <nav class="buttons_nav">
              <RouterLink :to="`/dashboard/subject/${subject.id}`" @click.native="scrollToView">Edit
              </RouterLink>
              |
              <button class="delete_button green" @click="handleDelete('subjects',subject.id)">
                Delete
              </button>
            </nav>

          </li>
        </ul>
      </template>
    </DashboardList>

  </div>

</template>

<style scoped>

.portfolio_dashboard {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
  padding: 1rem;
  gap: 1rem;
}


.portfolio_items {
  background: linear-gradient(rgba(140, 188, 188, 0.51), rgba(65, 103, 103, 0.76), rgba(88, 129, 129, 0.49), rgba(31, 81, 81, 0.15));

}

.subject {
  background: linear-gradient(rgba(115, 179, 116, 0.55), rgba(74, 124, 74, 0.73), rgba(52, 96, 53, 0.4), rgba(18, 71, 19, 0.19));

}


.items {
  display: flex;
  flex-direction: column;
  gap: 1rem;

}

.item {
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

.delete_button {
  text-decoration: none;
  border: none;
  background: none;
  transition: 0.4s;
  padding: 3px;
  cursor: pointer;

  &:hover {
    background-color: hsl(180, 11%, 50%);
    color: var(--vt-c-white);
  }
}

@media ( min-width: 732px ) {
  .portfolio_dashboard {
    display: flex;
    flex-direction: row;

  }


}

</style>
