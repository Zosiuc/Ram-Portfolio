<script setup lang="ts">
import AboutItem from '../components/AboutItem.vue'
import BioIcon from '../components/icons/IconBio.vue'
import EducationIcon from '../components/icons/IconEducation.vue'
import CommunityIcon from '../components/icons/IconCommunity.vue'
import SupportIcon from '../components/icons/IconSupport.vue'
</script>

<template>

  <AboutItem>
    <template #icon>
      <BioIcon/>
    </template>
    <template #heading>BIO</template>
    <template #content>
    Holding a BA in Musical Theatre From Sharjah
    Performing Arts Academy, Ram combines formal
    training with a passion for performance that has
    marked his career. Known for his vibrant presence
    on stage, He is celebrated for his passionate
    approach to musical theatre, characterized by his
    boundless energy, supportive demeanor, and
    versatility in acting techniques. Beyond his
    performances, Ram has actively contributed to the
    arts community through teaching and managing
    theatrical festivals.
    <a href="/Ram_Farha.pdf" target="_blank">CV</a>
    </template>
  </AboutItem>

  <AboutItem>
    <template #icon>
      <EducationIcon/>
    </template>
    <template #heading>Education</template>
    <template #content>
    <p>Ram Farha’s educational journey has
      equipped him with a deep understanding
      of the nuances of theatre from both
      perspectives of backstage and onstage
      characters due to his unique experiences
      shifting from being a scenographer to
      being the first Syrian-born artist to hold a
      BA in musical theatre from Sharjah
      Performing Arts Academy and to be
      accepted into the Royal Academy of Music
      for their MA program in Musical Theatre in
      London.</p>
    <div>
      <i class="bg-red-500">-Bachelor of Arts in Musical Theatre Sharjah</i>
      <p>
        Performing Arts Academy, Sharjah
        Concentration: Performance
      </p>
    </div>
    <div>
      <i>-Diploma in Scenography</i>
      <p>
        Higher Institution of Dramatic Arts, Damascus, Syria
        Specialization: Set design, Costume design
      </p>
    </div>
    </template>

  </AboutItem>

  <AboutItem>
    <template #icon>
      <CommunityIcon/>
    </template>
    <template #heading>Community</template>
    <template #content>
    <strong>Got stuck?</strong>
    <p>Ask your question on:</p>
    <a href="mailto:Ramfarha37@gmail.com" target="_blank" rel="noopener">E-mail:
      Ramfarha37@gmail.com</a>
    <a href="tel:+971585395308"> call: +971585395308</a>
    <a href="https://x.com/" target="_blank" rel="noopener"
    >X</a>
    <a href="https://facebook/" target="_blank" rel="noopener">facebook</a>
    <p>You should also follow the official
      <a href="https://instegram/" target="_blank" rel="noopener">instagram</a></p>
    </template>
  </AboutItem>

  <AboutItem>
    <template #icon>
      <SupportIcon/>
    </template>
    <template #heading>Support Me</template>
    <template #content>
    <a href="https://vuejs.org/sponsor/" target="_blank" rel="noopener">becoming a sponsor</a>.
    </template>
  </AboutItem>
</template>
