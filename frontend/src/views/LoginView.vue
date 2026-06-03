<template>
  <div class="auth-page">
    <div class="auth-card luxury-card">
      <div class="auth-card__header text-center">
        <i class="bi bi-building auth-card__logo" aria-hidden="true"></i>
        <h1 class="auth-card__title">Masuk</h1>
        <p class="auth-card__subtitle text-muted">
          Portal administrasi GKI Menteng
        </p>
      </div>

      <form class="auth-form" novalidate @submit.prevent="handleSubmit">
        <div v-if="formError" class="alert alert-danger" role="alert">
          {{ formError }}
        </div>

        <div class="mb-3">
          <label for="login-email" class="form-label">Email</label>
          <input
            id="login-email"
            v-model.trim="form.email"
            type="email"
            class="form-control"
            :class="{ 'is-invalid': fieldErrors.email }"
            autocomplete="email"
            required
          />
          <div v-if="fieldErrors.email" class="invalid-feedback">
            {{ fieldErrors.email }}
          </div>
        </div>

        <div class="mb-3">
          <label for="login-password" class="form-label">Kata sandi</label>
          <div class="input-group">
            <input
              id="login-password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-control"
              :class="{ 'is-invalid': fieldErrors.password }"
              autocomplete="current-password"
              required
            />
            <button
              type="button"
              class="btn btn-outline-secondary"
              :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
              @click="showPassword = !showPassword"
            >
              <i
                :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"
                aria-hidden="true"
              ></i>
            </button>
          </div>
          <div v-if="fieldErrors.password" class="invalid-feedback d-block">
            {{ fieldErrors.password }}
          </div>
        </div>

        <button
          type="submit"
          class="btn btn-luxury w-100"
          :disabled="auth.loading"
        >
          <span
            v-if="auth.loading"
            class="spinner-border spinner-border-sm me-2"
            role="status"
            aria-hidden="true"
          ></span>
          {{ auth.loading ? "Memproses..." : "Masuk" }}
        </button>
      </form>

      <p class="auth-card__footer text-center mt-4 mb-0">
        Belum punya akun?
        <router-link to="/register">Daftar sekarang</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import { reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

export default {
  name: "LoginView",
  setup() {
    const auth = useAuthStore();
    const router = useRouter();
    const route = useRoute();

    const form = reactive({
      email: "",
      password: "",
    });
    const fieldErrors = reactive({});
    const formError = ref("");
    const showPassword = ref(false);

    const handleSubmit = async () => {
      formError.value = "";
      Object.keys(fieldErrors).forEach((key) => delete fieldErrors[key]);

      try {
        await auth.login(form);
        const redirect = route.query.redirect || "/";
        await router.replace(String(redirect));
      } catch (error) {
        if (error.errors) {
          Object.assign(fieldErrors, error.errors);
        }
        formError.value = error.message || "Login gagal.";
      }
    };

    return {
      auth,
      form,
      fieldErrors,
      formError,
      showPassword,
      handleSubmit,
    };
  },
};
</script>
