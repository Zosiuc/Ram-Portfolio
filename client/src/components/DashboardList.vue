<script setup lang="ts">

</script>

<template>
<div class="dashboard-list">

  <h2 class="text-2xl  font-bold mb-4">
    <slot name="heading"></slot>
  </h2>
  <a class="add">
    <slot name="new" ></slot>
  </a>
  <div>
    <slot name="list" ></slot>
  </div>

</div>
</template>

<style scoped>
.dashboard-list {
  display: flex;
  flex-direction: column;
  border-radius: 7px;
  gap: 1rem;
  padding: 1rem;
  box-shadow: 1px 2px 9px 1px rgba(255, 255, 255, 0.38);
  backdrop-filter: blur(15px);
  width: 100%;
}

.add{
  border-bottom: 1px solid #b7dfda;
}


</style>
