<script setup lang="ts">

import {RouterView} from "vue-router";
</script>

<template>
  <nav class=" second_nav  ">
    <RouterLink to="/dashboard/portfolio">Portfolio</RouterLink>
    <RouterLink to="/dashboard/events">Events</RouterLink>
    <RouterLink to="/dashboard/analytics">Analytics</RouterLink>
    <RouterLink to="/dashboard/messages">Messages</RouterLink>
  </nav>
  <main class=" dashboard " >
      <RouterView />
  </main>
</template>
<style>

.second_nav{
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}
.dashboard{
  display: flex;
  flex-direction: column;
  gap: 30px;
  padding: 20px;
  margin-top: 20px;
  background-color: rgba(121, 181, 180, 0.1);
}
</style>
