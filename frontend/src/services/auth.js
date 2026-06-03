import { apiPost, apiGet, setAccessTokenGetter } from "./api";

export function register(payload) {
  return apiPost("/api/auth/register", payload);
}

export function login(payload) {
  return apiPost("/api/auth/login", payload);
}

export function refreshSession() {
  return apiPost("/api/auth/refresh");
}

export function logout() {
  return apiPost("/api/auth/logout");
}

export function fetchCurrentUser() {
  return apiGet("/api/auth/me");
}

export function bindAuthStore(getter) {
  setAccessTokenGetter(getter);
}
