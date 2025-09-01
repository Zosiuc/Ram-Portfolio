<script setup lang="ts">
import {onMounted, ref, watch} from "vue";
import {api, csrf} from "@/lib/api.ts";
import {useRouter} from "vue-router";
import {useAuth} from "@/stores/auth.ts";


const auth = useAuth();

const imgURL = import.meta.env.VITE_API_URL + "/";


// Types
interface Education {
  id: string;
  title: string;
  description: string;
  start_date: string ;
  end_date: string ;
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

interface Reel {
  id: string;
  title: string;
  description: string;
  url: string;
}

// State
const msg = ref({text: "", status: false});

const cover_photo = ref<File | string>();
const profile_photo = ref<File | string>();

const bio = ref("");
const educationBio = ref("")
const details = ref({
  job_title: "",
  first_name: "",
  last_name: "",
  email: "",
  mobile: "",
  birthday: "",
});

const education = ref<Education[]>([]);
const experiences = ref<Experience[]>([]);
const skills = ref<Skill[]>([]);
const social_media = ref<Social[]>([]);
const reels = ref<Reel[]>([]);

// Helpers
const getPreview = (file: File | string) =>
  typeof file === "string" ? imgURL + "storage/" + file : URL.createObjectURL(file);

const mapArray = <T>(arr: any[], keys: (keyof T)[]): T[] =>
  arr.map((obj) =>
    Object.fromEntries(keys.map((k) => [k, obj[k] ?? ""])) as T
  );

onMounted(async () => {
  try {
    const {data} = await api.get(`/user-metas/${auth.user.id}`);

    cover_photo.value = data.cover_photo;
    profile_photo.value = data.profile_photo;
    bio.value = data.bio;
    educationBio.value = data.education_bio;

    Object.assign(details.value, {
      job_title: data.job_title,
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.number,
      birthday: data.birthday,
    });

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

    reels.value = Array.isArray(data.reels)
      ? mapArray<Reel>(data.reels, ["id", "title", "description", "url"])
      : [];

    console.log("Data loaded:", data);
  } catch (err: any) {
    msg.value = {text: err.message, status: false};
    console.error("API Error:", err);
  }
});

// Actions
const addItem = (item: string) => {
  switch (item) {
    case item = 'education'  :
      education.value.push({
        id: "",
        title: "",
        description: "",
        start_date: "",
        end_date: "",
      });
      break
    case item = 'experience'  :
      experiences.value.push({
        id: "",
        title: "",
        description: "",
        company_name: "",
        start_date: "",
        end_date: "",
      });
      break;
    case item = 'skill'  :
      skills.value.push({
        id: "",
        title: "",
        description: "",
      })
      break;
    case item = 'social_media' :
      social_media.value.push({
        id: "",
        title: "",
        description: "",
        url: ""
      })
      break;
    case item = 'reel'  :
      reels.value.push({
        id: "",
        title: "",
        description: "",
        url: ""
      })
      break;

  }
}

const removeItem = (item: string, index: number) => {
  switch (item) {
    case item = 'education'  :
      education.value.splice(index, 1);
      break
    case item = 'experience'  :
      experiences.value.splice(index, 1);
      break;
    case item = 'skill'  :
      skills.value.splice(index, 1);
      break;
    case item = 'social_media' :
      social_media.value.splice(index, 1);
      break;
    case item = 'reel'  :
      reels.value.splice(index, 1);
      break;

  }
}

watch(
  () => msg.value.text,
  (newVal) => {
    if (newVal) setTimeout(() => (msg.value = {text: "", status: false}), 3000);
  }
);

const submit = async () => {
  try {
    const formData = new FormData()
    formData.append('bio', bio.value)
    formData.append('education_bio', educationBio.value)
    formData.append('job_title', details.value.job_title)
    formData.append('first_name', details.value.first_name)
    formData.append('last_name', details.value.last_name)
    formData.append('birthday', details.value.birthday)
    formData.append('number', details.value.mobile)
    formData.append('email', details.value.email)

    if (profile_photo.value instanceof File) {
      formData.append("profile_photo", profile_photo.value)
    }
    if (cover_photo.value instanceof File) {
      formData.append("cover_photo", cover_photo.value)
    }
    education.value.map((e, index) => {
      formData.append(`education[${index}][id]`, e.id)
      formData.append(`education[${index}][title]`, e.title)
      formData.append(`education[${index}][description]`, e.description)
      formData.append(`education[${index}][start_date]`, e.start_date)
      formData.append(`education[${index}][end_date]`, e.end_date)
    })
    experiences.value.map((e, index) => {
      formData.append(`experience[${index}][id]`, e.id)
      formData.append(`experience[${index}][title]`, e.title)
      formData.append(`experience[${index}][description]`, e.description)
      formData.append(`experience[${index}][company_name]`, e.company_name ?? '')
      formData.append(`experience[${index}][start_date]`, e.start_date)
      formData.append(`experience[${index}][end_date]`, e.end_date)
    })
    skills.value.map((s, index) => {
      formData.append(`skills[${index}][id]`, s.id)
      formData.append(`skills[${index}][title]`, s.title)
      formData.append(`skills[${index}][description]`, s.description)

    })
    social_media.value.map((sm, index) => {
      formData.append(`social_media[${index}][id]`, sm.id)
      formData.append(`social_media[${index}][title]`, sm.title)
      formData.append(`social_media[${index}][description]`, sm.description)
      formData.append(`social_media[${index}][url]`, sm.url)

    })
    reels.value.map((r, index) => {
      formData.append(`reels[${index}][id]`, r.id)
      formData.append(`reels[${index}][title]`, r.title)
      formData.append(`reels[${index}][description]`, r.description)
      formData.append(`reels[${index}][url]`, r.url)

    })

    for (const [key, value] of formData.entries()) {
      console.log(`${key}:`, value)
    }
    formData.append('_method', 'PUT')

   await api.post(`/user-metas/${auth.user.id}`, formData)
    msg.value = {text: "Saved successfully!", status: true};
  } catch (err: any) {
    msg.value = {text: `Error saving\n${err}`, status: false};
    console.error(err);
  }
};
</script>


<template>
  <form @submit.prevent="submit" class="profile">
    <section class="form">
      <h2>Cover</h2>

