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
                    <button
                        class="inline-flex items-center gap-3 px-3 py-1.5 text-sm font-semibold uppercase transition duration-300 ease-in-out transform rounded-full hover:bg-[var(--color-topbar-hover)] hover:scale-105 focus:outline-none"
                    >
                        <span class="hidden md:inline">
                            {{ page.props.auth.user.name }}
                        </span>
                        <div
                            class="flex items-center justify-center w-8 h-8 text-sm font-bold rounded-full shadow-md"
                            style="background-color: var(--color-avatar-bg); color: var(--color-avatar-text)"
                        >
                            {{
                                page.props.auth.user.name?.split(' ')[0]?.[0] || ''
                            }}{{
                                page.props.auth.user.name?.split(' ')[1]?.[0] || ''
                            }}
                        </div>
                    </button>
                </template>

                <template #content>
                    <div class="px-4 py-2 text-xs font-medium text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        Administrar cuenta
                    </div>

                    <DropdownLink
                        :href="route('profile.show')"
                        class="transition hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <i class="w-4 mr-2 text-sm text-gray-500 fas fa-user"></i>
                        Perfil
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
