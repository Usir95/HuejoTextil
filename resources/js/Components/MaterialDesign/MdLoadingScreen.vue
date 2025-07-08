<script setup>
defineProps({
  show: { type: Boolean, default: false },
  text: { type: String, default: 'Guardando cambios...' },
})
</script>

<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center text-sm"
    >
      <div class="flex flex-col items-center gap-3">
        <div class="w-8 h-8 border-4 border-gray-800 border-t-transparent rounded-full animate-spin dark:border-white dark:border-t-transparent" />
        <span class="mt-1 text-gray-800 dark:text-white">{{ text }}</span>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
