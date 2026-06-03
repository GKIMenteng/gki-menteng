import { createRouter, createWebHistory } from "vue-router";
import DashboardView from "../views/DashboardView.vue";
import { useAuthStore } from "../stores/auth";
import { loadRouteData } from "./loaders";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
      meta: { title: "Masuk - GKI Menteng", guest: true },
    },
    {
      path: "/register",
      name: "register",
      component: () => import("../views/RegisterView.vue"),
      meta: { title: "Daftar - GKI Menteng", guest: true },
    },
    {
      path: "/",
      name: "dashboard",
      component: DashboardView,
      meta: { title: "Dashboard - GKI Menteng" },
    },
    {
      path: "/calendar",
      name: "calendar",
      component: () => import("../views/CalendarView.vue"),
      meta: { title: "Calendar - GKI Menteng" },
    },
    {
      path: "/volunteers",
      name: "volunteers",
      component: () => import("../views/VolunteersView.vue"),
      meta: { title: "Volunteers - GKI Menteng" },
    },
    {
      path: "/about",
      name: "about",
      component: () => import("../views/AboutView.vue"),
      meta: { title: "About - GKI Menteng" },
    },
  ],
});

router.beforeEach(async (to) => {
  document.title = to.meta.title || "GKI Menteng";

  const auth = useAuthStore();
  if (!auth.initialized) {
    await auth.bootstrap();
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: "dashboard" };
  }

  if (to.name && !to.meta.guest) {
    await loadRouteData(to.name);
  }
});

export default router;
