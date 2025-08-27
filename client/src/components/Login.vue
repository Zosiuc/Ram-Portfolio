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
    console.log("Ingelogde gebruiker:", auth.user);
    await router.push("/dashboard");

  } catch (e: any) {
    error.value = e ||"Login failed";
  }
}
</script>

<template>
  <main class="login-container ">
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
  </main>
</template>

<style>
.login-container{
  background-color: var(--color-border);
  display: flex;
  justify-content: center;
  align-items: center;
  width: 90vw;
  height: 100vh;
  gap: 20px;
}
.wrapper{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.logo {
  display: block;
  margin: 0 auto 2rem;
  max-height: 300px;
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
