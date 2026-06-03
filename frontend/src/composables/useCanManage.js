import { computed } from "vue";
import { useAuthStore } from "../stores/auth";

/** True when the user is logged in and may create, edit, or delete content. */
export function useCanManage() {
  const auth = useAuthStore();
  const canManage = computed(() => auth.isAuthenticated);

  return { auth, canManage };
}
