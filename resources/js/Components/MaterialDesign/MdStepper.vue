<template>
    <div
        class="w-full my-6 px-4 flex"
        :class="orientation === 'vertical' ? 'flex-col items-start space-y-6' : 'flex-row items-center justify-between'"
        role="list"
        aria-label="Progreso de pasos"
    >
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="relative flex flex-col items-center"
            :class="orientation === 'vertical' ? 'w-full' : 'flex-1'"
            role="listitem"
        >
            <!-- Línea antes del paso -->
            <div
                v-if="i > 0 && orientation === 'horizontal'"
                class="absolute top-4 left-0 right-1/2 h-0.5 z-0 transition-colors duration-300"
                :class="[
                    i <= index
                        ? 'bg-[var(--color-primary)]'
                        : 'bg-gray-300 dark:bg-gray-600',
                ]"
            ></div>

            <div
                v-if="i > 0 && orientation === 'vertical'"
                class="absolute top-0 left-4 bottom-1/2 w-0.5 z-0 transition-colors duration-300"
                :class="[
                    i <= index
                        ? 'bg-[var(--color-primary)]'
                        : 'bg-gray-300 dark:bg-gray-600',
                ]"
            ></div>

            <!-- Círculo del paso -->
            <div
                class="z-10 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold transition-all duration-300"
                :class="[
                    i < index
                        ? 'bg-[var(--color-primary)]'
                        : i === index
                        ? 'bg-[var(--color-primary-hover)]'
                        : 'bg-gray-400 dark:bg-gray-600',
                ]"
                :aria-current="i === index ? 'step' : null"
            >
                <span v-if="i < index">✓</span>
                <span v-else>{{ i + 1 }}</span>
            </div>

            <!-- Línea después del paso -->
            <div
                v-if="i < steps.length - 1 && orientation === 'horizontal'"
                class="absolute top-4 left-1/2 right-0 h-0.5 z-0 transition-colors duration-300"
                :class="[
                    i < index
                        ? 'bg-[var(--color-primary)]'
                        : 'bg-gray-300 dark:bg-gray-600',
                ]"
            ></div>

            <div
                v-if="i < steps.length - 1 && orientation === 'vertical'"
                class="absolute top-1/2 left-4 bottom-0 w-0.5 z-0 transition-colors duration-300"
                :class="[
                    i < index
                        ? 'bg-[var(--color-primary)]'
                        : 'bg-gray-300 dark:bg-gray-600',
                ]"
            ></div>

            <!-- Texto del paso -->
            <div class="mt-2 text-xs text-center text-gray-800 dark:text-gray-200">
                {{ step }}
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    steps: Array,
    index: Number, // índice actual del paso (0 = paso 1)
    orientation: {
        type: String,
        default: 'horizontal', // o 'vertical'
        validator: (val) => ['horizontal', 'vertical'].includes(val),
    },
});
</script>
