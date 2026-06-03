<template>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-luxury">
    <div class="container-fluid">
      <button
        type="button"
        class="btn sidebar-toggle d-md-none"
        aria-label="Open navigation menu"
        aria-controls="sidebarMenu"
        :aria-expanded="isOpen"
        @click="toggle"
      >
        <i class="bi bi-list" aria-hidden="true"></i>
      </button>

      <router-link class="navbar-brand d-flex align-items-center" to="/">
        <i class="bi bi-building me-2 navbar-brand__icon" aria-hidden="true"></i>
        <div class="navbar-brand__text">
          <strong>GKI Menteng</strong>
          <small class="d-block text-gold">Gereja Kristen Indonesia</small>
        </div>
      </router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link nav-icon-link" href="#" title="Notifikasi">
              <i class="bi bi-bell"></i>
              <span class="nav-badge"></span>
            </a>
          </li>
          <li v-if="auth.isAuthenticated" class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle user-menu"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
            >
              <span class="user-avatar">
                <i class="bi bi-person-circle"></i>
              </span>
              <span class="user-name">{{ auth.user?.name || "Akun" }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end luxury-dropdown">
              <li class="dropdown-header small">
                {{ auth.user?.email }}
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button
                  type="button"
                  class="dropdown-item logout-item"
                  @click="handleLogout"
                >
                  <i class="bi bi-box-arrow-right me-2"></i>Keluar
                </button>
              </li>
            </ul>
          </li>
          <li v-else class="nav-item">
            <router-link class="nav-link login-link" to="/login">
              <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { useRouter } from "vue-router";
import { useSidebar } from "../composables/useSidebar";
import { useAuthStore } from "../stores/auth";

export default {
  name: "NavbarComponent",
  setup() {
    const { isOpen, toggle } = useSidebar();
    const auth = useAuthStore();
    const router = useRouter();

    const handleLogout = async () => {
      await auth.logout();
      await router.push({ name: "login" });
    };

    return { isOpen, toggle, auth, handleLogout };
  },
};
</script>
