import { defineStore } from 'pinia';
import { api, csrf } from '../lib/api';

export const useAuth = defineStore('auth',{
  state: ()=>({ user: null as any }),
  actions: {

    async login(email:string, password:string){
      await csrf();
      const { data } = await api.post('/login', {
        email: email,
        password: password
      });
      console.log(data.user);
      this.user=data.user; },

    async me(){
      const {data}=await api.get('/me');
      this.user=data; },

    async logout(){
      await api.post('/logout'); this.user=null; }
  }
});
