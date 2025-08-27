<template>
  <div class="about_item">
    <i>
      <slot name="icon"></slot>
    </i>
    <div class="details" >
      <h2>
        <slot name="heading" ></slot>
      </h2>
      <div class="content">
          <slot name="content" ></slot>
      </div>
    </div>

  </div>
</template>

<style scoped>
.about_item {
  display: flex;
  flex-direction: row;
  gap: 1rem;
  position: relative;
  width: 100%;
}

.details {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;

}
.content {
  width: 100%;


}

i {
  display: flex;
  place-items: center;
  place-content: center;
  width: 32px;
  height: 32px;

  color: var(--color-text);
}

h3 {
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 0.4rem;
  color: var(--color-heading);
}

@media (min-width: 1024px) {

}
</style>
<script setup lang="ts">
</script>
