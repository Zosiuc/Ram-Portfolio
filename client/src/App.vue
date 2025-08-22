<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import Hero from "@/components/Hero.vue";
import { onMounted } from "vue";
import { useAuth } from "@/stores/auth";
import Logout from "@/components/Logout.vue";

const auth = useAuth();

onMounted(() => {
  auth.me(); // check ingelogde gebruiker
});
</script>

<template>
  <header>
    <Logout />
    <img alt="Vue logo" class="logo" src="@/assets/logo-light.png" width="300" height="300" />

    <div class="wrapper">
      <Hero
        name="Ram"
        title="Singer | Actor | Musical Theater Performer"
      />
      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
        <RouterLink to="/news">Niews</RouterLink>
        <RouterLink to="/contact">Contact</RouterLink>
        <RouterLink to="/dashboard">Dashboard</RouterLink>
      </nav>
    </div>
  </header>

  <main>
    <RouterView class="main"/>
  </main>

  <footer>
    <div class="footer-wrapper">
      <a href="https://zosiuc.dev" target="_blank" class="poweredBlok">
        <strong class=""><i>Powered by</i></strong>
        <img src="https://zosiuc.dev/nb-logo.png" alt="zosiuc logo" />
      </a>
      <strong> &copy; 2025 Ramusical. All rights reserved </strong>
    </div>

  </footer>
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

main {
  margin: 30px 0 ;
  grid-column: span 2;

}

footer {
  position: fixed;
  bottom: 0;
  left: 0;
  color: white;
  align-items: center;
  justify-content: center;
  margin-top: 1rem;
  width: 100%;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  background-color: rgba(155, 166, 168, 0.2);

}
.poweredBlok {
  display: flex;
  flex-direction: column;
  width: 120px;
  height: 70px;
  overflow: hidden;
  position: relative;


}

.poweredBlok strong {
  position: absolute;
  top: 0;
  left: 0;
  color: var(--color-text);
}

.poweredBlok img {
  width: 160px;
  height: 160px;
  position: absolute;
  top: -40px;
  left: -20px;
}

.footer-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
  place-items: center;
  place-content: center;

}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    grid-column: span 2;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
    width: 30rem;
    height: 30rem;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style>
