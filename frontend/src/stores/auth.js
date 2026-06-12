import { defineStore } from "pinia";
import {
  bindAuthStore,
  fetchCurrentUser,
  login as apiLogin,
  logout as apiLogout,
  refreshSession,
  register as apiRegister,
} from "../services/auth";
import { useBackendService } from "../services/env";
import { db, auth } from "../services/firebase";
import { doc, setDoc } from "firebase/firestore";
import { onAuthStateChanged, signInWithEmailAndPassword, createUserWithEmailAndPassword, signOut } from "firebase/auth";

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
      this.syncUserProfile();
    },

    clearSession() {
      this.user = null;
      this.accessToken = null;
      this.persist();
    },

    waitForFirebaseAuthState() {
      return new Promise((resolve) => {
        const unsubscribe = onAuthStateChanged(auth, (firebaseUser) => {
          unsubscribe();
          resolve(firebaseUser);
        });
      });
    },

    async bootstrap() {
      this.init();

      if (!this.accessToken) {
        this.initialized = true;
        return;
      }

      if (useBackendService) {
        try {
          const data = await fetchCurrentUser();
          this.user = data.user;
          this.persist();
          this.syncUserProfile();
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
      } else {
        // Production: using Firebase Auth
        try {
          const firebaseUser = await this.waitForFirebaseAuthState();
          if (!firebaseUser) {
            throw new Error("User not signed in");
          }

          const idToken = await firebaseUser.getIdToken(true);
          this.accessToken = idToken;
          this.user = {
            id: firebaseUser.uid,
            email: firebaseUser.email,
            name: firebaseUser.displayName || firebaseUser.email,
          };
          this.persist();
          this.syncUserProfile();
        } catch (error) {
          console.error("Firebase auth error in bootstrap:", error);
          this.clearSession();
        } finally {
          this.initialized = true;
        }
      }
    },

    async register(form) {
      this.loading = true;

      try {
        if (useBackendService) {
          const data = await apiRegister({
            name: form.name,
            email: form.email,
            password: form.password,
            passwordConfirmation: form.passwordConfirmation,
          });
          this.applyAuthPayload(data);
          return data;
        } else {
          const userCredential = await createUserWithEmailAndPassword(
            auth,
            form.email,
            form.password
          );
          const firebaseUser = userCredential.user;
          const idToken = await firebaseUser.getIdToken();
          this.accessToken = idToken;
          this.user = {
            id: firebaseUser.uid,
            email: firebaseUser.email,
            name: firebaseUser.displayName || firebaseUser.email,
          };
          this.persist();
          this.syncUserProfile();
          return { user: this.user, tokens: { accessToken: this.accessToken } };
        }
      } finally {
        this.loading = false;
      }
    },

    async login(form) {
      this.loading = true;

      try {
        if (useBackendService) {
          const data = await apiLogin({
            email: form.email,
            password: form.password,
          });
          this.applyAuthPayload(data);
          return data;
        } else {
          const userCredential = await signInWithEmailAndPassword(
            auth,
            form.email,
            form.password
          );
          const firebaseUser = userCredential.user;
          const idToken = await firebaseUser.getIdToken();
          this.accessToken = idToken;
          this.user = {
            id: firebaseUser.uid,
            email: firebaseUser.email,
            name: firebaseUser.displayName || firebaseUser.email,
          };
          this.persist();
          this.syncUserProfile();
          return { user: this.user, tokens: { accessToken: this.accessToken } };
        }
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      try {
        if (useBackendService) {
          await apiLogout();
        } else {
          await signOut(auth);
        }
      } catch {
        // Clear local session even if the call fails.
      } finally {
        this.clearSession();
      }
    },

    async refresh() {
      if (useBackendService) {
        const data = await refreshSession();
        this.applyAuthPayload(data);
        return data;
      } else {
        try {
          const firebaseUser = auth.currentUser;
          if (!firebaseUser) {
            throw new Error('User not signed in');
          }
          const idToken = await firebaseUser.getIdToken(true);
          this.accessToken = idToken;
          this.user = {
            id: firebaseUser.uid,
            email: firebaseUser.email,
            name: firebaseUser.displayName || firebaseUser.email,
          };
          this.persist();
          this.syncUserProfile();
          return { user: this.user, tokens: { accessToken: this.accessToken } };
        } catch (error) {
          this.clearSession();
          throw error;
        }
      }
    },

    syncUserProfile() {
      if (!useBackendService && this.user) {
        // Store the user profile in Firestore under users collection with user ID as document ID
        // We assume the user object has an id property
        if (this.user.id) {
          const userRef = doc(db, 'users', this.user.id);
          // We don't want to overwrite the entire document every time, but we can merge.
          // We'll set the fields we want to sync.
          setDoc(userRef, {
            name: this.user.name,
            email: this.user.email,
            // Add other fields as needed
            updatedAt: new Date(),
          }, { merge: true });
        }
      }
    },
  },
});