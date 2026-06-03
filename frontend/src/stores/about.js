import { defineStore } from "pinia";
import { ref } from "vue";
import { apiGet } from "../services/api";

const emptyProfile = {
  name: "",
  fullName: "",
  established: "",
  address: "",
  phone: "",
  email: "",
  website: "",
  denomination: "",
  vision: "",
  mission: [],
  history: "",
  description: "",
};

export const useAboutStore = defineStore("about", () => {
  const churchProfile = ref({ ...emptyProfile });
  const pastoralTeam = ref([]);
  const churchActivities = ref([]);
  const loading = ref(false);
  const error = ref(null);

  async function fetchAbout() {
    loading.value = true;
    error.value = null;

    try {
      const data = await apiGet("/api/about");
      churchProfile.value = data.churchProfile ?? { ...emptyProfile };
      pastoralTeam.value = data.pastoralTeam ?? [];
      churchActivities.value = data.churchActivities ?? [];
    } catch (err) {
      error.value = err.message || "Gagal memuat halaman tentang gereja";
    } finally {
      loading.value = false;
    }
  }

  return {
    churchProfile,
    pastoralTeam,
    churchActivities,
    loading,
    error,
    fetchAbout,
  };
});
