<template>

  <div class="volunteers-page">

    <ApiStatus

      :loading="volunteersStore.loading"

      :error="volunteersStore.error"

      @retry="volunteersStore.fetchVolunteers"

    />

    <div :class="{ 'opacity-50 pe-none': volunteersStore.loading }">

    <div class="row mb-4">

      <div class="col-12">

        <h2 class="section-title">Jadwal Pelayan Minggu</h2>

      </div>

    </div>



    <!-- Sunday Service Schedule -->

    <div class="row mb-4">

      <div class="col-12">

        <div class="luxury-card p-4">

          <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 v-if="volunteersStore.sundayServiceSchedule.date">

              Ibadah Minggu - {{ formatDate(volunteersStore.sundayServiceSchedule.date) }}

            </h4>

            <h4 v-else>Ibadah Minggu</h4>

            <span class="event-badge">Minggu Ini</span>

          </div>



          <div class="row">

            <div

              v-for="(service, index) in volunteersStore.sundayServiceSchedule.services"

              :key="index"

              class="col-md-6 mb-4"

            >

              <div class="card volunteer-card h-100">

                <div class="card-header gradient-primary text-white">

                  <h5 class="mb-0">Kebaktian {{ service.time }}</h5>

                </div>

                <div class="card-body">

                  <div class="mb-3">

                    <h6 class="text-gold mb-2"><i class="bi bi-mic-fill me-2"></i>Pengkhotbah</h6>

                    <p class="mb-0 fw-bold">{{ service.preacher }}</p>

                  </div>



                  <div class="mb-3">

                    <h6 class="text-gold mb-2">

                      <i class="bi bi-music-note-beamed me-2"></i>Pemimpin Pujian

                    </h6>

                    <p class="mb-0 fw-bold">{{ service.worshipLeader }}</p>

                  </div>



                  <div class="mb-3">

                    <h6 class="text-gold mb-2"><i class="bi bi-people me-2"></i>Pemain Musik</h6>

                    <div v-if="service.musicians[0] !== 'TBD'">

                      <span

                        v-for="musician in service.musicians"

                        :key="musician"

                        class="badge bg-secondary me-1 mb-1"

                      >

                        {{ musician }}

                      </span>

                    </div>

                    <p v-else class="text-muted mb-0">TBD</p>

                  </div>



                  <div class="mb-3">

                    <h6 class="text-gold mb-2">

                      <i class="bi bi-hand-thumbs-up me-2"></i>Tim Hospitality

                    </h6>

                    <div v-if="service.hospitality[0] !== 'TBD'">

                      <span

                        v-for="person in service.hospitality"

                        :key="person"

                        class="badge bg-secondary me-1 mb-1"

                      >

                        {{ person }}

                      </span>

                    </div>

                    <p v-else class="text-muted mb-0">TBD</p>

                  </div>



                  <div class="mb-3">

                    <h6 class="text-gold mb-2"><i class="bi bi-laptop me-2"></i>Multimedia</h6>

                    <p class="mb-0 fw-bold">{{ service.multimedia }}</p>

                  </div>



                  <div>

                    <h6 class="text-gold mb-2"><i class="bi bi-book me-2"></i>Sekolah Minggu</h6>

                    <div v-if="service.sundaySchool[0] !== 'TBD'">

                      <span

                        v-for="teacher in service.sundaySchool"

                        :key="teacher"

                        class="badge bg-secondary me-1 mb-1"

                      >

                        {{ teacher }}

                      </span>

                    </div>

                    <p v-else class="text-muted mb-0">TBD</p>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>



    <!-- Volunteers List -->

    <div class="row mb-4">

      <div class="col-12">

        <div class="luxury-card p-4">

          <div v-if="!canManage" class="alert alert-info py-2 mb-3" role="status">
            Mode tamu — hanya dapat melihat daftar volunteer.
            <router-link to="/login" class="alert-link">Masuk</router-link>
            untuk menambah, mengubah, atau menghapus.
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="mb-0">Daftar Volunteer</h4>

            <div v-if="canManage">

              <button class="btn btn-luxury" @click="openCreateModal">

                <i class="bi bi-plus-lg me-1"></i>Tambah Volunteer

              </button>

            </div>

          </div>



          <div class="table-responsive">

            <table class="table table-hover">

              <thead class="table-dark">

                <tr>

                  <th>Foto</th>

                  <th>Nama</th>

                  <th>Peran</th>

                  <th>Pelayanan</th>

                  <th>Kontak</th>

                  <th v-if="canManage">Aksi</th>

                </tr>

              </thead>

              <tbody>

                <tr v-for="volunteer in volunteersStore.volunteers" :key="volunteer.id">

                  <td>

                    <img

                      :src="volunteer.image || defaultAvatar"

                      class="rounded-circle"

                      width="40"

                      height="40"

                      :alt="volunteer.name"

                    />

                  </td>

                  <td class="fw-bold">{{ volunteer.name }}</td>

                  <td>{{ volunteer.role }}</td>

                  <td>

                    <span class="badge bg-primary">{{ volunteer.ministry }}</span>

                  </td>

                  <td>

                    <small>

                      <i class="bi bi-telephone me-1"></i>{{ volunteer.phone || "-" }}<br />

                      <i class="bi bi-envelope me-1"></i>{{ volunteer.email || "-" }}

                    </small>

                  </td>

                  <td v-if="canManage">

                    <button

                      class="btn btn-sm btn-outline-primary me-1"

                      title="Edit"

                      @click="openEditModal(volunteer)"

                    >

                      <i class="bi bi-pencil"></i>

                    </button>

                    <button

                      class="btn btn-sm btn-outline-danger"

                      title="Hapus"

                      :disabled="volunteersStore.saving"

                      @click="confirmDelete(volunteer)"

                    >

                      <i class="bi bi-trash"></i>

                    </button>

                  </td>

                </tr>

              </tbody>

            </table>

          </div>

        </div>

      </div>

    </div>



    <!-- Ministries Overview -->

    <div class="row">

      <div class="col-12">

        <div class="luxury-card p-4">

          <h4 class="section-title">Pelayanan</h4>

          <div class="row">

            <div

              v-for="ministry in volunteersStore.ministries"

              :key="ministry.name"

              class="col-md-4 col-sm-6 mb-3"

            >

              <div class="p-3 border rounded text-center">

                <i class="bi bi-building display-6 text-gold mb-2"></i>

                <h5 class="fw-bold">{{ ministry.name }}</h5>

                <p class="mb-0 text-muted">{{ ministry.count }} Volunteer</p>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    </div>



    <!-- Volunteer modal -->

    <div

      class="modal fade"

      :class="{ show: showVolunteerModal }"

      :style="{ display: showVolunteerModal ? 'block' : 'none' }"

      tabindex="-1"

      role="dialog"

      aria-modal="true"

      @click.self="closeVolunteerModal"

    >

      <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

          <form @submit.prevent="submitVolunteerForm">

            <div class="modal-header">

              <h5 class="modal-title">

                {{ editingVolunteer ? "Edit Volunteer" : "Tambah Volunteer Baru" }}

              </h5>

              <button type="button" class="btn-close" @click="closeVolunteerModal"></button>

            </div>

            <div class="modal-body">

              <div v-if="formError" class="alert alert-danger py-2">{{ formError }}</div>



              <div class="mb-3">

                <label class="form-label">Nama <span class="text-danger">*</span></label>

                <input v-model="volunteerForm.name" type="text" class="form-control" required />

              </div>



              <div class="mb-3">

                <label class="form-label">Peran <span class="text-danger">*</span></label>

                <input v-model="volunteerForm.role" type="text" class="form-control" required />

              </div>



              <div class="mb-3">

                <label class="form-label">Pelayanan <span class="text-danger">*</span></label>

                <select v-model="volunteerForm.ministry" class="form-select" required>

                  <option value="">Pilih pelayanan</option>

                  <option

                    v-for="ministry in volunteersStore.ministries"

                    :key="ministry.name"

                    :value="ministry.name"

                  >

                    {{ ministry.name }}

                  </option>

                </select>

              </div>



              <div class="row mb-3">

                <div class="col-6">

                  <label class="form-label">Telepon</label>

                  <input v-model="volunteerForm.phone" type="tel" class="form-control" />

                </div>

                <div class="col-6">

                  <label class="form-label">Email</label>

                  <input v-model="volunteerForm.email" type="email" class="form-control" />

                </div>

              </div>



              <div class="mb-3">

                <label class="form-label">Pengalaman</label>

                <input

                  v-model="volunteerForm.experience"

                  type="text"

                  class="form-control"

                  placeholder="Contoh: 3 tahun"

                />

              </div>



              <div class="mb-3">

                <label class="form-label">URL Foto</label>

                <input

                  v-model="volunteerForm.image"

                  type="url"

                  class="form-control"

                  placeholder="https://..."

                />

              </div>

            </div>

            <div class="modal-footer">

              <button type="button" class="btn btn-outline-secondary" @click="closeVolunteerModal">

                Batal

              </button>

              <button type="submit" class="btn btn-primary" :disabled="volunteersStore.saving">

                <span

                  v-if="volunteersStore.saving"

                  class="spinner-border spinner-border-sm me-1"

                ></span>

                {{ editingVolunteer ? "Simpan Perubahan" : "Tambah Volunteer" }}

              </button>

            </div>

          </form>

        </div>

      </div>

    </div>

    <div

      v-if="showVolunteerModal"

      class="modal-backdrop fade show"

      @click="closeVolunteerModal"

    ></div>

  </div>

