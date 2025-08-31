<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'

import { useAuth } from "@/stores/auth";
import Logout from "@/components/Logout.vue";
import {onMounted} from "vue";
import {useRouter} from "vue-router";

const router = useRouter()
const auth = useAuth();

onMounted(() => {
  if (auth.user) {
    router.push('/dashboard');
  }
});



</script>

<template>
  <header>
    <Logout />
    <nav :class="  auth.user ? `admin_nav nav` : `visitor_nav nav` ">
      <RouterLink to="/">Home</RouterLink>
      <RouterLink to="/reels">Show Reels</RouterLink>
      <RouterLink to="/about">About</RouterLink>
      <RouterLink to="/upcoming">Upcoming</RouterLink>
      <RouterLink v-if="auth.user" to="/dashboard">Dashboard</RouterLink>
    </nav>

  </header>
  <div class="admin_view">
    <RouterView />
  </div>


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
  position: relative;
  padding: .5rem;

}

.nav{
  width: 100%;
  min-height: 3rem;
  text-align: center;

  place-content: center;
  background-color: var(--color-background-mute);
  position: fixed;
  backdrop-filter: blur(15px);
  z-index: 1000;
  top: 0;
  left: 0;

}

.admin_nav{
  margin-top: 63px;

}
.admin_view{
  display: flex;
  flex-direction: column;
  align-items: center;

}


nav a.router-link-exact-active {
  color: var(--color-text-1);
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



footer {
  flex: 1;
  position: fixed;
  bottom: 0;
  left: 0;
  margin-top: 1rem;
  width: 100%;
  backdrop-filter: blur(30px);
  -webkit-backdrop-filter: blur(30px);
  background-color: var(--color-background-mute);

}
.poweredBlok {
  display: flex;
  flex-direction: column;
  width: 120px;
  height: 53px;
  overflow: hidden;
  position: relative;


}

.poweredBlok strong {
  position: absolute;
  top: 0;
  left: 0;
  color: var(--color-text-1);
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

}
</style>
