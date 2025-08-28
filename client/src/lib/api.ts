import axios from 'axios';
import Cookies from 'js-cookie';

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL+'/api',
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
});
export async function csrf(){
  await axios.get(import.meta.env.VITE_API_URL+"/sanctum/csrf-cookie", {
    withCredentials: true,
  });
}
// ⬇️ interceptor om altijd de juiste XSRF header te zetten
api.interceptors.request.use((config) => {
  const token = Cookies.get("XSRF-TOKEN");
  if (token) {
    config.headers["X-XSRF-TOKEN"] = decodeURIComponent(token);
  }
  return config;
});
