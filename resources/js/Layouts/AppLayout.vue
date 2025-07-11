<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

import Sidebar from '@/Components/Layout/Sidebar.vue'

defineProps({ title: String })

const tema = ref('light')
const isMobile = ref(false)
const mobileOpen = ref(false)
const sidebarCollapsed = ref(false)
const page = usePage()

const categoriasModulos = ref(page.props.PermisosModulos || [])

const checkMobile = () => {
  isMobile.value = window.innerWidth <= 768
}

const toggleMobileSidebar = () => {
  mobileOpen.value = !mobileOpen.value
}

onMounted(() => {
  tema.value = 'light'
  localStorage.setItem('tema', 'light')
  document.documentElement.classList.remove('dark')

  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Head :title="title" />

    <!-- Sidebar -->
    <Sidebar
      :isMobile="isMobile"
      :mobileOpen="mobileOpen"
      :sidebarCollapsed="sidebarCollapsed"
      :CategoriasModulos="categoriasModulos"
      @close-mobile="mobileOpen = false"
      @update-collapsed="sidebarCollapsed = $event"
    />

    <!-- Contenido principal -->
    <div
      class="flex flex-col flex-1 transition-all duration-300"
      :class="{
        'ml-56': !isMobile && !sidebarCollapsed,
        'ml-14': !isMobile && sidebarCollapsed
      }"
    >
      <!-- Header opcional -->
      <header
        v-if="$slots.header || $slots['header-left'] || $slots['header-right']"
        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 mb-4"
      >
        <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="flex flex-wrap items-center gap-2">
            <slot name="header-left" />
          </div>
          <div class="flex items-center">
            <slot name="header-right" />
          </div>
        </div>
      </header>

      <!-- Página -->
      <main class="px-4 py-6 sm:px-6 lg:px-8 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
