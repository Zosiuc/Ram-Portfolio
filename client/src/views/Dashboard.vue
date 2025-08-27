<script setup lang="ts">

import {RouterLink, RouterView} from "vue-router";
</script>

<template>
  <main class="main_dashboard">
    <nav class=" second_nav  ">
      <RouterLink class="rl" to="/dashboard/profile">Profile</RouterLink>
      <RouterLink class="rl" to="/dashboard/portfolio">Portfolio</RouterLink>
      <RouterLink class="rl" to="/dashboard/events">Events</RouterLink>
      <RouterLink class="rl" to="/dashboard/analytics">Analytics</RouterLink>
    </nav>
    <section class=" dashboard ">
      <RouterView/>
    </section>
  </main>
</template>
<style>
.main_dashboard {
  display: flex;
  flex-direction: column;
  margin-bottom: 30px;
  height: 100%;


}

.second_nav {
  display: flex;
  z-index: 999;
  flex-direction: row;
  align-items: end;
  position: fixed;
  bottom: 92px;
  left: 0;
  gap: 1rem;
  padding: 1rem .5rem;
  backdrop-filter: blur(15px);
  background-color: var(--color-background-mute);

  width: 100%;
}

.rl {
  display: flex;
  transition: 0.8s;
  border-radius: 0.8rem;
  padding:  0 1rem ;
  width: 100%;
  height: fit-content;
  text-align: center;
  align-self: end;
  border-bottom: 2px solid var(--color-border);


  &:hover {
    scale: 1.09;
    color: var(--color-text-2);

  }
}

.dashboard {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin-top: 4rem;
}

@media (min-width: 420px) {

  .main_dashboard {
    flex-direction: row;

  }

  .second_nav {
    display: flex;
    flex-direction: column;
    align-items: start;
    width: 120px;
    height: 85.5vh;
    gap: 1rem;
    background: none;
    padding: 3rem 1rem;
    border-right: 2px solid var(--color-border);
    border-bottom: none;
  }

  .rl {
    width: 100%;
  }

  .dashboard {
    margin-left: 120px;
    width: 100%;
    margin-top: 0;
  }
}
</style>
