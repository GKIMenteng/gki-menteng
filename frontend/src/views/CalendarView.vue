<template>
  <div class="calendar-page">
    <ApiStatus
      :loading="calendarStore.loading"
      :error="calendarStore.error"
      @retry="calendarStore.fetchEvents"
    />
    <div :class="{ 'opacity-50 pe-none': calendarStore.loading }">
    <div class="row mb-4">
      <div class="col-12">
        <h2 class="section-title">Kalender Kegiatan</h2>
        <div v-if="!canManage" class="alert alert-info py-2 mb-0" role="status">
          Mode tamu — hanya dapat melihat kegiatan.
          <router-link to="/login" class="alert-link">Masuk</router-link>
          untuk menambah, mengubah, atau menghapus.
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 mb-4">
        <div class="luxury-card p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-outline-primary" @click="previousPeriod">
              <i class="bi bi-chevron-left"></i>
            </button>
            <h4 class="mb-0 fw-bold">
              {{ viewMode === "monthly" ? calendarStore.monthName + ' ' + calendarStore.currentYear : weekTitle }}
            </h4>
            <button class="btn btn-outline-primary" @click="nextPeriod">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>

          <div class="d-flex justify-content-center mb-3">
            <div class="btn-group" role="group">
              <button
                class="btn btn-sm"
                :class="viewMode === 'monthly' ? 'btn-primary' : 'btn-outline-primary'"
                @click="toggleView('monthly')"
              >
                Bulanan
              </button>
              <button
                class="btn btn-sm"
                :class="viewMode === 'weekly' ? 'btn-primary' : 'btn-outline-primary'"
                @click="toggleView('weekly')"
              >
                Mingguan
              </button>
            </div>
          </div>

          <div class="calendar-grid">
            <div class="row mb-2">
              <div
                v-for="day in dayNamesFull"
                :key="day"
                class="col text-center fw-bold py-2"
              >
                {{ day.substring(0, 3) }}
              </div>
            </div>

            <div v-if="viewMode === 'monthly'" class="monthly-view">
              <div class="row g-0" v-for="(week, weekIndex) in Math.ceil(monthDates.length / 7)" :key="weekIndex">
                <div
                  v-for="dayObj in monthDates.slice(weekIndex * 7, (weekIndex + 1) * 7)"
                  :key="dayObj.date.getTime() + '-cell'"
                  class="col day-cell"
                  :class="{
                    'empty': dayObj.empty,
                    today: isToday(dayObj.date.getDate(), dayObj.date),
                    'has-events': hasEventsOnDate(dayObj.date),
                    selected: isSelectedDate(dayObj.date),
                  }"
                  @click="!dayObj.empty && selectDate(dayObj.date)"
                >
                  <div class="day-number">{{ dayObj.date.getDate() }}</div>
                  <div v-if="hasEventsOnDate(dayObj.date)" class="event-dots">
                    <span
                      v-for="event in getEventsForDate(dayObj.date).slice(0, 3)"
                      :key="event.id"
                      class="dot"
                      :style="{ backgroundColor: event.color }"
                    ></span>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="weekly-view">
              <div class="row g-0">
                <div
                  v-for="dayObj in weekDates"
                  :key="dayObj.getTime() + '-week'"
                  class="col day-cell px-2"
                  :class="{
                    today: isToday(dayObj.getDate(), dayObj),
                    'has-events': hasEventsOnDate(dayObj),
                    selected: isSelectedDate(dayObj),
                  }"
                  @click="selectDate(dayObj)"
                >
                  <div class="day-number">{{ dayObj.getDate() }}</div>
                  <div v-if="hasEventsOnDate(dayObj)" class="event-dots">
                    <span
                      v-for="event in getEventsForDate(dayObj).slice(0, 3)"
                      :key="event.id"
                      class="dot"
                      :style="{ backgroundColor: event.color }"
                    ></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="luxury-card p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h4 class="mb-0">
              <i class="bi bi-list-check me-2"></i>
              {{ selectedDateHeader }}
            </h4>
            <button
              v-if="canManage && calendarStore.selectedDate"
              type="button"
              class="btn btn-primary btn-sm"
              @click="openCreateModal"
            >
              <i class="bi bi-plus-lg me-1"></i>
              Tambah Kegiatan
            </button>
          </div>

          <p v-if="!calendarStore.selectedDate" class="text-muted small mb-3">
            Klik tanggal di kalender untuk melihat kegiatan{{
              canManage ? " atau menambah kegiatan baru" : ""
            }}.
          </p>

          <div
            v-if="calendarStore.selectedDate && selectedEvents.length === 0"
            class="text-center py-4 text-muted"
          >
            <i class="bi bi-calendar-x" style="font-size: 3rem"></i>
            <p class="mt-3 mb-0">Tidak ada kegiatan pada tanggal ini</p>
          </div>

          <div
            v-for="event in selectedEvents"
            :key="event.id"
            class="event-card mb-3 p-3 border rounded"
          >
            <div class="d-flex align-items-start">
              <div class="event-time me-3 text-center">
                <small class="d-block fw-bold text-primary">{{ event.time }}</small>
                <small class="text-muted">s/d</small>
                <small class="d-block fw-bold text-primary">{{ event.endTime }}</small>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">{{ event.title }}</h6>
                <p class="mb-1 small text-muted">
                  <i class="bi bi-geo-alt me-1"></i>{{ event.location }}
                </p>
                <p v-if="event.description" class="mb-2 small">{{ event.description }}</p>
                <span class="badge" :style="{ backgroundColor: event.color }">
                  {{ event.category }}
                </span>
              </div>
              <div v-if="canManage && calendarStore.selectedDate" class="event-actions ms-2">
                <button
                  type="button"
                  class="btn btn-outline-secondary btn-sm mb-1"
                  title="Edit"
                  @click="openEditModal(event)"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button
                  type="button"
                  class="btn btn-outline-danger btn-sm"
                  title="Hapus"
                  :disabled="calendarStore.saving"
                  @click="confirmDelete(event)"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>

    <!-- Event modal -->
    <div
      class="modal fade"
      :class="{ show: showEventModal }"
      :style="{ display: showEventModal ? 'block' : 'none' }"
      tabindex="-1"
      role="dialog"
      aria-modal="true"
      @click.self="closeEventModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form @submit.prevent="submitEventForm">
            <div class="modal-header">
              <h5 class="modal-title">
                {{ editingEvent ? "Edit Kegiatan" : "Tambah Kegiatan Baru" }}
              </h5>
              <button type="button" class="btn-close" @click="closeEventModal"></button>
            </div>
            <div class="modal-body">
              <div v-if="formError" class="alert alert-danger py-2">{{ formError }}</div>

              <div class="mb-3">
                <label class="form-label">Judul <span class="text-danger">*</span></label>
                <input v-model="eventForm.title" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input v-model="eventForm.date" type="date" class="form-control" required />
              </div>

              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                  <input v-model="eventForm.time" type="time" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                  <input v-model="eventForm.endTime" type="time" class="form-control" required />
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                <input v-model="eventForm.location" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select
                  v-model="eventForm.category"
                  class="form-select"
                  required
                  @change="onCategoryChange"
                >
                  <option value="">Pilih kategori</option>
                  <option v-for="cat in categories" :key="cat.name" :value="cat.name">
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea
                  v-model="eventForm.description"
                  class="form-control"
                  rows="3"
                  placeholder="Opsional"
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" @click="closeEventModal">
                Batal
              </button>
              <button type="submit" class="btn btn-primary" :disabled="calendarStore.saving">
                <span v-if="calendarStore.saving" class="spinner-border spinner-border-sm me-1"></span>
                {{ editingEvent ? "Simpan Perubahan" : "Tambah Kegiatan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      v-if="showEventModal"
      class="modal-backdrop fade show"
      @click="closeEventModal"
    ></div>
  </div>
</template>

<script>
import { useCalendarStore } from "../stores/calendar";
import { useCanManage } from "../composables/useCanManage";
import ApiStatus from "../components/ApiStatus.vue";
import { computed, ref } from "vue";

const CATEGORIES = [
  { name: "Ibadah", color: "#1a237e" },
  { name: "Pendidikan", color: "#c5a572" },
  { name: "Persekutuan", color: "#d4af37" },
  { name: "Musik", color: "#283593" },
  { name: "Doa", color: "#1a237e" },
  { name: "Kegiatan", color: "#d4af37" },
  { name: "Sosial", color: "#c5a572" },
];

function emptyEventForm(date = "") {
  return {
    title: "",
    date,
    time: "09:00",
    endTime: "11:00",
    location: "",
    description: "",
    category: "",
    color: "#1a237e",
  };
}

export default {
  name: "CalendarView",
  components: { ApiStatus },
  setup() {
    const calendarStore = useCalendarStore();
    const { canManage } = useCanManage();
    const viewMode = ref("monthly");
    const showEventModal = ref(false);
    const editingEvent = ref(null);
    const formError = ref(null);
    const eventForm = ref(emptyEventForm());

    const dayNamesFull = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    const isToday = (day, dateObj) => {
      const today = new Date();
      return (
        day === today.getDate() &&
        dateObj.getMonth() === today.getMonth() &&
        dateObj.getFullYear() === today.getFullYear()
      );
    };

    const isSelectedDate = (dateObj) => {
      if (!calendarStore.selectedDate) return false;
      const selected = new Date(calendarStore.selectedDate);
      return (
        dateObj.getDate() === selected.getDate() &&
        dateObj.getMonth() === selected.getMonth() &&
        dateObj.getFullYear() === selected.getFullYear()
      );
    };

    const hasEventsOnDate = (dateObj) => {
      const date = formatDateString(dateObj);
      return calendarStore.getEventsForDate(date).length > 0;
    };

    const getEventsForDate = (dateObj) => {
      const date = formatDateString(dateObj);
      return calendarStore.getEventsForDate(date);
    };

    const selectDate = (dateObj) => {
      const date = formatDateString(dateObj);
      calendarStore.selectDate(date);
    };

    const formatDateString = (dateObj) => {
      const year = dateObj.getFullYear();
      const month = String(dateObj.getMonth() + 1).padStart(2, "0");
      const day = String(dateObj.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    };

    const weekDates = computed(() => {
      const dates = [];
      const now = new Date();
      const startOfWeek = new Date(now);
      const day = now.getDay();
      startOfWeek.setDate(now.getDate() - day);
      
      for (let i = 0; i < 7; i++) {
        const d = new Date(startOfWeek);
        d.setDate(startOfWeek.getDate() + i);
        dates.push(d);
      }
      return dates;
    });

    const monthDates = computed(() => {
      const dates = [];
      const year = calendarStore.currentYear;
      const month = calendarStore.currentMonth;
      
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const firstDayIndex = firstDay.getDay();
      const daysCount = lastDay.getDate();
      
      const prevMonthLastDay = new Date(year, month, 0);
      const prevDaysCount = prevMonthLastDay.getDate();
      
      for (let i = firstDayIndex; i > 0; i--) {
        const prevMonthDay = new Date(year, month - 1, prevDaysCount - i + 1);
        dates.push({ date: prevMonthDay, empty: true });
      }
      
      for (let i = 1; i <= daysCount; i++) {
        const d = new Date(year, month, i);
        dates.push({ date: d, empty: false });
      }
      
      const remainingCells = 42 - dates.length;
      for (let i = 1; i <= remainingCells; i++) {
        const nextMonthDay = new Date(year, month + 1, i);
        dates.push({ date: nextMonthDay, empty: true });
      }
      
      return dates;
    });

    const selectedDateHeader = computed(() => {
      if (!calendarStore.selectedDate) return "Pilih Tanggal";
      const date = new Date(calendarStore.selectedDate);
      const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
      return date.toLocaleDateString("id-ID", options);
    });

    const selectedEvents = computed(() => {
      if (!calendarStore.selectedDate) {
        return [];
      }
      return calendarStore.getEventsForDate(calendarStore.selectedDate);
    });

    const onCategoryChange = () => {
      const cat = CATEGORIES.find((c) => c.name === eventForm.value.category);
      if (cat) {
        eventForm.value.color = cat.color;
      }
    };

    const openCreateModal = () => {
      editingEvent.value = null;
      formError.value = null;
      eventForm.value = emptyEventForm(calendarStore.selectedDate || "");
      showEventModal.value = true;
    };

    const openEditModal = (event) => {
      editingEvent.value = event;
      formError.value = null;
      eventForm.value = {
        title: event.title,
        date: event.date,
        time: event.time,
        endTime: event.endTime,
        location: event.location,
        description: event.description || "",
        category: event.category,
        color: event.color,
      };
      showEventModal.value = true;
    };

    const closeEventModal = () => {
      showEventModal.value = false;
      editingEvent.value = null;
      formError.value = null;
    };

    const submitEventForm = async () => {
      formError.value = null;
      onCategoryChange();

      try {
        if (editingEvent.value) {
          await calendarStore.updateEvent(editingEvent.value.id, { ...eventForm.value });
        } else {
          await calendarStore.createEvent({ ...eventForm.value });
        }
        closeEventModal();
      } catch (err) {
        formError.value = err.message || "Gagal menyimpan kegiatan";
      }
    };

    const confirmDelete = async (event) => {
      if (!window.confirm(`Hapus kegiatan "${event.title}"?`)) {
        return;
      }

      try {
        await calendarStore.deleteEvent(event.id);
      } catch {
        // error shown via store
      }
    };

    const weekTitle = computed(() => {
      const start = weekDates.value[0];
      const end = weekDates.value[6];
      return `${start.getDate()} ${start.toLocaleDateString("id-ID", { month: "short" })} - ${end.getDate()} ${end.toLocaleDateString("id-ID", { month: "short" })} ${end.getFullYear()}`;
    });

    const toggleView = (mode) => {
      viewMode.value = mode;
    };

    const previousPeriod = async () => {
      if (viewMode.value === "monthly") {
        await calendarStore.previousMonth();
      }
    };

    const nextPeriod = async () => {
      if (viewMode.value === "monthly") {
        await calendarStore.nextMonth();
      }
    };

    return {
      calendarStore,
      canManage,
      viewMode,
      dayNamesFull,
      weekDates,
      monthDates,
      isToday,
      isSelectedDate,
      hasEventsOnDate,
      getEventsForDate,
      selectDate,
      selectedDateHeader,
      selectedEvents,
      weekTitle,
      toggleView,
      previousPeriod,
      nextPeriod,
      categories: CATEGORIES,
      showEventModal,
      editingEvent,
      eventForm,
      formError,
      openCreateModal,
      openEditModal,
      closeEventModal,
      submitEventForm,
      confirmDelete,
      onCategoryChange,
    };
  },
};
</script>

<style scoped>
.calendar-grid {
  min-height: 400px;
}

.day-cell {
  padding: 8px;
  min-height: 80px;
  border: 1px solid #e9ecef;
  cursor: pointer;
  transition: var(--transition-smooth);
  position: relative;
  overflow: hidden;
}

.day-cell::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: var(--gradient-gold);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 0;
}

.day-cell:hover {
  background-color: #f8f9fa;
  transform: scale(1.03);
  z-index: 1;
}

.day-cell.today {
  background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
  border-color: #1a237e;
}

.day-cell.selected {
  background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
  border-color: #d4af37;
}

.day-cell.empty {
  background-color: #f8f9fa;
  opacity: 0.5;
  cursor: default;
}

.day-cell.empty:hover {
  background-color: #f8f9fa;
  transform: none;
}

.day-cell.has-events {
  font-weight: 500;
}

.weekly-view .day-cell {
  min-height: 100px;
}

.day-number {
  font-size: 0.9rem;
  margin-bottom: 4px;
  position: relative;
  z-index: 1;
}

.event-dots {
  display: flex;
  gap: 2px;
  flex-wrap: wrap;
  position: relative;
  z-index: 1;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  display: inline-block;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}

.event-card {
  border-left: 4px solid #d4af37 !important;
  transition: var(--transition-smooth);
  border-radius: 12px;
}

.event-card:hover {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  transform: translateX(8px) translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.event-actions {
  display: flex;
  flex-direction: column;
}

.event-actions .btn {
  transition: var(--transition-smooth);
}

.event-actions .btn:hover {
  transform: scale(1.1);
}

.modal.show {
  background: rgba(0, 0, 0, 0.35);
}

.modal-content {
  border-radius: 20px;
  border: none;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
  background: var(--gradient-primary);
  color: white;
  border-radius: 20px 20px 0 0;
  border-bottom: 2px solid var(--accent-color);
}

.modal-title {
  font-weight: 600;
  letter-spacing: 0.5px;
}

.modal-header .btn-close {
  filter: brightness(0) invert(1);
}

@media (max-width: 768px) {
  .day-cell {
    min-height: 60px;
    padding: 5px;
  }

  .day-number {
    font-size: 0.8rem;
  }
}
</style>
