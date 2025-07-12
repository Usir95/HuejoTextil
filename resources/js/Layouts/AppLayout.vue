<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue';
    import { Head, usePage } from '@inertiajs/vue3';

    import Sidebar from '@/Components/Layout/Sidebar.vue';
    import TopBar from '@/Components/Layout/TopBar.vue';

    defineProps({ title: String });

    const tema = ref('light');
    const isMobile = ref(false);
    const mobileOpen = ref(false);
    const sidebarCollapsed = ref(false);
    const page = usePage();

    const CategoriasModulos  = ref(page.props.PermisosModulos || []);
    const PermisosGranulares = ref(page.props.PermisosGranulares || []);

    const checkMobile = () => {
        isMobile.value = window.innerWidth <= 768;
    };

    const toggleMobileSidebar = () => {
        mobileOpen.value = !mobileOpen.value;
    };

    onMounted(() => {
        tema.value = 'light';
        localStorage.setItem('tema', 'light');
        document.documentElement.classList.remove('dark');

        checkMobile();
        window.addEventListener('resize', checkMobile);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', checkMobile);
    });
</script>

<template>
    <div
        class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
    >
        <Head :title="title" />

        <!-- Sidebar -->
        <Sidebar
            :isMobile="isMobile"
            :mobileOpen="mobileOpen"
            :sidebarCollapsed="sidebarCollapsed"
            :CategoriasModulos="CategoriasModulos"
            @close-mobile="mobileOpen = false"
            @update-collapsed="sidebarCollapsed = $event"
        />

        <!-- Contenido principal -->
        <div
            class="flex flex-col flex-1 transition-all duration-300"
            :class="{
                'ml-56': !isMobile && !sidebarCollapsed,
                'ml-14': !isMobile && sidebarCollapsed,
            }"
        >
            <!-- TopBar fijo -->
            <TopBar
                :title="title"
                :isMobile="isMobile"
                :sidebarCollapsed="sidebarCollapsed"
                @toggle-mobile-sidebar="toggleMobileSidebar"
            />

            <!-- Página -->
            <main class="mt-14">
                <section v-if="$slots.header || $slots['header-left'] || $slots['header-right']" class="bg-gray-100 mb-4 dark:bg-gray-900 border-b border-gray-300 dark:border-gray-700">
                    <div class="px-3 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <!-- Filtros u opciones -->
                        <div class="flex flex-wrap items-center space-x-2">
                            <slot name="header-left" />
                        </div>

                        <!-- Botón principal -->
                        <div class="flex items-center space-x-4">
                            <slot name="header-right" />
                        </div>
                    </div>
                </section>

                <slot />
            </main>
        </div>
    </div>
</template>
