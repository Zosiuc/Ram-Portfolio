import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Login from '../components/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import PortfolioList from '../views/PortfolioList.vue';
import PortfolioForm from '../components/PortfolioForm.vue';
import EventList from '../views/EventList.vue';
import EventForm from '../components/EventForm.vue';
import Analytics from '../views/Analytics.vue';
import Messages from '../views/Messages.vue';
import { useAuth } from '../stores/auth';
import About from "@/views/About.vue";
import Contact from "@/views/Contact.vue";
import Upcoming from "@/views/Upcoming.vue";
import SubjectForm from "@/components/SubjectForm.vue";
import Profile from "@/views/Profile.vue";
import Reels from "@/views/Reels.vue";


const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/about', component: About },
  { path: '/reels', component: Reels },
  { path: '/upcoming', component: Upcoming },
  { path: '/contact', component: Contact },
  {
    path: '/dashboard',
    component: Dashboard,
    meta: { auth: true },
    children: [
      { path: 'profile', component: Profile},
      { path: 'portfolio', component: PortfolioList},
      { path: 'portfolio_item/new', component: PortfolioForm, props: { heading: "Add new portfolio item" } },
      { path: 'portfolio_item/:id', component: PortfolioForm, props: (route:any) => ({heading: `Edit portfolio item #${route.params.id}`, id: route.params.id }) },
      { path: 'subject/new', component: SubjectForm, props: { heading: "Add new subject " } },
      { path: 'subject/:id', component: SubjectForm, props: (route:any) => ({heading: `Edit subject #${route.params.id}`,id: route.params.id}) },
      { path: 'events', component: EventList},
      { path: 'events/new', component: EventForm, props: { heading: "Add new event " } },
      { path: 'events/:id', component: EventForm, props: (route:any) => ({heading: `Edit event #${route.params.id}`,id: route.params.id}) },
      { path: 'analytics', component: Analytics },
      { path: 'messages', component: Messages }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// ⬅ hier hoort de beforeEach guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuth();

  if (to.meta.auth && !auth.user) {
    try {
      await auth.me();
      next(); // als gebruiker ingelogd is
    } catch {
      next({ path: '/login', query: { redirect: to.fullPath } });
    }
  } else {
    next();
  }
});

export default router;
