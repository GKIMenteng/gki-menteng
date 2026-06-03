<template>
  <div v-if="error" class="alert alert-warning d-flex align-items-center justify-content-between mb-4" role="alert">
    <span><i class="bi bi-exclamation-triangle me-2"></i>{{ error }}</span>
    <button v-if="showRetry" type="button" class="btn btn-sm btn-outline-dark" @click="$emit('retry')">
      Coba lagi
    </button>
  </div>
  <div v-if="loading" class="text-center text-muted py-4 mb-4">
    <div class="spinner-border spinner-border-sm text-primary me-2" role="status" aria-hidden="true"></div>
    Memuat data dari server...
  </div>
</template>

<script>
export default {
  name: "ApiStatus",
  props: {
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
    showRetry: { type: Boolean, default: true },
  },
  emits: ["retry"],
};
</script>
