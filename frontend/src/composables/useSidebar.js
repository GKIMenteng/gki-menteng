import { ref, watch } from "vue";

const isOpen = ref(false);

export function useSidebar() {
  const open = () => {
    isOpen.value = true;
  };

  const close = () => {
    isOpen.value = false;
  };

  const toggle = () => {
    isOpen.value = !isOpen.value;
  };

  watch(isOpen, (open) => {
    if (typeof document === "undefined") return;
    document.body.style.overflow = open ? "hidden" : "";
  });

  return { isOpen, open, close, toggle };
}
