<template>
    <!-- ============================================ Fondo móvil ============================================ -->
    <div
        v-if="isMobile && mobileOpen"
        class="fixed inset-0 z-10 bg-black/40"
        @click="$emit('close-mobile')"
    />

    <!-- ============================================ Sidebar principal ============================================ -->
    <aside
        :class="[
            'fixed top-0 left-0 h-screen z-20 flex flex-col justify-between overflow-hidden shadow-xl sidebar-transition',
            isMobile ? 'w-64' : sidebarCollapsed ? 'w-14' : 'w-56',
            isMobile ? (mobileOpen ? 'translate-x-0' : '-translate-x-full') : 'translate-x-0',
            'bg-[var(--color-sidebar-bg)] text-[var(--color-sidebar-text)]',
        ]"
    >
        <!-- ============================================ Contenido principal ============================================ -->
        <div class="relative z-10 flex flex-col h-full">
            <!-- ============================================ Encabezado ============================================ -->
            <SidebarHeader
                v-if="session && session.user"
                :isMobile="isMobile"
                :sidebarCollapsed="sidebarCollapsed"
                @update-collapsed="$emit('update-collapsed', $event)"
            />

            <!-- ============================================ Navegación ============================================ -->
            <nav class="flex-1 overflow-y-auto no-scrollbar py-3">
                <SubMenu
                    v-for="categoria in CategoriasModulos"
                    :key="categoria.id"
                    :title="categoria.nombre"
                    :icon="categoria.icono"
                    :collapsed="sidebarCollapsed"
                >
                    <NavLink
                        v-for="modulo in categoria.modulos"
                        :key="modulo.id"
                        :href="route(modulo.ruta)"
                        :active="route().current(modulo.ruta)"
                        :collapsed="sidebarCollapsed"
                    >
                        <template #icon>
                            <i
                                :class="modulo.icono"
                                class="transition-transform duration-200 group-hover:scale-110"
                            />
                        </template>
                        <span class="truncate block max-w-[140px]">
                            {{ modulo.nombre }}
                        </span>
                    </NavLink>
                </SubMenu>
            </nav>
        </div>

        <!-- ============================================ Botón cerrar (móvil) ============================================ -->
        <button
            v-if="isMobile"
            @click="$emit('close-mobile')"
            class="absolute z-20 top-2 right-2 text-white hover:text-red-300 transition"
        >
            <i class="fas fa-times" />
        </button>

        <!-- ============================================ Footer: logout ============================================ -->
        <footer class="z-10 px-3 py-4 border-t border-[var(--color-sidebar-border)]">
            <form @submit.prevent="logout">
                <button
                    type="submit"
                    class="w-full flex items-center gap-2 text-sm font-medium text-left text-[var(--color-sidebar-text)] hover:text-[var(--color-primary-hover)] transition-colors"
                >
                    <i class="fas fa-sign-out-alt" />
                    <span v-if="!sidebarCollapsed">Cerrar Sesión</span>
                </button>
            </form>
        </footer>
    </aside>
</template>

<script setup>
/* ============================================ Imports ============================================ */
import { ref, provide } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import SidebarHeader from '@/Components/Layout/SidebarHeader.vue'
import NavLink from '@/Components/Layout/NavLink.vue'
import SubMenu from '@/Components/Layout/SubMenu.vue'

/* ============================================ Props ============================================ */
defineProps({
    isMobile: Boolean,
    mobileOpen: Boolean,
    sidebarCollapsed: Boolean,
    CategoriasModulos: Object,
})

/* ============================================ Emits ============================================ */
defineEmits(['close-mobile', 'update-collapsed'])

/* ============================================ Variables ============================================ */
const activeSubmenu = ref(null)
const page = usePage()
const session = ref(page.props.auth)
provide('activeSubmenu', activeSubmenu)

/* ============================================ Functions ============================================ */
function logout() {
    router.post(route('logout'))
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    scrollbar-width: none;
}

.sidebar-transition {
  transition: width 300ms cubic-bezier(0.4, 0.0, 0.2, 1); /* curva Material Design */
}
</style>