      <div class="cover-photo">
        <img :src=" cover_photo ? getPreview(cover_photo) : '/theatre1.png' " alt="cover-photo"/>
      </div>
      <!-- upload nieuw bestand -->
      <label class="upload-container upload-button">
        update
        <input type="file" accept="image/*" class="hidden"
               @change="(e:any) => {cover_photo = e.target.files[0]; console.log(cover_photo) } "/>
      </label>

    </section>

    <section class="form ">
      <h2>Profile</h2>
      <div class="profile-photo">
        <img :src=" profile_photo ? getPreview(profile_photo) : '/profile.png' "
             alt="profile photo"/>
      </div>
      <!-- upload nieuw bestand -->
      <label class="upload-container upload-button">
        update
        <input type="file" accept="image/*" class="hidden"
               @change="(e:any) => {profile_photo = e.target.files[0]; console.log(profile_photo)} "/>
      </label>
    </section>

    <section class="bio form">
      <h2>BIO</h2>
      <div class="veld raed_only">
        <label for="bio">Bio: </label>
        <textarea id="bio" class="veld_input" placeholder="bio" v-model="bio"/>
      </div>
    </section>

    <section class="info form">
      <h2>Details</h2>
      <div class="veld raed_only">
        <label for="job_title">Job Title: </label>
        <input id="job_title" type="text" class="veld_input" placeholder="Separated with  ' | '  "
               v-model="details.job_title" required/>
      </div>
      <div class="veld raed_only">
        <label for="first_name">First name: </label>
        <input id="first_name" type="text" class="veld_input" placeholder="What is your first name?"
               v-model="details.first_name" required/>
      </div>
      <div class="veld raed_only">
        <label for="last_name">Last name: </label>
        <input id="last_name" type="text" class="veld_input" placeholder="What is your last name?"
               v-model="details.last_name" required/>
      </div>
      <div class="veld raed_only">
        <label for="birthday">Date of birth: </label>
        <input id="birthday" type="date" class="veld_input" placeholder="When is your birthday?"
               v-model="details.birthday"/>
      </div>
      <div class="veld raed_only">
        <label for="email">Email: </label>
        <input id="email" type="text" class="veld_input" placeholder="What is your email address?"
               v-model="details.email"/>
      </div>
      <div class="veld raed_only">
        <label for="mobile">Mobile: </label>
        <input id="mobile" type="text" class="veld_input" placeholder="What is your mobile number?"
               v-model="details.mobile"/>
      </div>
    </section>

