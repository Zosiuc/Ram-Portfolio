<script setup lang="ts">
import AboutItem from '../components/AboutItem.vue'
import BioIcon from '../components/icons/IconBio.vue'
import EducationIcon from '../components/icons/IconEducation.vue'
import ExperienceIcon from '../components/icons/IconExperience.vue'
import SkillsIcon from '../components/icons/IconSkills.vue'
import CommunityIcon from '../components/icons/IconCommunity.vue'
import {onMounted, ref} from "vue";
import {api} from '@/lib/api.ts'

const imgURL = import.meta.env.VITE_API_URL + "/";
const loading = ref<boolean>(true);
const empty = ref<boolean>(false);
// Types
interface Education {
  id: string;
  title: string;
  description: string;
  start_date: string | null;
  end_date: string | null;
}

interface Experience extends Education {
  company_name?: string;
}

interface Skill {
  id: string;
  title: string;
  description: string;
}

interface Social {
  id: string;
  title: string;
  description: string;
  url: string;
}

const details = ref({
  first_name: '',
  last_name: '',
  email: '',
  mobile: '',
  profile_photo: '',
  cover_photo: ''

})

const bio = ref('')
const educationBio = ref('')
const education = ref<Education[]>([]);
const experiences = ref<Experience[]>([]);
const skills = ref<Skill[]>([]);
const social_media = ref<Social[]>([]);


const mapArray = <T>(arr: any[], keys: (keyof T)[]): T[] =>
  arr.map((obj) =>
    Object.fromEntries(keys.map((k) => [k, obj[k] ?? ""])) as T
  );

onMounted(async () => {
  try {

    const {data} = await api.get('/user-metas/1',{withCredentials:false})

    Object.assign(details.value, {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.number,
      profile_photo: data.profile_photo,
      cover_photo: data.cover_photo,
    });

    bio.value = data.bio
    educationBio.value = data.education_bio
    education.value = Array.isArray(data.education)
      ? mapArray<Education>(data.education, [
        "id",
        "title",
        "description",
        "start_date",
        "end_date",
      ])
      : [];

    experiences.value = Array.isArray(data.experience)
      ? mapArray<Experience>(data.experience, [
        "id",
        "title",
        "company_name",
        "description",
        "start_date",
        "end_date",
      ])
      : [];

    skills.value = Array.isArray(data.skills)
      ? mapArray<Skill>(data.skills, ["id", "title", "description"])
      : [];

    social_media.value = Array.isArray(data.social_media)
      ? mapArray<Social>(data.social_media, [
        "id",
        "title",
        "description",
        "url",
      ])
      : [];

    loading.value = false

  } catch (err: any) {
    loading.value = false
    empty.value = true
    console.log(err.response?.data)
  }

})
</script>

