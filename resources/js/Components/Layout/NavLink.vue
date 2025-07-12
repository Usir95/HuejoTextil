<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    collapsed: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'flex items-center w-full py-2 pl-4 text-sm font-medium text-white bg-[var(--color-sidebar-text)] text-[var(--color-tertiary)] transition duration-150 ease-in-out relative'
        : 'flex items-center w-full p-2 text-sm font-medium  text-[var(--color-sidebar-text)] transition duration-150 ease-in-out';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <i class="ml-4 transition-transform duration-200 group-hover:scale-110">
            <slot name="icon" />
        </i>
        <span v-if="!props.collapsed" class="ml-2 max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap">
            <slot />
        </span>
        <div v-if="props.active" class="absolute right-0 top-1/2 -translate-y-1/2 border-r-[13px] border-r-white border-t-[15px] border-t-transparent border-b-[15px] border-b-transparent"></div>
    </Link>
</template>

