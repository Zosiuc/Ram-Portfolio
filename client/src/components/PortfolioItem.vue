<template>
  <div class="item">

    <h2 class="item_heading" >
      <slot name="item_heading" ></slot>
    </h2>


    <div class="description">
      <slot name="description" ></slot>
    </div>

    <div class="subject">
      <slot name="subject" ></slot>
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

.item_heading {
  font-size: 1.2rem;
  font-weight: 800;
  margin-bottom: 1.2rem;


  color: var(--color-heading);
}


.description {

  display: flex;
  flex-direction: column;
  gap: 1rem;

}

.subject_heading {
  font-size: 0.9rem;
  font-weight: 900;
  margin-bottom: 0.2rem;

  color: var(--color-heading);
}

.images {
  display: flex;

  overflow:scroll;

}

.content {

  display: flex;
  flex-direction: column;
  gap: 1rem;


}





@media (min-width: 1024px) {
  .item {
    margin-top: 0;
    padding: 0.4rem 0 1rem calc(var(--section-gap) / 2);
  }


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
