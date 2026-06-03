<template>
  <div class="auth-page">
    <div class="auth-card luxury-card">
      <div class="auth-card__header text-center">
        <i class="bi bi-person-plus auth-card__logo" aria-hidden="true"></i>
        <h1 class="auth-card__title">Daftar Akun</h1>
        <p class="auth-card__subtitle text-muted">
          Buat akun untuk mengakses portal GKI Menteng
        </p>
      </div>

      <form class="auth-form" novalidate @submit.prevent="handleSubmit">
        <div v-if="formError" class="alert alert-danger" role="alert">
          {{ formError }}
        </div>

        <div class="mb-3">
          <label for="register-name" class="form-label">Nama lengkap</label>
          <input
            id="register-name"
            v-model.trim="form.name"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': fieldErrors.name }"
            autocomplete="name"
            required
          />
          <div v-if="fieldErrors.name" class="invalid-feedback">
            {{ fieldErrors.name }}
          </div>
        </div>

        <div class="mb-3">
          <label for="register-email" class="form-label">Email</label>
          <input
            id="register-email"
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
          <label for="register-password" class="form-label">Kata sandi</label>
          <input
            id="register-password"
            v-model="form.password"
            type="password"
            class="form-control"
            :class="{ 'is-invalid': fieldErrors.password }"
            autocomplete="new-password"
            required
          />
          <div class="form-text">
            Minimal 8 karakter, mengandung huruf dan angka.
          </div>
          <div v-if="fieldErrors.password" class="invalid-feedback">
            {{ fieldErrors.password }}
          </div>
        </div>

        <div class="mb-3">
          <label for="register-password-confirm" class="form-label">
            Konfirmasi kata sandi
          </label>
          <input
            id="register-password-confirm"
            v-model="form.passwordConfirmation"
            type="password"
            class="form-control"
            :class="{ 'is-invalid': fieldErrors.passwordConfirmation }"
            autocomplete="new-password"
            required
          />
          <div v-if="fieldErrors.passwordConfirmation" class="invalid-feedback">
            {{ fieldErrors.passwordConfirmation }}
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
          {{ auth.loading ? "Memproses..." : "Daftar" }}
        </button>
      </form>

      <p class="auth-card__footer text-center mt-4 mb-0">
        Sudah punya akun?
        <router-link to="/login">Masuk</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

export default {
  name: "RegisterView",
  setup() {
    const auth = useAuthStore();
    const router = useRouter();

    const form = reactive({
      name: "",
      email: "",
      password: "",
      passwordConfirmation: "",
    });
    const fieldErrors = reactive({});
    const formError = ref("");

    const handleSubmit = async () => {
      formError.value = "";
      Object.keys(fieldErrors).forEach((key) => delete fieldErrors[key]);

      try {
        await auth.register(form);
        await router.replace("/");
      } catch (error) {
        if (error.errors) {
          Object.assign(fieldErrors, error.errors);
        }
        formError.value = error.message || "Pendaftaran gagal.";
      }
    };

    return {
      auth,
      form,
      fieldErrors,
      formError,
      handleSubmit,
    };
  },
};
</script>