</template>



<script>

import { useVolunteersStore } from "../stores/volunteers";
import { useCanManage } from "../composables/useCanManage";
import ApiStatus from "../components/ApiStatus.vue";
import { ref } from "vue";



const DEFAULT_AVATAR = "https://i.pravatar.cc/150?img=68";



function emptyVolunteerForm() {

  return {

    name: "",

    role: "",

    ministry: "",

    phone: "",

    email: "",

    experience: "",

    image: "",

  };

}



export default {

  name: "VolunteersView",

  components: { ApiStatus },

  setup() {

    const volunteersStore = useVolunteersStore();
    const { canManage } = useCanManage();

    const showVolunteerModal = ref(false);

    const editingVolunteer = ref(null);

    const formError = ref(null);

    const volunteerForm = ref(emptyVolunteerForm());



    const formatDate = (date) => {

      const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };

      return new Date(date).toLocaleDateString("id-ID", options);

    };



    const openCreateModal = () => {

      editingVolunteer.value = null;

      formError.value = null;

      volunteerForm.value = emptyVolunteerForm();

      showVolunteerModal.value = true;

    };



    const openEditModal = (volunteer) => {

      editingVolunteer.value = volunteer;

      formError.value = null;

      volunteerForm.value = {

        name: volunteer.name,

        role: volunteer.role,

        ministry: volunteer.ministry,

        phone: volunteer.phone || "",

        email: volunteer.email || "",

        experience: volunteer.experience || "",

        image: volunteer.image || "",

      };

      showVolunteerModal.value = true;

    };



    const closeVolunteerModal = () => {

      showVolunteerModal.value = false;

      editingVolunteer.value = null;

      formError.value = null;

    };



    const submitVolunteerForm = async () => {

      formError.value = null;



      try {

        if (editingVolunteer.value) {

          await volunteersStore.updateVolunteer(editingVolunteer.value.id, {

            ...volunteerForm.value,

          });

        } else {

          await volunteersStore.createVolunteer({ ...volunteerForm.value });

        }

        closeVolunteerModal();

      } catch (err) {

        formError.value = err.message || "Gagal menyimpan volunteer";

      }

    };



    const confirmDelete = async (volunteer) => {

      if (!window.confirm(`Hapus volunteer "${volunteer.name}"?`)) {

        return;

      }



      try {

        await volunteersStore.deleteVolunteer(volunteer.id);

      } catch {

        // error shown via store

      }

    };



    return {

      volunteersStore,

      canManage,

      formatDate,

      defaultAvatar: DEFAULT_AVATAR,

      showVolunteerModal,

      editingVolunteer,

      formError,

      volunteerForm,

      openCreateModal,

      openEditModal,

      closeVolunteerModal,

      submitVolunteerForm,

      confirmDelete,

    };

  },

};

