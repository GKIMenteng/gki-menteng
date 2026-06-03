<template>
  <Transition name="sidebar-backdrop">
    <div
      v-if="isOpen"
      class="sidebar-backdrop d-md-none"
      aria-hidden="true"
      @click="close"
    />
  </Transition>

  <aside
    id="sidebarMenu"
    class="sidebar"
    :class="{ 'sidebar--open': isOpen }"
    aria-label="Main navigation"
  >
    <div class="sidebar__top">
      <div class="sidebar__header">
        <div class="sidebar__brand">
          <span class="sidebar__brand-icon" aria-hidden="true">
            <i class="bi bi-grid-1x2-fill"></i>
          </span>
          <div>
            <span class="sidebar__title">Navigation</span>
            <span class="sidebar__subtitle">GKI Menteng</span>
          </div>
        </div>
        <button
          type="button"
          class="sidebar__close d-md-none"
          aria-label="Close menu"
          @click="close"
        >
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>
    </div>

    <div class="sidebar__body">
      <p class="sidebar-heading">Main Menu</p>
      <ul class="nav flex-column sidebar-nav">
        <li v-for="item in mainNav" :key="item.to" class="nav-item">
          <router-link
            :to="item.to"
            class="nav-link"
            :exact-active-class="item.exact ? 'active' : undefined"
            :active-class="item.exact ? undefined : 'active'"
          >
            <span class="nav-link__icon" aria-hidden="true">
              <i :class="['bi', item.icon]"></i>
            </span>
            <span class="nav-link__label">{{ item.label }}</span>
          </router-link>
        </li>
      </ul>

      <div class="sidebar__divider" role="separator"></div>

      <p class="sidebar-heading">Quick Links</p>
      <ul class="nav flex-column sidebar-nav sidebar-nav--secondary">
        <li v-for="item in quickLinks" :key="item.label" class="nav-item">
          <a class="nav-link" :href="item.href">
            <span class="nav-link__icon" aria-hidden="true">
              <i :class="['bi', item.icon]"></i>
            </span>
            <span class="nav-link__label">{{ item.label }}</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script>
import { watch, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import { useSidebar } from "../composables/useSidebar";

export default {
  name: "SidebarComponent",
  setup() {
    const route = useRoute();
    const { isOpen, close } = useSidebar();

    watch(
      () => route.path,
      () => close(),
    );

    const onKeydown = (event) => {
      if (event.key === "Escape") close();
    };

    onMounted(() => {
      document.addEventListener("keydown", onKeydown);
    });

    onUnmounted(() => {
      document.removeEventListener("keydown", onKeydown);
    });

    return { isOpen, close };
  },
  data() {
    return {
      mainNav: [
        { to: "/", label: "Dashboard", icon: "bi-speedometer2", exact: true },
        { to: "/calendar", label: "Calendar", icon: "bi-calendar3" },
        { to: "/volunteers", label: "Volunteers", icon: "bi-people-fill" },
        { to: "/about", label: "About", icon: "bi-info-circle-fill" },
      ],
      quickLinks: [
        { label: "Daily Bible", icon: "bi-book-half", href: "#" },
        { label: "Worship Songs", icon: "bi-music-note-beamed", href: "#" },
        { label: "Prayer Requests", icon: "bi-chat-dots", href: "#" },
      ],
    };
  },
};
</script>
