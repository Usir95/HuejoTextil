<template>
  <div :data-submenu="title" class="overflow-hidden text-sm font-medium border-b border-white/10 dark:border-white/20">
    <div
      @click="toggle"
      class="flex items-center px-2 py-3 cursor-pointer transition-colors hover:bg-[var(--color-primary-light)] hover:text-white"
    >
      <!-- Icono -->
      <div class="flex justify-center w-10 text-[var(--color-complement-1)]">
        <i :class="icon" class="text-lg" />
      </div>

      <!-- Título -->
      <div
        class="flex-1 overflow-hidden whitespace-nowrap text-ellipsis transition-all duration-200 max-w-[140px]"
        :class="{ hidden: collapsed }"
      >
        {{ title }}
      </div>

      <!-- Flecha -->
      <div
        class="flex justify-center w-4 text-xs text-white/80"
        :class="{ hidden: collapsed }"
      >
        <i :class="isOpen ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" />
      </div>
    </div>

    <!-- Submenú desplegable -->
    <transition name="slide-fade">
      <div v-show="isOpen && !collapsed" class="py-1 px-4 space-y-1 text-sm overflow-y-auto max-h-60">
        <slot />
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, inject, watch, onMounted } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  icon: { type: String, required: true },
  collapsed: { type: Boolean, required: true }
})

const isOpen = ref(false)
const activeSubmenu = inject('activeSubmenu')

const toggle = () => {
  if (activeSubmenu.value !== props.title) {
    activeSubmenu.value = props.title
    isOpen.value = true
  } else {
    isOpen.value = !isOpen.value
    if (!isOpen.value) activeSubmenu.value = null
  }
  localStorage.setItem('submenu-open', activeSubmenu.value || '')
}

onMounted(() => {
  const saved = localStorage.getItem('submenu-open')
  if (saved === props.title) {
    isOpen.value = true
    activeSubmenu.value = props.title
  }
})

watch(activeSubmenu, (val) => {
  if (val !== props.title) isOpen.value = false
})
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-4px);
  opacity: 0;
}
</style>