    <section class="items form">
      <h2>Education</h2>
      <div class="veld raed_only">
        <label for="education_bio">Education Bio: </label>
        <textarea id="education_bio" v-model="educationBio"
                  placeholder="Talk about your journey of education"/>
      </div>
      <div v-for=" (edu, index ) in education  " :key="edu.id" class="item ">
        <div class="veld raed_only">
          <label for="studie_title">Education name: </label>
          <input id="studie_title" type="text" class="veld_input"
                 placeholder="Type hier your study name" v-model="edu.title" required/>
        </div>
        <div class="veld raed_only">
          <label for="studie_description">Education description: </label>
          <textarea id="studie_description" type="text" class="veld_input"
                    placeholder="Type hier description of your education"
                    v-model="edu.description"/>
        </div>
        <div class="veld raed_only">
          <label for="studie_start_date">Start datum: </label>
          <input id="studie_start_date" type="date" class="veld_input" v-model="edu.start_date"/>
        </div>
        <div class="veld raed_only">
          <label for="studie_end_date">end datum: </label>
          <input id="studie_end_date" type="date" class="veld_input" v-model="edu.end_date"/>
        </div>
        <button @click="removeItem('education',index)" type="button" class="remove "> remove</button>
      </div>
      <button @click="addItem('education')" type="button" class="pro_button"> Add</button>

    </section>

    <section class="items form">
      <h2>Experiences</h2>
      <div class="item " v-for=" (exp, index ) in experiences  " :key="exp.id">
        <div class="veld raed_only">
          <label for="position_title">Position: </label>
          <input id="position_title" type="text" class="veld_input"
                 placeholder="What is your position or job title?" v-model="exp.title" required/>
        </div>
        <div class="veld raed_only">
        <label for="company_name">Company name: </label>
        <input id="company_name" type="text" class="veld_input"
               placeholder="Where did you work?" v-model="exp.company_name" />
      </div>
        <div class="veld raed_only">
          <label for="position_description">Description: </label>
          <textarea id="position_description" type="text" class="veld_input"
                    placeholder="Tel your visitors about your job" v-model="exp.description"/>
        </div>
        <div class="veld raed_only">
          <label for="position_start_date">Start datum: </label>
          <input id="position_start_date" type="date" class="veld_input" v-model="exp.start_date"/>
        </div>
        <div class="veld raed_only">
          <label for="position_end_date">End datum: </label>
          <input id="position_end_date" type="date" class="veld_input" v-model="exp.end_date"/>
        </div>
        <button @click="removeItem('experience',index)" type="button" class="remove "> remove</button>

      </div>
      <button @click="addItem('experience')" type="button" class="pro_button"> Add</button>
    </section>

    <section class="items form">
      <h2>Skills</h2>
      <div class="item " v-for=" (skill, index ) in skills  " :key="skill.id">
        <div class="veld raed_only">
          <label for="skill_title">Skills: </label>
          <input id="skill_title" type="text" class="veld_input" placeholder="What is your skill?"
                 v-model="skill.title" required/>
        </div>
        <div class="veld raed_only">
          <label for="skill_description">Skill description: </label>
          <textarea id="skill_description" type="text" class="veld_input"
                    placeholder="Tel your visitors about your skill if you want..."
                    v-model="skill.description"/>

