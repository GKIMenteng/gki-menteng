import { defineStore } from "pinia";
import { ref } from "vue";
import { apiGet } from "../services/api";
import { useBackendService } from "../services/env";
import { db } from "../services/firebase";
import {
  doc,
  getDoc,
  collection,
  getDocs,
} from "firebase/firestore";

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

  // Firestore references
  const churchProfileDoc = doc(db, 'about', 'churchProfile');
  const pastoralTeamCol = collection(db, 'pastoralTeam');
  const churchActivitiesCol = collection(db, 'churchActivities');

  async function fetchAbout() {
    loading.value = true;
    error.value = null;

    if (useBackendService) {
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
    } else {
      try {
        // Fetch churchProfile
        const profileSnap = await getDoc(churchProfileDoc);
        churchProfile.value = profileSnap.exists() ? { ...profileSnap.data() } : { ...emptyProfile };

        // Fetch pastoralTeam
        const teamSnapshot = await getDocs(pastoralTeamCol);
        pastoralTeam.value = teamSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

        // Fetch churchActivities
        const activitiesSnapshot = await getDocs(churchActivitiesCol);
        churchActivities.value = activitiesSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      } catch (err) {
        error.value = err.message || "Gagal memuat halaman tentang gereja";
      } finally {
        loading.value = false;
      }
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
