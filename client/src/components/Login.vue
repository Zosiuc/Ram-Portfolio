<template>
  <div class="login">
    <h3>Login</h3>
    <input class="logInput" v-model="email" placeholder="Email" />
    <input class="logInput" v-model="password" type="password" placeholder="Password" />
    <button class="button" @click="submit">Login</button>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useAuth } from "@/stores/auth";
import { useRouter } from "vue-router";

const auth = useAuth();
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
    error.value = e.response?.data?.message || "Login failed";
  }
}
</script>


<style>
.login {
  background-color: hsla(173, 47%, 83%, 0.46);
  border: none;
  border-radius: 7px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  justify-content: center;
  align-items: center;
  width: 100%;
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
