<script setup>
    const props = defineProps({
        type: { type: String, default: 'button' },
        variant: { type: String, default: 'primary' }, // primary | secondary | dark | light
        disabled: { type: Boolean, default: false },
        loading: { type: Boolean, default: false },
    });

    const emit = defineEmits(['click']);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        @click="$emit('click')"
        :class="[
            'px-4 py-1 rounded-xl font-semibold transition-colors duration-150 inline-flex cursor-pointer items-center justify-center gap-2',
            disabled || loading ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md',
            variant === 'primary' &&
                'text-white bg-[var(--color-primary)] hover:bg-[var(--color-primary-hover)] dark:bg-[var(--color-primary-light)]',
            variant === 'secondary' &&
                'text-white bg-[var(--color-secondary)] hover:brightness-110',
            variant === 'dark' && 'text-white bg-neutral-800 hover:bg-neutral-900',
            variant === 'light' && 'text-gray-800 bg-white hover:bg-gray-100 border',
        ]"
    >
        <!-- Spinner -->
        <span
            v-if="loading"
            class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"
        ></span>

        <!-- Text -->
        <span>
            <slot />
        </span>
    </button>
</template>
