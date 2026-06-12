import { resolveApiBaseUrl, useBackendService } from "./env";

const baseUrl = resolveApiBaseUrl();

let accessTokenGetter = () => null;
let refreshPromise = null;

export function setAccessTokenGetter(getter) {
  accessTokenGetter = getter;
}

function buildUrl(path) {
  const normalizedPath = path.startsWith("/") ? path : `/${path}`;
  return baseUrl ? `${baseUrl}${normalizedPath}` : normalizedPath;
}

function parsePayload(payload, response) {
  if (!response.ok || !payload.success) {
    const error = new Error(payload.message || `Permintaan gagal (${response.status})`);
    error.status = response.status;
    error.errors = payload.errors;
    throw error;
  }

  return payload.data;
}

async function tryRefreshSession() {
  if (refreshPromise) {
    return refreshPromise;
  }

  refreshPromise = (async () => {
    const url = buildUrl("/api/auth/refresh");
    const response = await fetch(url, {
      method: "POST",
      headers: { Accept: "application/json" },
      credentials: "include",
    });

    let payload;
    try {
      payload = await response.json();
    } catch {
      throw new Error("Respons server tidak valid (bukan JSON).");
    }

    const data = parsePayload(payload, response);

    const { useAuthStore } = await import("../stores/auth");
    useAuthStore().applyAuthPayload(data);

    return data;
  })();

  try {
    return await refreshPromise;
  } finally {
    refreshPromise = null;
  }
}

async function apiRequest(method, path, body, { retryOnUnauthorized = true } = {}) {
  const url = buildUrl(path);
  const headers = { Accept: "application/json" };

  const token = accessTokenGetter();
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const options = { method, headers, credentials: "include" };
  if (body !== undefined) {
    headers["Content-Type"] = "application/json";
    options.body = JSON.stringify(body);
  }

  if (!useBackendService) {
    throw new Error("API services are disabled when VITE_DEVELOPMENT=false. Use Firebase instead.");
  }

  let response;
  try {
    response = await fetch(url, options);
  } catch {
    throw new Error("Tidak dapat terhubung ke server API. Pastikan backend berjalan di port 8000.");
  }

  let payload;
  try {
    payload = await response.json();
  } catch {
    throw new Error("Respons server tidak valid (bukan JSON).");
  }

  if (response.status === 401 && retryOnUnauthorized && !path.startsWith("/api/auth/")) {
    try {
      await tryRefreshSession();
      return apiRequest(method, path, body, { retryOnUnauthorized: false });
    } catch {
      const { useAuthStore } = await import("../stores/auth");
      useAuthStore().clearSession();
    }
  }

  return parsePayload(payload, response);
}

export async function apiGet(path) {
  return apiRequest("GET", path);
}

export async function apiPost(path, body) {
  return apiRequest("POST", path, body);
}

export async function apiPut(path, body) {
  return apiRequest("PUT", path, body);
}

export async function apiDelete(path) {
  return apiRequest("DELETE", path);
}

export function getApiBaseUrl() {
  return baseUrl || window.location.origin;
}
