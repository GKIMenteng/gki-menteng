import { defineStore } from "pinia";
import {
  bindAuthStore,
  fetchCurrentUser,
  login as apiLogin,
  logout as apiLogout,
  refreshSession,
  register as apiRegister,
} from "../services/auth";

const ACCESS_TOKEN_KEY = "gki_access_token";
const USER_KEY = "gki_user";

function loadStoredUser() {
  try {
    const raw = sessionStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function loadStoredToken() {
  return sessionStorage.getItem(ACCESS_TOKEN_KEY);
}

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: loadStoredUser(),
    accessToken: loadStoredToken(),
    initialized: false,
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.accessToken && state.user),
  },

  actions: {
    init() {
      bindAuthStore(() => this.accessToken);
    },

    persist() {
      if (this.accessToken) {
        sessionStorage.setItem(ACCESS_TOKEN_KEY, this.accessToken);
      } else {
        sessionStorage.removeItem(ACCESS_TOKEN_KEY);
      }

      if (this.user) {
        sessionStorage.setItem(USER_KEY, JSON.stringify(this.user));
      } else {
        sessionStorage.removeItem(USER_KEY);
      }
    },

    applyAuthPayload(data) {
      this.user = data.user;
      this.accessToken = data.tokens.accessToken;
      this.persist();
    },

    clearSession() {
      this.user = null;
      this.accessToken = null;
      this.persist();
    },

    async bootstrap() {
      this.init();

      if (!this.accessToken) {
        this.initialized = true;
        return;
      }

      try {
        const data = await fetchCurrentUser();
        this.user = data.user;
        this.persist();
      } catch {
        try {
          const data = await refreshSession();
          this.applyAuthPayload(data);
        } catch {
          this.clearSession();
        }
      } finally {
        this.initialized = true;
      }
    },

    async register(form) {
      this.loading = true;
      try {
        const data = await apiRegister({
          name: form.name,
          email: form.email,
          password: form.password,
          passwordConfirmation: form.passwordConfirmation,
        });
        this.applyAuthPayload(data);
        return data;
      } finally {
        this.loading = false;
      }
    },

    async login(form) {
      this.loading = true;
      try {
        const data = await apiLogin({
          email: form.email,
          password: form.password,
        });
        this.applyAuthPayload(data);
        return data;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      try {
        await apiLogout();
      } catch {
        // Clear local session even if API call fails.
      } finally {
        this.clearSession();
      }
    },

    async refresh() {
      const data = await refreshSession();
      this.applyAuthPayload(data);
      return data;
    },
  },
});