</script>



<style scoped>
.modal.show {
  background: rgba(0, 0, 0, 0.35);
}

.volunteer-card {
  border-radius: 18px;
  border-left: 4px solid var(--accent-color);
  transition: var(--transition-smooth);
}

.volunteer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(26, 35, 126, 0.15);
}

.volunteer-card .card-header {
  border-radius: 18px 18px 0 0;
  padding: 1.25rem 1rem;
}

.volunteer-card .badge {
  font-weight: 500;
  transition: var(--transition-smooth);
}

.volunteer-card .badge:hover {
  transform: scale(1.05);
  box-shadow: 0 0 10px rgba(212, 175, 55, 0.4);
}

.table img.rounded-circle {
  border: 2px solid var(--accent-color);
  transition: var(--transition-smooth);
}

.table tr:hover img.rounded-circle {
  transform: scale(1.1) rotate(5deg);
  border-color: var(--primary-color);
}

.ministry-card {
  border-radius: 18px;
  transition: var(--transition-bounce);
  border: 1px solid rgba(26, 35, 126, 0.05);
}

.ministry-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 20px 40px rgba(26, 35, 126, 0.18);
}

.ministry-card i {
  transition: var(--transition-bounce);
}

.ministry-card:hover i {
  transform: scale(1.15) rotate(10deg);
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

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.volunteers-page .row > div {
  animation: fadeInUp 0.6s ease-out both;
}

.volunteers-page .row > div:nth-child(1) { animation-delay: 0.1s; }
.volunteers-page .row > div:nth-child(2) { animation-delay: 0.2s; }
.volunteers-page .row > div:nth-child(3) { animation-delay: 0.3s; }
</style>

