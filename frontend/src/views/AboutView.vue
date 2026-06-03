<template>
  <div class="about-page">
    <ApiStatus
      :loading="aboutStore.loading"
      :error="aboutStore.error"
      @retry="aboutStore.fetchAbout"
    />
    <div :class="{ 'opacity-50 pe-none': aboutStore.loading }">
      <!-- Church Profile Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="luxury-card p-5 gradient-primary text-white">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h1 class="display-4 fw-bold mb-3">{{ aboutStore.churchProfile.name }}</h1>
                <p class="lead mb-2">{{ aboutStore.churchProfile.fullName }}</p>
                <p class="mb-0 opacity-75">
                  Berdiri Sejak {{ aboutStore.churchProfile.established }}
                </p>
              </div>
              <div class="col-md-4 text-center">
                <i class="bi bi-building display-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Church Information -->
        <div class="col-lg-8">
          <!-- About Church -->
          <div class="luxury-card p-4 mb-4">
            <h3 class="section-title">Tentang Kami</h3>
            <p class="lead">{{ aboutStore.churchProfile.description }}</p>
            <p>{{ aboutStore.churchProfile.history }}</p>
          </div>

          <!-- Vision & Mission -->
          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <div class="luxury-card p-4 h-100">
                <div class="text-center mb-3">
                  <i class="bi bi-eye-fill display-4 text-gold"></i>
                </div>
                <h4 class="text-center mb-3">Visi</h4>
                <p class="text-center fw-bold">{{ aboutStore.churchProfile.vision }}</p>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="luxury-card p-4 h-100">
                <div class="text-center mb-3">
                  <i class="bi bi-bullseye display-4 text-gold"></i>
                </div>
                <h4 class="text-center mb-3">Misi</h4>
                <ul class="list-unstyled">
                  <li
                    v-for="(mission, index) in aboutStore.churchProfile.mission"
                    :key="index"
                    class="mb-2"
                  >
                    <i class="bi bi-check-circle-fill text-gold me-2"></i>
                    {{ mission }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Church Activities -->
          <div class="luxury-card p-4 mb-4">
            <h3 class="section-title">Kegiatan Rutin</h3>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Kegiatan</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="activity in aboutStore.churchActivities" :key="activity.name">
                    <td class="fw-bold">{{ activity.name }}</td>
                    <td>
                      <span class="badge bg-primary">{{ activity.time }}</span>
                    </td>
                    <td><i class="bi bi-geo-alt me-2 text-gold"></i>{{ activity.location }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
          <!-- Contact Information -->
          <div class="luxury-card p-4 mb-4">
            <h4 class="section-title">Kontak Kami</h4>
            <div class="mb-3">
              <h6 class="text-gold"><i class="bi bi-geo-alt-fill me-2"></i>Alamat</h6>
              <p class="mb-0">{{ aboutStore.churchProfile.address }}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-gold"><i class="bi bi-telephone-fill me-2"></i>Telepon</h6>
              <p class="mb-0">{{ aboutStore.churchProfile.phone }}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-gold"><i class="bi bi-envelope-fill me-2"></i>Email</h6>
              <p class="mb-0">{{ aboutStore.churchProfile.email }}</p>
            </div>
            <div>
              <h6 class="text-gold"><i class="bi bi-globe me-2"></i>Website</h6>
              <p class="mb-0">{{ aboutStore.churchProfile.website }}</p>
            </div>
          </div>

          <!-- Map -->
          <div class="luxury-card p-4 mb-4">
            <h4 class="section-title">Lokasi</h4>
            <div class="ratio ratio-4x3">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.45311971954!2d106.83114900000001!3d-6.203802999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f411a3f894a3%3A0xa7aced109157a8f2!2sGereja%20Kristen%20Indonesia%20Menteng!5e0!3m2!1sen!2sid!4v1780473626896!5m2!1sen!2sid"
                style="border: 0; border-radius: 10px"
                allowfullscreen=""
                loading="lazy"
              ></iframe>
            </div>
          </div>

          <!-- Pastoral Team -->
          <div class="luxury-card p-4">
            <h4 class="section-title">Tim Pastoral</h4>
            <div
              v-for="pastor in aboutStore.pastoralTeam"
              :key="pastor.name"
              class="mb-3 pb-3 border-bottom"
            >
              <div class="text-center mb-2">
                <img
                  :src="pastor.image"
                  class="rounded-circle mb-2"
                  width="100"
                  height="100"
                  :alt="pastor.name"
                />
                <h6 class="mb-0 fw-bold">{{ pastor.name }}</h6>
                <small class="text-gold">{{ pastor.position }}</small>
                <p class="mb-1">
                  <small>{{ pastor.education }}</small>
                </p>
                <small class="text-muted">{{ pastor.email }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
 </div>
</template>

<style scoped>
.about-page .luxury-card {
  animation: fadeInUp 0.6s ease-out both;
}

.about-page .row:nth-child(1) .luxury-card {
  animation-delay: 0.1s;
}

.about-page .row:nth-child(2) .col-lg-8 .luxury-card {
  animation-delay: 0.2s;
}

.about-page .row:nth-child(2) .col-lg-4 .luxury-card {
  animation-delay: 0.3s;
}

.about-page .luxury-card:hover {
  transform: translateY(-8px);
}

.vision-mission-card {
  background: linear-gradient(135deg, #fff 0%, #fafbff 100%);
  border-radius: 20px;
}

.vision-mission-card i {
  transition: var(--transition-bounce);
}

.vision-mission-card:hover i {
  transform: scale(1.1) rotate(5deg);
  color: var(--accent-color) !important;
}

.table-dark {
  background: var(--gradient-primary) !important;
}

.table tbody tr {
  transition: var(--transition-smooth);
}

.table tbody tr:hover {
  background: rgba(212, 175, 55, 0.05);
  transform: translateX(5px);
}

.contact-item {
  transition: var(--transition-smooth);
}

.contact-item:hover {
  transform: translateX(8px);
}

.contact-item i {
  transition: var(--transition-smooth);
}

.contact-item:hover i {
  color: var(--dark-color) !important;
  transform: scale(1.2);
}

.ratio-4x3 iframe {
  border-radius: 15px;
  transition: var(--transition-smooth);
}

.ratio-4x3:hover iframe {
  box-shadow: var(--shadow-gold);
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
import { useAboutStore } from "../stores/about";
import ApiStatus from "../components/ApiStatus.vue";

export default {
  name: "AboutView",
  components: { ApiStatus },
  setup() {
    const aboutStore = useAboutStore();

    return {
      aboutStore,
    };
  },
};
</script>
