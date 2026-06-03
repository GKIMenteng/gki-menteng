import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiGet } from "../services/api";

export const useDashboardStore = defineStore("dashboard", () => {
  const news = ref([]);
  const dailyReflection = ref(null);
  const upcomingEvents = ref([]);
  const stats = ref({
    members: 0,
    ministries: 0,
    volunteers: 0,
    events: 0,
  });
  const loading = ref(false);
  const error = ref(null);

  const getRecentNews = computed(() => news.value.slice(0, 4));

  const getTodayEvents = computed(() => {
    const today = new Date().toISOString().split("T")[0];
    return upcomingEvents.value.filter((event) => event.date === today);
  });

  async function fetchDashboard() {
    loading.value = true;
    error.value = null;

    try {
      const data = await apiGet("/api/dashboard");
      news.value = data.news ?? [];
      dailyReflection.value = data.dailyReflection ?? null;
      upcomingEvents.value = data.upcomingEvents ?? [];
      stats.value = data.stats ?? stats.value;
    } catch (err) {
      error.value = err.message || "Gagal memuat dashboard";
    } finally {
      loading.value = false;
    }
  }

  return {
    news,
    dailyReflection,
    upcomingEvents,
    stats,
    loading,
    error,
    getRecentNews,
    getTodayEvents,
    fetchDashboard,
  };
});
