import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiGet, apiPost, apiPut, apiDelete } from "../services/api";
import { useBackendService } from "../services/env";
import { db } from "../services/firebase";
import {
  collection,
  query,
  where,
  getDocs,
  addDoc,
  updateDoc,
  deleteDoc,
  doc,
} from "firebase/firestore";

export const useCalendarStore = defineStore("calendar", () => {
  const currentDate = ref(new Date());
  const selectedDate = ref(null);
  const events = ref([]);
  const loading = ref(false);
  const saving = ref(false);
  const error = ref(null);

  const currentMonth = computed(() => currentDate.value.getMonth());
  const currentYear = computed(() => currentDate.value.getFullYear());

  const daysInMonth = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;
    return new Date(year, month + 1, 0).getDate();
  });

  const firstDayOfMonth = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;
    return new Date(year, month, 1).getDay();
  });

  const monthName = computed(() => {
    const months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    return months[currentMonth.value];
  });

  const getEventsForDate = (date) => events.value.filter((event) => event.date === date);

  // Determine if we are in development
  // Firestore references
  const eventsCol = collection(db, 'events');

  // Function implementations
  async function fetchEvents() {
    loading.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const year = currentYear.value;
        const month = currentMonth.value + 1;
        const data = await apiGet(`/api/calendar/events?year=${year}&month=${month}`);
        events.value = data.events ?? [];
      } catch (err) {
        error.value = err.message || "Gagal memuat kalender";
      } finally {
        loading.value = false;
      }
    } else {
      try {
        const year = currentYear.value;
        const month = currentMonth.value + 1;
        const start = `${year}-${month.toString().padStart(2, '0')}-01`;
        const end = `${year}-${month.toString().padStart(2, '0')}-31`;
        const q = query(eventsCol, where("date", ">=", start), where("date", "<=", end));
        const querySnapshot = await getDocs(q);
        events.value = querySnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      } catch (err) {
        error.value = err.message || "Gagal memuat kalender";
      } finally {
        loading.value = false;
      }
    }
  }

  async function createEvent(eventData) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const data = await apiPost("/api/calendar/events", eventData);
        const created = data.event;
        if (created) {
          const eventMonth = new Date(created.date).getMonth();
          const eventYear = new Date(created.date).getFullYear();
          if (
            eventMonth === currentMonth.value &&
            eventYear === currentYear.value
          ) {
            events.value = [...events.value, created].sort(
              (a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time),
            );
          }
        }
        return created;
      } catch (err) {
        error.value = err.message || "Gagal menambah kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        // Add a timestamp for createdAt and updatedAt?
        const docRef = await addDoc(eventsCol, {
          ...eventData,
          createdAt: new Date(),
          updatedAt: new Date(),
        });
        const created = { id: docRef.id, ...eventData };
        const eventMonth = new Date(created.date).getMonth();
        const eventYear = new Date(created.date).getFullYear();
        if (
          eventMonth === currentMonth.value &&
          eventYear === currentYear.value
        ) {
          events.value = [...events.value, created].sort(
            (a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time),
          );
        }
        return created;
      } catch (err) {
        error.value = err.message || "Gagal menambah kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  async function updateEvent(id, eventData) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        const data = await apiPut(`/api/calendar/events/${id}`, eventData);
        const updated = data.event;
        if (updated) {
          const eventMonth = new Date(updated.date).getMonth();
          const eventYear = new Date(updated.date).getFullYear();
          const inCurrentMonth =
            eventMonth === currentMonth.value && eventYear === currentYear.value;

          if (!inCurrentMonth) {
            events.value = events.value.filter((e) => e.id !== id);
          } else {
            const idx = events.value.findIndex((e) => e.id === id);
            if (idx !== -1) {
              events.value[idx] = updated;
            } else {
              events.value = [...events.value, updated];
            }
            events.value = [...events.value].sort(
              (a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time),
            );
          }
        }
        return updated;
      } catch (err) {
        error.value = err.message || "Gagal memperbarui kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        const docRef = doc(db, "events", id);
        await updateDoc(docRef, {
          ...eventData,
          updatedAt: new Date(),
        });
        const updated = { id, ...eventData };
        const eventMonth = new Date(updated.date).getMonth();
        const eventYear = new Date(updated.date).getFullYear();
        const inCurrentMonth =
          eventMonth === currentMonth.value && eventYear === currentYear.value;

        if (!inCurrentMonth) {
          events.value = events.value.filter((e) => e.id !== id);
        } else {
          const idx = events.value.findIndex((e) => e.id === id);
          if (idx !== -1) {
            events.value[idx] = updated;
          } else {
            events.value = [...events.value, updated];
          }
          events.value = [...events.value].sort(
            (a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time),
          );
        }
        return updated;
      } catch (err) {
        error.value = err.message || "Gagal memperbarui kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  async function deleteEvent(id) {
    saving.value = true;
    error.value = null;

    if (useBackendService) {
      try {
        await apiDelete(`/api/calendar/events/${id}`);
        events.value = events.value.filter((e) => e.id !== id);
      } catch (err) {
        error.value = err.message || "Gagal menghapus kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    } else {
      try {
        await deleteDoc(doc(db, "events", id));
        events.value = events.value.filter((e) => e.id !== id);
      } catch (err) {
        error.value = err.message || "Gagal menghapus kegiatan";
        throw err;
      } finally {
        saving.value = false;
      }
    }
  }

  const previousMonth = async () => {
    const date = new Date(currentDate.value);
    date.setMonth(date.getMonth() - 1);
    currentDate.value = date;
    await fetchEvents();
  };

  const nextMonth = async () => {
    const date = new Date(currentDate.value);
    date.setMonth(date.getMonth() + 1);
    currentDate.value = date;
    await fetchEvents();
  };

  const selectDate = (date) => {
    selectedDate.value = date;
  };

  return {
    currentDate,
    selectedDate,
    events,
    loading,
    saving,
    error,
    currentMonth,
    currentYear,
    daysInMonth,
    firstDayOfMonth,
    monthName,
    getEventsForDate,
    fetchEvents,
    createEvent,
    updateEvent,
    deleteEvent,
    previousMonth,
    nextMonth,
    selectDate,
  };
});
