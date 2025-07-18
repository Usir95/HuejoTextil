<template>
    <div
        :class="[
            'fixed top-0 left-0 right-0 z-10 flex items-center justify-between px-4 h-14 shadow-md transition-all duration-500 ease-in-out motion-safe:animate-fade-in-down',
            'bg-[var(--color-topbar)] text-[var(--color-topbar-text)]',
            !isMobile ? (sidebarCollapsed ? 'ml-14' : 'ml-56') : '',
        ]"
    >
        <!-- Botón hamburguesa en móvil -->
        <button
            @click="$emit('toggle-mobile-sidebar')"
            class="md:hidden focus:outline-none group"
        >
            <i class="text-xl transition-transform duration-300 fas fa-bars group-hover:scale-110"></i>
        </button>

        <!-- Título -->
        <h1 class="text-sm font-bold tracking-wide uppercase md:text-lg">
            {{ title }}
        </h1>

        <!-- Sección de acciones -->
        <div class="flex items-center gap-6">
            <!-- Usuario -->
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-hidden focus:border-gray-300">
                        <img class="object-cover rounded-full size-8" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.nombre">
                    </button>

                    <span v-else class="inline-flex rounded-md">
                        <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-bold uppercase leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-hidden focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700">
                            {{ $page.props.auth.user.nombre }}

                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <div class="px-4 py-2 text-xs font-medium text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        Administrar cuenta
                    </div>

                    <DropdownLink :href="route('profile.show')" class="transition hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i class="w-4 mr-2 text-sm text-gray-500 fas fa-user"></i> Perfil
                    </DropdownLink>

                    <div class="my-1 border-t border-gray-200 dark:border-gray-700" />

                    <form @submit.prevent="logout">
                        <DropdownLink as="button" class="transition hover:bg-gray-100 dark:hover:bg-gray-800">
                            <i class="w-4 mr-2 text-sm text-gray-500 fas fa-right-to-bracket"></i>
                            Salir
                        </DropdownLink>
                    </form>
                </template>
            </Dropdown>
        </div>
    </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const page = usePage()

defineProps({
    title: String,
    isMobile: Boolean,
    sidebarCollapsed: Boolean,
})

defineEmits(['toggle-mobile-sidebar'])

const logout = () => {
    router.post(route('logout'))
}
</script>

<style scoped>
@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.3s ease-out;
}
</style>
