import { defineStore } from "pinia";
import { ref } from "vue";
import { apiGet, apiPost, apiPut, apiDelete } from "../services/api";

export const useVolunteersStore = defineStore("volunteers", () => {
  const volunteers = ref([]);
  const ministries = ref([]);
  const sundayServiceSchedule = ref({ date: null, services: [] });
  const loading = ref(false);
  const saving = ref(false);
  const error = ref(null);

  const getVolunteersByMinistry = (ministry) =>
    volunteers.value.filter((vol) => vol.ministry === ministry);

  async function fetchVolunteers() {
    loading.value = true;
    error.value = null;

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
  }

  async function refreshMinistries() {
    try {
      const data = await apiGet("/api/volunteers");
      ministries.value = data.ministries ?? [];
    } catch {
      // keep existing ministries on failure
    }
  }

  async function createVolunteer(volunteerData) {
    saving.value = true;
    error.value = null;

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
  }

  async function updateVolunteer(id, volunteerData) {
    saving.value = true;
    error.value = null;

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
  }

  async function deleteVolunteer(id) {
    saving.value = true;
    error.value = null;

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
    createVolunteer,
    updateVolunteer,
    deleteVolunteer,
  };
});
