<template>
    <!-- Sidebar expandido -->
    <div v-if="!sidebarCollapsed" class="flex items-center justify-between pt-4">
        <div class="flex items-center justify-center w-full">
            <img src="/Img/LogoLargo.png" alt="Logo completo" class="h-16 w-9/12" />
        </div>
        <button
            v-if="!isMobile"
            @click="$emit('update-collapsed', true)"
            class="text-[var(--color-primary-light)] font-extrabold hover:text-[var(--color-primary-light)] cursor-pointer transition"
        >
            <i class="fas fa-angle-left" />
        </button>
    </div>

    <!-- Sidebar colapsado -->
    <div v-else class="relative flex flex-col items-center py-4 space-y-3">
        <img src="/Img/Logo.png" alt="Icono" class="h-8" />
        <button
            @click="$emit('update-collapsed', false)"
            class="[var(--color-primary-light)] cursor-pointer font-extrabold hover:text-[var(--color-primary-light)] transition"
        >
            <i class="fas fa-angle-right" />
        </button>
    </div>
</template>

<script setup>
    defineProps({
        isMobile: Boolean,
        sidebarCollapsed: Boolean,
    });

    defineEmits(['update-collapsed']);
</script>
