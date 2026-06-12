export const useBackendService = import.meta.env.VITE_DEVELOPMENT === 'true';
export const useFirebaseService = !useBackendService;

export function resolveApiBaseUrl() {
  if (!useBackendService) {
    return "";
  }

  const fromEnv = import.meta.env.VITE_API_BASE_URL;
  if (fromEnv === undefined || fromEnv === null || String(fromEnv).trim() === "") {
    return "";
  }

  return String(fromEnv).replace(/\/$/, "");
}
