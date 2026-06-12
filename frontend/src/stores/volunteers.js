import { defineStore } from "pinia";
import { ref } from "vue";
import { apiGet, apiPost, apiPut, apiDelete } from "../services/api";
import { useBackendService } from "../services/env";
import { db } from "../services/firebase";
import {
  collection,
  query,
  where,
  getDocs,
  getDoc,
  addDoc,
  updateDoc,
  deleteDoc,
  doc,
} from "firebase/firestore";

export const useVolunteersStore = defineStore("volunteers", () => {
  const volunteers = ref([]);
  const ministries = ref([]);
  const sundayServiceSchedule = ref({ date: null, services: [] });
  const loading = ref(false);
  const saving = ref(false);
  const error = ref(null);

  const getVolunteersByMinistry = (ministry) =>
    volunteers.value.filter((vol) => vol.ministry === ministry);

  // Firestore references
  const volunteersCol = collection(db, 'volunteers');
  const ministriesCol = collection(db, 'ministries');
  const sundayServiceScheduleDoc = doc(db, 'sundayServiceSchedule', 'current');

  // Function implementations
  async function fetchVolunteers() {
    loading.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const data = await apiGet("/api/volunteers");
        volunteers.value = data.volunteers ?? [];
        ministries.value = data.ministries ?? [];
        sundayServiceSchedule.value = data.sundayServiceSchedule ?? {
          date: null,
          services: [],
        };
      } catch (err) {
        error.value = err.message || "Gagal memuat data volunteer";
      } finally {
        loading.value = false;
      }
    } else {
      try {
        // Fetch volunteers
        const volunteersSnapshot = await getDocs(volunteersCol);
        volunteers.value = volunteersSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

        // Fetch ministries
        const ministriesSnapshot = await getDocs(ministriesCol);
        ministries.value = ministriesSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

        // Fetch sundayServiceSchedule
        const scheduleSnap = await getDoc(sundayServiceScheduleDoc);
        if (scheduleSnap.exists()) {
          sundayServiceSchedule.value = { id: scheduleSnap.id, ...scheduleSnap.data() };
        } else {
          sundayServiceSchedule.value = { date: null, services: [] };
        }
      } catch (err) {
        error.value = err.message || "Gagal memuat data volunteer";
      } finally {
        loading.value = false;
      }
    }
  }

  async function refreshMinistries() {
    if (useBackendService) {
      try {
        const data = await apiGet("/api/volunteers");
        ministries.value = data.ministries ?? [];
      } catch {
        // keep existing ministries on failure
      }
    } else {
      try {
        const ministriesSnapshot = await getDocs(ministriesCol);
        ministries.value = ministriesSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      } catch {
        // keep existing ministries on failure
      }
    }
  }

  async function createVolunteer(volunteerData) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const data = await apiPost("/api/volunteers", volunteerData);
        const created = data.volunteer;
        if (created) {
          volunteers.value = [...volunteers.value, created];
          await refreshMinistries();
        }
        return created;
      } catch (err) {
        error.value = err.message || "Gagal menambah volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        const docRef = await addDoc(volunteersCol, {
          ...volunteerData,
          createdAt: new Date(),
          updatedAt: new Date(),
        });
        const created = { id: docRef.id, ...volunteerData };
        volunteers.value = [...volunteers.value, created];
        await refreshMinistries();
        return created;
      } catch (err) {
        error.value = err.message || "Gagal menambah volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  async function updateVolunteer(id, volunteerData) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const data = await apiPut(`/api/volunteers/${id}`, volunteerData);
        const updated = data.volunteer;
        if (updated) {
          const idx = volunteers.value.findIndex((v) => v.id === id);
          if (idx !== -1) {
            volunteers.value[idx] = updated;
          }
          await refreshMinistries();
        }
        return updated;
      } catch (err) {
        error.value = err.message || "Gagal memperbarui volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        const docRef = doc(db, "volunteers", id);
        await updateDoc(docRef, {
          ...volunteerData,
          updatedAt: new Date(),
        });
        const updated = { id, ...volunteerData };
        const idx = volunteers.value.findIndex((v) => v.id === id);
        if (idx !== -1) {
          volunteers.value[idx] = updated;
        }
        await refreshMinistries();
        return updated;
      } catch (err) {
        error.value = err.message || "Gagal memperbarui volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  async function deleteVolunteer(id) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        await apiDelete(`/api/volunteers/${id}`);
        volunteers.value = volunteers.value.filter((v) => v.id !== id);
        await refreshMinistries();
      } catch (err) {
        error.value = err.message || "Gagal menghapus volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        await deleteDoc(doc(db, "volunteers", id));
        volunteers.value = volunteers.value.filter((v) => v.id !== id);
        await refreshMinistries();
      } catch (err) {
        error.value = err.message || "Gagal menghapus volunteer";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  return {
    volunteers,
    ministries,
    sundayServiceSchedule,
    loading,
    saving,
    error,
    getVolunteersByMinistry,
    fetchVolunteers,
    refreshMinistries,
    createVolunteer,
    updateVolunteer,
    deleteVolunteer,
  };
});
