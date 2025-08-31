<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useAuth} from "@/stores/auth";
import {useRouter} from "vue-router";


const auth = useAuth();

onMounted( () => {
  auth.me(); // check ingelogde gebruiker
});

const router = useRouter();
const email = ref("");
const password = ref("");
const error = ref("");

async function submit() {
  try {
    await auth.login(email.value, password.value);
    // redirect ophalen (als iemand bv. /dashboard/events probeerde te openen)
    const redirect = router.currentRoute.value.query.redirect as string || '/dashboard';
    await router.push(redirect);
    console.log("Ingelogde gebruiker:", auth.user);

  } catch (e: any) {
    error.value = e ||"Login failed";
  }
}
</script>

<template>
  <div class="login-container ">
    <div  class="wrapper">
      <img alt="logo" class="logo " src="/logo.png"  />
    </div>
    <div class="login">
      <h3>Login</h3>
      <input class="logInput" v-model="email" placeholder="Email"/>
      <input class="logInput" v-model="password" type="password" placeholder="Password"/>
      <button class="button" @click="submit">Login</button>

      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<style>
.login-container{
  display: flex;
  flex-direction: column;
  background-color: var(--color-border);
  justify-content: start;
  align-items: center;
  gap: 10px;
  padding: 2rem;
  width: 100vw;
  height: 100vh;
}
.wrapper{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.logo {
  display: block;
  margin: 0 auto;
  max-height: 350px;
}

.login {

  backdrop-filter: blur(15px);
  border: none;
  border-radius: 7px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;

  width: 100%;
  max-width: 400px;
}

.logInput {
  display: flex;
  text-align: center;
  width: 100%;
  border: none;
  border-radius: 12px;
  padding: 7px;
}
</style>
