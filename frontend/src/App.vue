<template>
  <div id="app" :class="isGuestRoute ? 'app-shell app-shell--guest' : 'app-shell'">
    <header v-if="!isGuestRoute" ref="headerRef" class="app-header">
      <NavbarComponent />
    </header>

    <SidebarComponent v-if="!isGuestRoute" />

    <div :class="isGuestRoute ? 'app-body app-body--guest' : 'app-body'">
      <main :class="isGuestRoute ? 'main-content main-content--guest' : 'main-content'">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
      <FooterComponent v-if="!isGuestRoute" />
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import NavbarComponent from "./components/NavbarComponent.vue";
import SidebarComponent from "./components/SidebarComponent.vue";
import FooterComponent from "./components/FooterComponent.vue";

export default {
  name: "App",
  components: {
    NavbarComponent,
    SidebarComponent,
    FooterComponent,
  },
  setup() {
    const route = useRoute();
    const isGuestRoute = computed(() => Boolean(route.meta.guest));

    const headerRef = ref(null);
    let resizeObserver;

    const syncHeaderHeight = () => {
      const height = headerRef.value?.offsetHeight ?? 64;
      document.documentElement.style.setProperty(
        "--header-height",
        `${height}px`,
      );
    };

    onMounted(() => {
      syncHeaderHeight();
      resizeObserver = new ResizeObserver(syncHeaderHeight);
      if (headerRef.value) resizeObserver.observe(headerRef.value);
      window.addEventListener("resize", syncHeaderHeight);
    });

    onUnmounted(() => {
      resizeObserver?.disconnect();
      window.removeEventListener("resize", syncHeaderHeight);
    });

    return { headerRef, isGuestRoute };
  },
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-active {
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
