<template>
    <!-- ===================== Sidebar expandido ===================== -->
    <div v-if="!sidebarCollapsed" class="flex items-center justify-between pt-4 transition-all duration-300">
        <div class="flex items-center justify-center w-full">
            <img src="/Img/LogoLargo.png" alt="Logo completo" class="h-16 w-9/12 transition-all duration-300" />
        </div>
        <button
            v-if="!isMobile"
            @click="$emit('update-collapsed', true)"
            class="p-2 rounded-full text-[var(--color-primary-light)] hover:bg-[var(--color-primary-light)]/10 transition-colors duration-200"
        >
            <i class="fas fa-angle-left" />
        </button>
    </div>

    <!-- ===================== Sidebar colapsado ===================== -->
    <div v-else class="relative flex flex-col items-center py-4 space-y-3 transition-all duration-300">
        <img src="/Img/Logo.png" alt="Icono" class="h-8 object-contain transition-all duration-300" />
        <button
            @click="$emit('update-collapsed', false)"
            class="p-2 rounded-full text-[var(--color-primary-light)] hover:bg-[var(--color-primary-light)]/10 transition-colors duration-200"
        >
            <i class="fas fa-angle-right text-lg" />
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
