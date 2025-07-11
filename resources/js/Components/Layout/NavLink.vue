<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: String,
  active: Boolean,
  collapsed: Boolean
})

const classes = computed(() =>
  props.active
    ? 'relative flex items-center w-full py-2 px-4 text-sm font-semibold text-white bg-[var(--color-primary)]'
    : 'flex items-center w-full py-2 px-4 text-sm text-white/90 hover:bg-[var(--color-primary-light)] transition'
)
</script>

<template>
  <Link :href="href" :class="classes">
    <!-- Icono -->
    <div class="w-5 min-w-5 flex justify-center items-center">
      <slot name="icon" />
    </div>

    <!-- Título -->
    <span
      v-if="!collapsed"
      class="ml-3 truncate max-w-[150px]"
    >
      <slot />
    </span>

    <!-- Triángulo decorativo opcional -->
    <div
      v-if="active && !collapsed"
      class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 rotate-45 bg-white rounded-sm"
    />
  </Link>
</template>