<template>
  <main v-if="loading" class="loading">
    <h2> Prepare data ... </h2>
  </main>
  <main v-else class="about">
    <section class="photo_container">
      <div class="cover_photo">
        <img :src="details.cover_photo? imgURL +'storage/'+details.cover_photo : '/theatre1.png' " alt="cover-photo"/>
      </div>
      <div class="profile_photo">
        <img :src="details.profile_photo ? imgURL +'storage/'+details.profile_photo : '/profile.png' " alt="profile-photo"/>
        <AboutItem v-if="bio" class="bio_item">
          <template #icon>
            <BioIcon/>
          </template>
          <template #heading>BIO</template>
          <template #content>
            <i class="bio">"{{ bio }}"</i>
            <a class="cv" href="/portfolio.pdf" target="_blank">Portfolio</a>
          </template>
        </AboutItem>
      </div>
    </section>
    <section v-if="empty" class="main">
      <h2> <router-link to="dashboard/profile">Let's build your portfolio!</router-link> </h2>
    </section>
    <section v-else  class="main">
      <AboutItem v-if="education.length > 0" class="education item">
        <template #icon>
          <EducationIcon/>
        </template>
        <template #heading>Education</template>
        <template #content>
          <i class="bio">"{{ educationBio }}"</i>
          <div class="content">
            <div class="contentItem" v-for=" (edu, index) in education " :key="edu.id">
              <p class="item_title">{{ edu.title }}</p>
              <p class="about_description" v-if="edu.description">
                • {{ edu.description }}
              </p>
            </div>
          </div>
        </template>

      </AboutItem>
      <AboutItem v-if="experiences.length > 0" class="experiences item">
        <template #icon>
          <ExperienceIcon/>
        </template>
        <template #heading>Experiences</template>
        <template #content>
          <div class="content">
            <div class="contentItem" v-for=" (exp, index) in experiences "
                 :key="exp.id">
              <p class="item_title">{{ exp.title }}</p>
              <p>{{ exp.company_name }}</p>
              <p v-if="exp.description">
                • {{ exp.description }}
              </p>
            </div>
          </div>
        </template>

      </AboutItem>

      <AboutItem v-if="skills.length > 0" class="skills item">
        <template #icon>
          <SkillsIcon/>
        </template>
        <template #heading>Skills</template>
        <template #content>
          <div class="content">
            <div class="contentItem" v-for=" (s, index) in skills " :key="s.id">
              <p class="item_title">• {{ s.title }}</p>
              <p v-if="s.description">
                {{ s.description }}
              </p>
            </div>
          </div>
        </template>

      </AboutItem>

      <AboutItem class="community item">
        <template #icon>
          <CommunityIcon/>
        </template>
        <template #heading>Community</template>
        <template #content>
          <div class="content">
            <a :href=" `mailto:${details.email}` " target="_blank" rel="noopener">
              <p class="item_title">• E-mail:</p>
              {{ details.email }}</a>
            <a href="tel:+971585395308">
              <p class="item_title">• Call:</p>
              {{ details.mobile }} </a>

            <a class="contentItem" v-for="(sm , index) in social_media " :href="sm.url"
               target="_blank" rel="noopener">
              <p class="item_title">• {{ sm.title }} </p>
              <i v-if="sm.description">{{ sm.description }}</i>
            </a>
          </div>
        </template>
      </AboutItem>
    </section>

  </main>
</template>

<style>

.about {


}

.about .item {
  display: flex;
  flex-direction: row;
  border-bottom: 2px dashed var(--color-border);
  border-radius: 5px;
  padding: 40px 0;
}


.photo_container {
  position: relative;
  width: 88vw;
  border-radius: 0 0 110px 0;
}

.cover_photo {

  img {

    width: 100%;
    max-height: 400px;
    border-radius: 60px 0;
  }
}

.profile_photo {
  display: flex;
  flex-direction: column;
  width: 100%;

  img {
    margin-left: 10px;
    margin-top: -80px;
    width: 35%;
    height: 100%;
    border-radius: 30px;
    box-shadow: 2px 2px 18px 1px black;
  }

  .bio_item {
    display: flex;
    flex-direction: row;
    border-bottom: 2px dashed var(--color-border);
    border-radius: 5px;
    padding: 40px 0;
    color: var(--color-link);

  }
}

.tex-shadow {

  text-shadow: 4px 2px 30px rgba(50, 62, 69, 0.73);

}


.content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.contentItem {
  display: flex;
  flex-direction: column;
  gap: .5rem;
  border-bottom: .5px solid var(--color-link);
  width: fit-content;
}

.item_title {
  font-family: "monospace", sans-serif;
  font-weight: 700;
}


@media (min-width: 420px) {
  .photo_container{
    position: sticky;

  }
  .cover_photo {

    img {

      width: 100%;

    }
  }
}


@media (min-width: 760px) {

  .cover_photo {
    img {

      width: 100%;
      max-height: 500px;

    }
  }

  .profile_photo{
    flex-direction: row;
    gap: 40px;

    img{
      max-height: 300px;

    }
    .bio_item {
      padding-top: .5rem;
      border-bottom: none;
    }
  }
  .cv{
    position: absolute;
    top: 230px;
    left: -188px;
  }
  .photo_container{
    border-bottom: 1px solid var(--color-link);
  }

}

@media (min-width: 895px) {

}
</style>
