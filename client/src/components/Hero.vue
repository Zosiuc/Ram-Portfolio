<script setup lang="ts">
defineProps<{
  name: string;
  title: string;
}>()
</script>

<template>
  <div class="greetings">
    <h1 class="">{{ name }}</h1>
    <h3>
      {{title}}
    </h3>

  </div>
</template>

<style scoped>
h1 {
  font-weight: 500;
  font-size: 2.6rem;
  position: relative;
  top: -20px;
}

h3 {
  font-size: 1.2rem;
}

.greetings h1,
.greetings h3 {
  text-align: center;
}


</style>
