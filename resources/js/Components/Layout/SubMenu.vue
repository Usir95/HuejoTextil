<template>
    <div
        :data-submenu="title"
        :style="{ backgroundColor: backgroundColor, color: textColor }"
        class="overflow-auto text-sm font-bold border-b transition-colors"
    >
        <div
            @click="toggle"
            class="flex items-center px-2 py-3 cursor-pointer hover:bg-[var(--color-sidebar-bg)] transition-colors"
        >
            <!-- Columna 1 -->
            <div class="flex justify-center w-10" :style="{ color: textColor }">
                <i :class="icon"></i>
            </div>
            <!-- Columna 2 -->
            <div
                class="flex-1 overflow-hidden whitespace-nowrap text-ellipsis max-w-[140px]"
                :class="{ hidden: collapsed }"
                :style="{ color: textColor }"
            >
                {{ title }}
            </div>
            <!-- Columna 3 -->
            <div class="flex justify-center w-4 text-xl" :class="{ hidden: collapsed }" :style="{ color: textColor }">
                <i :class="isOpen ? 'fa-solid fa-caret-up' : 'fa-solid fa-caret-down'"></i>
            </div>
        </div>

        <!-- Contenido del SubMenú -->
        <transition name="slide-fade">
            <div v-show="isOpen && !collapsed" class="py-2" :style="{ color: textColor }">
                <slot></slot>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, inject, watch, onMounted, computed } from "vue";

const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, required: true },
    collapsed: { type: Boolean, required: true },
});

const activeSubmenu = inject("activeSubmenu");
const isOpen = ref(false);
const isDark = ref(false);

const backgroundColor = computed(() =>
    isDark.value ? '#101828' : '#f3f4f6'
);

const textColor = computed(() =>
    isDark.value ? '#f1f5f9' : '#1e3a5f'
);

const toggle = () => {
    if (activeSubmenu.value !== props.title) {
        activeSubmenu.value = props.title;
        isOpen.value = true;
    } else {
        isOpen.value = !isOpen.value;
        if (!isOpen.value) activeSubmenu.value = null;
    }
    localStorage.setItem("submenu-open", activeSubmenu.value || "");
};

onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    isDark.value = prefersDark.matches;

    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches;
    });

    const savedState = localStorage.getItem("submenu-open");
    if (savedState === props.title) {
        isOpen.value = true;
        activeSubmenu.value = props.title;
    }
});

watch(activeSubmenu, (newVal) => {
    if (newVal !== props.title) {
        isOpen.value = false;
    }
});
</script>
