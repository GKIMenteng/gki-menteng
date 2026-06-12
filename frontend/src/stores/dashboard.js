import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiGet } from "../services/api";
import { useBackendService } from "../services/env";
import { db } from "../services/firebase";
import {
  doc,
  getDoc,
  collection,
  getDocs,
  query,
  where,
  orderBy,
  limit,
} from "firebase/firestore";

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

  // Firestore references
  const newsCol = collection(db, 'news');
  const dailyReflectionDoc = doc(db, 'dailyReflection', 'today');
  const upcomingEventsCol = collection(db, 'upcomingEvents');
  const statsDoc = doc(db, 'stats', 'overall');

  async function fetchDashboard() {
    loading.value = true;
    error.value = null;

    if (useBackendService) {
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
    } else {
      try {
        // Fetch news: limit to 10 most recent
        const newsQuery = query(newsCol, orderBy("createdAt", "desc"), limit(10));
        const newsSnapshot = await getDocs(newsQuery);
        news.value = newsSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

        // Fetch dailyReflection for today
        const reflectionSnap = await getDoc(dailyReflectionDoc);
        dailyReflection.value = reflectionSnap.exists() ? reflectionSnap.data() : null;

        // Fetch upcomingEvents: events where date >= today
        const today = new Date().toISOString().split("T")[0];
        const upcomingQuery = query(upcomingEventsCol, where("date", ">=", today), orderBy("date", "asc"));
        const upcomingSnapshot = await getDocs(upcomingQuery);
        upcomingEvents.value = upcomingSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

        // Fetch stats
        const statsSnap = await getDoc(statsDoc);
        stats.value = statsSnap.exists() ? { ...statsSnap.data() } : stats.value;
      } catch (err) {
        error.value = err.message || "Gagal memuat dashboard";
      } finally {
        loading.value = false;
      }
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
