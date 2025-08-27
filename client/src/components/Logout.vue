<template>
  <nav class="logout">
    <div v-if="auth.user">
      <h3> Admin mode</h3>
      <strong> Welcome {{ auth.user.name }} !</strong>
    </div>
    <button class="button" v-if="auth.user" @click="auth.logout">Logout</button>
  </nav>
</template>

<script setup lang="ts">

import { useAuth } from "@/stores/auth";
const auth = useAuth();

</script>
<style scoped>
.logout{
  position: fixed;
  left: 0;
  top:0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  background-color: var(--color-background-mute);
  color: var(--color-text-2);
  backdrop-filter: blur(20px);
  padding: 16px;
  border-radius: 0 0 .5rem .5rem;

  h3{
    color: var(--color-link);
  }
}
</style>
