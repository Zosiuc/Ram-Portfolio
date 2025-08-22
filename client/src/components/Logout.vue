<template>
  <nav class="flex justify-between items-center w-full">
    <div v-if="auth.user">
      <h3> Admin mode</h3>
      <strong> welcome {{ auth.user.name }} !</strong>
    </div>
    <button class="button" v-if="auth.user" @click="auth.logout">Logout</button>
  </nav>
</template>

<script setup lang="ts">
import { useAuth } from "@/stores/auth";
const auth = useAuth();
</script>
