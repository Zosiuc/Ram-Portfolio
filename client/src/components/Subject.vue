<template>
  <div class="item">
    <h2 class="heading" >
      <slot name="heading" ></slot>
    </h2>

    <div class="images" >
      <slot name="images" ></slot>
    </div>

    <div class="content">
      <slot name="content" ></slot>
    </div>


  </div>
</template>

<style scoped>

.item {
  margin-top: 2rem;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  place-content: center;
  gap:10px;
  position: relative;

}

.item .heading {
  font-size: 2.2rem;
  font-weight: 900;
  margin-bottom: 0.2rem;

  color: var(--color-heading);
}

.item .images {
  display: flex;

  overflow:scroll;

}

.item .content {

  display: flex;
  flex-direction: column;
  gap: 1rem;


}





@media (min-width: 1024px) {
  .item {
    margin-top: 0;
    padding: 0.4rem 0 1rem calc(var(--section-gap) / 2);
  }

  /*.images {
    top: calc(50% - 25px);
    left: -26px;
    position: absolute;
    border: 1px solid var(--color-border);
    background: var(--color-background);
    border-radius: 8px;
    width: 50px;
    height: 50px;
  }*/

  .item:before {
    content: ' ';
    border-left: 1px solid var(--color-border);
    position: absolute;
    left: 0;
    bottom: calc(50% + 25px);
    height: calc(50% - 25px);
  }

  .item:after {
    content: ' ';
    border-left: 1px solid var(--color-border);
    position: absolute;
    left: 0;
    top: calc(50% + 25px);
    height: calc(50% - 25px);
  }

  .item:first-of-type:before {
    display: none;
  }

  .item:last-of-type:after {
    display: none;
  }
}
</style>
<script setup lang="ts">
</script>
