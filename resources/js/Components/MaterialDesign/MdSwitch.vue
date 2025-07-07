<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        modelValue: Boolean,
        label: String,
        icon: String,
        id: String,
        name: String,
        required: Boolean,
        disabled: Boolean,
    });

    const emit = defineEmits(['update:modelValue']);

    const isChecked = computed(() => props.modelValue);

    function toggle() {
        if (!props.disabled) emit('update:modelValue', !props.modelValue);
    }
</script>

<template>
    <div class="flex items-center gap-2 select-none">
        <!-- Icono -->
        <i v-if="icon" :class="`mdi mdi-${icon} text-base text-gray-600 dark:text-gray-300`" />

        <!-- Switch -->
        <input type="hidden" :name="name" value="0" />
        <button
            type="button"
            :id="id"
            @click="toggle"
            :disabled="disabled"
            :class="[
                'relative w-12 h-6 flex items-center rounded-full transition duration-300 focus:outline-none',
                isChecked ? 'bg-[var(--color-primary)]' : 'bg-gray-300',
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            ]"
        >
            <span
                :class="[
                    'w-5 h-5 rounded-full shadow-md transform transition-all duration-300',
                    isChecked ? 'translate-x-6' : 'translate-x-1',
                    'bg-white dark:bg-gray-200',
                ]"
            />
        </button>

        <!-- Etiqueta -->
        <label v-if="label" :for="id" class="text-sm text-gray-800 dark:text-gray-200">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <!-- Campo oculto para formularios -->
        <input
            type="checkbox"
            class="hidden"
            :id="id"
            :name="name"
            :checked="modelValue"
            :required="required"
        />
    </div>
</template>
