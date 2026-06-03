<template>
  <div class="dashboard">
    <ApiStatus
      :loading="dashboardStore.loading"
      :error="dashboardStore.error"
      @retry="dashboardStore.fetchDashboard"
    />
    <div :class="{ 'opacity-50 pe-none': dashboardStore.loading }">
    <!-- Welcome Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="luxury-card p-4 gradient-primary text-white">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h2 class="mb-2">Selamat Datang di GKI Menteng</h2>
              <p class="mb-0 opacity-75">
                "Tuhan adalah gembalaku, takkan kekurangan aku" - Mazmur 23:1
              </p>
            </div>
            <div class="col-md-4 text-end">
              <span class="event-badge">{{ currentDate }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card luxury-card">
          <i class="bi bi-people-fill stat-icon"></i>
          <h3 class="fw-bold mb-0">{{ dashboardStore.stats.members }}+</h3>
          <p class="text-muted mb-0">Jemaat</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card luxury-card">
          <i class="bi bi-building stat-icon"></i>
          <h3 class="fw-bold mb-0">{{ dashboardStore.stats.ministries }}</h3>
          <p class="text-muted mb-0">Pelayanan</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card luxury-card">
          <i class="bi bi-heart-fill stat-icon"></i>
          <h3 class="fw-bold mb-0">{{ dashboardStore.stats.volunteers }}+</h3>
          <p class="text-muted mb-0">Volunteer</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card luxury-card">
          <i class="bi bi-calendar-check stat-icon"></i>
          <h3 class="fw-bold mb-0">{{ dashboardStore.stats.events }}</h3>
          <p class="text-muted mb-0">Kegiatan/Bulan</p>
        </div>
      </div>
    </div>

    <!-- Daily Reflection -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="luxury-card p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-title mb-0">Renungan Harian</h4>
            <span v-if="dashboardStore.dailyReflection" class="event-badge">{{
              dashboardStore.dailyReflection.date
            }}</span>
          </div>
          <div class="text-center py-4">
            <template v-if="dashboardStore.dailyReflection">
              <h3 class="text-gold mb-3">"{{ dashboardStore.dailyReflection.content }}"</h3>
              <p class="fw-bold mb-1">{{ dashboardStore.dailyReflection.verse }}</p>
              <p class="text-muted">
                Tema: {{ dashboardStore.dailyReflection.theme }} | Oleh:
                {{ dashboardStore.dailyReflection.author }}
              </p>
            </template>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- News Section -->
      <div class="col-md-8 mb-4">
        <div class="luxury-card p-4">
          <h4 class="section-title">Berita & Pengumuman</h4>
          <div class="row">
            <div v-for="item in dashboardStore.getRecentNews" :key="item.id" class="col-md-6 mb-3">
              <div class="card h-100 border-0 shadow-sm">
                <img
                  :src="item.image"
                  class="card-img-top"
                  :alt="item.title"
                  style="height: 180px; object-fit: cover"
                />
                <div class="card-body">
                  <span class="badge bg-primary mb-2">{{ item.category }}</span>
                  <h6 class="card-title fw-bold">{{ item.title }}</h6>
                  <p class="card-text small text-muted">
                    {{ item.content ? item.content.slice(0, 80) + "..." : "" }}
                  </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                  <small class="text-muted"
                    ><i class="bi bi-calendar me-1"></i>{{ item.date }}</small
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="col-md-4 mb-4">
        <div class="luxury-card p-4">
          <h4 class="section-title">Kegiatan Mendatang</h4>
          <div
            v-for="event in upcomingEventsNextMonth"
            :key="event.id"
            class="mb-3 pb-3 border-bottom"
          >
            <div class="d-flex">
              <div class="flex-shrink-0">
                <div class="bg-light rounded p-2 text-center" style="width: 60px">
                  <small class="d-block text-primary fw-bold">{{ formatDate(event.date) }}</small>
                  <small class="text-muted">{{ event.time }}</small>
                </div>
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1 fw-bold">{{ event.title }}</h6>
                <p class="mb-0 small text-muted">
                  <i class="bi bi-geo-alt me-1"></i>{{ event.location }}
                </p>
                <span class="badge bg-secondary mt-1">{{ event.type }}</span>
              </div>
            </div>
          </div>
          <button class="btn btn-luxury w-100" @click="$router.push('/calendar')">
            Lihat Semua Kegiatan
          </button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard .luxury-card {
  animation: fadeInUp 0.6s ease-out both;
}

.dashboard .row:nth-child(1) .luxury-card {
  animation-delay: 0.1s;
}

.dashboard .row:nth-child(2) .col-md-3:nth-child(1) .stat-card {
  animation-delay: 0.2s;
}

.dashboard .row:nth-child(2) .col-md-3:nth-child(2) .stat-card {
  animation-delay: 0.3s;
}

.dashboard .row:nth-child(2) .col-md-3:nth-child(3) .stat-card {
  animation-delay: 0.4s;
}

.dashboard .row:nth-child(2) .col-md-3:nth-child(4) .stat-card {
  animation-delay: 0.5s;
}

.stat-icon {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.event-badge {
  animation: shimmer 2s linear infinite;
}

@keyframes shimmer {
  0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
  50% { box-shadow: 0 0 0 8px rgba(212, 175, 55, 0); }
  100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
}

.card-img-top {
  transition: var(--transition-smooth);
  border-radius: 12px 12px 0 0;
}

.card:hover .card-img-top {
  transform: scale(1.05);
}

.card {
  border-radius: 15px;
  overflow: hidden;
  border: none;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  transition: var(--transition-smooth);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(26, 35, 126, 0.15);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<script>
import { useDashboardStore } from "../stores/dashboard";
import ApiStatus from "../components/ApiStatus.vue";
import { computed } from "vue";

export default {
  name: "DashboardView",
  components: { ApiStatus },
  setup() {
    const dashboardStore = useDashboardStore();

    const currentDate = computed(() => {
      const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
      return new Date().toLocaleDateString("id-ID", options);
    });

    const upcomingEventsNextMonth = computed(() => {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const endDate = new Date(today);
      endDate.setMonth(endDate.getMonth() + 1);

      const parseEventDate = (dateStr) => {
        const [year, month, day] = dateStr.split("-").map(Number);
        return new Date(year, month - 1, day);
      };

      return dashboardStore.upcomingEvents
        .filter((event) => {
          const eventDate = parseEventDate(event.date);
          return eventDate >= today && eventDate <= endDate;
        })
        .slice(0, 5);
    });

    const formatDate = (date) => {
      const d = new Date(date);
      return d.getDate() + "/" + (d.getMonth() + 1);
    };

    return {
      dashboardStore,
      currentDate,
      upcomingEventsNextMonth,
      formatDate,
    };
  },
};
</script>