        </div>
        <button @click="removeItem('skill',index)" type="button" class="remove "> remove</button>
      </div>
      <button @click="addItem('skill')" type="button" class="pro_button"> Add</button>
    </section>

    <section class="items form">
      <h2>Social Media</h2>
      <div class="item " v-for=" (sm, index ) in social_media  " :key="sm.id">
        <div class="veld raed_only">
          <label for="link_title">Platform: </label>
          <input id="link_title" type="text" class="veld_input"
                 placeholder="Type hier (Facebook, X, Instagram ..)" v-model="sm.title" required/>
        </div>
        <div class="veld raed_only">
          <label for="link_url">URL: </label>
          <input id="link_url" type="text" class="veld_input" placeholder="https:\\example.com\"
                 v-model="sm.url" required/>
        </div>
        <div class="veld raed_only">
          <label for="link_description">Link description: </label>
          <textarea id="link_description" type="text" class="veld_input"
                    placeholder="Type hier what do you want to show by your link... "
                    v-model="sm.description"/>
        </div>
        <button @click="removeItem('social_media',index)" type="button" class="remove "> remove</button>
      </div>
      <button @click="addItem('social_media')" type="button" class="pro_button"> Add</button>
    </section>

    <section class="items form">
      <h2>Reels</h2>
      <div class="item " v-for=" (reel, index ) in reels  " :key="reel.id">

        <div class="veld raed_only">
          <label for="reel_title">Reel: </label>
          <input id="reel_title" type="text" class="veld_input" placeholder="Give you reel a title"
                 v-model="reel.title" required/>
        </div>
        <div class="veld raed_only">
          <label for="reel_url">URL: </label>
          <input id="reel_url" type="text" class="veld_input" placeholder="https:\\instagram.com"
                 v-model="reel.url" required/>
        </div>
        <div class="veld raed_only">
          <label for="reel_description">Description: </label>
          <textarea id="reel_description" type="text" class="veld_input"
                    placeholder="Type hier what do you want to show by your reel..."
                    v-model="reel.description"/>
        </div>
        <button @click="removeItem('reel',index)" type="button" class="remove "> remove</button>
      </div>
      <button @click="addItem('reel')" type="button" class="pro_button"> Add</button>
    </section>
    <p v-if="msg.text" :class=" msg.status ? `success` : `fout` " >
      {{ msg.text }}
    </p>
    <button type="submit"  class="button"> Save</button>
  </form>
</template>

<style scoped>
.profile{
  display: flex;
  flex-direction: column;
  max-width: 860px;
  padding-bottom: 1rem;
  color: var(--color-text-1);
}
.cover-photo {
  max-height: 40vh;
  img {
    width: 100%;
    max-height: 100%;
    border-radius: 1rem;
  }
}

.profile-photo {
  display: flex;
  justify-content: center;

  img {
    border-radius: 1rem;
    width: 30vw;
  }

}

.form {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 1rem;

}

.veld {
  display: flex;
  flex-direction: column;
  gap: .5rem;
  border-bottom: 2px solid var(--color-border);
  color: var(--color-text-1);
  font-size: large;

}

.raed_only {

  input, textarea {
    background-color: transparent;
    border: none;
    padding: 1rem;
    box-shadow: none;
    font-family: inherit;
    font-size: medium;
    color: var(--color-text-2) ;

  }

  textarea {
    height: 100px;


  }

}

.items {
  gap: 4rem;
  border-bottom: 3px double var(--color-border);
  padding-bottom: 5rem;
}

.item {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  width: 100%;
  border: 1px dashed var(--color-border);

  border-radius: 1rem;
}

.veld_input {
  width: 100%;
}

.remove {
  border: none;
  border-radius: 16px;
  background-color: var(--color-fout);
  color: var(--color-text-button-2);
  cursor: pointer;
  width: 180px;
  place-self: center;
  font-size: small;
}
.success, .fout{
  display: flex;
  place-items: center;
  place-content: center;
}

.success {
  background-color: #98e398;
  color: var(--color-text-button-2);
}

.fout {
  background-color: #dc7777;

}
</style>
