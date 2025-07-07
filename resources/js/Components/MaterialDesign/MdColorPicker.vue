<template>
    <div class="relative w-full my-3 px-1">
        <!-- Label flotante -->
        <label
            v-if="label"
            :for="id"
            class="absolute text-sm px-1 pointer-events-none z-10 flex items-center gap-1 transform text-[0.75rem] -top-2.5 scale-90 text-[var(--color-primary)]"
            :class="iconClass ? 'left-10' : 'left-3'"
            :style="{ backgroundColor }"
        >
            {{ label }}
        </label>

        <!-- Contenedor del campo + botón -->
        <div
            class="flex items-center border-2 rounded-xl h-10 transition-all duration-300 ease-in-out shadow-sm relative"
            :class="{
                'pl-4': !iconClass,
                'pl-10': iconClass,
                'pr-12': true,
            }"
            :style="{
                borderColor,
                backgroundColor,
            }"
        >
            <!-- Ícono izquierdo opcional -->
            <i
                v-if="iconClass"
                :class="iconClass"
                class="absolute left-3 text-base text-[var(--color-primary-light)]"
            ></i>

            <!-- Texto del color (readonly, se puede copiar) -->
            <span
                class="text-gray-800 dark:text-gray-100 text-sm truncate cursor-pointer"
                @click="copiarHex"
                v-tooltip="tooltipText"
            >
                {{ modelValue }}
            </span>

            <!-- Botón de selección de color -->
            <button
                type="button"
                class="absolute right-3 w-7 h-7 rounded-full flex items-center justify-center text-white shadow-md"
                :style="{ backgroundColor: colorHex }"
                @click.prevent="abrirSelector"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    :stroke="colorHex"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.428 15.341A8 8 0 118.66 4.573l10.769 10.768z"
                    />
                </svg>
            </button>

            <!-- Input color oculto -->
            <input
                ref="colorInput"
                type="color"
                class="hidden"
                :value="modelValue"
                @input="actualizarColor($event.target.value)"
            />
        </div>

        <!-- Helper -->
        <div v-if="helper" class="text-xs text-gray-400 px-1 mt-1 leading-tight">
            {{ helper }}
        </div>
    </div>
</template>

<script setup>
    import { computed, ref, onMounted } from 'vue';

    const props = defineProps({
        id: String,
        label: String,
        modelValue: { type: String, default: '#991b1b' },
        iconClass: String,
        helper: String,
    });

    const emit = defineEmits(['update:modelValue']);

    const colorInput = ref(null);
    const tooltipText = ref('Copiar color');
    const isDark = ref(false);

    const backgroundColor = computed(() => (isDark.value ? '#101828' : '#f3f4f6'));

    const borderColor = computed(() => 'var(--color-primary)');

    const colorHex = computed(() => props.modelValue || '#991b1b');

    function abrirSelector() {
        colorInput.value?.click();
    }

    function actualizarColor(hex) {
        emit('update:modelValue', hex);
    }

    function copiarHex() {
        if (!props.modelValue) return;
        navigator.clipboard.writeText(props.modelValue);
        tooltipText.value = '¡Copiado!';
        console.log('Copiado: ', props.modelValue);

        setTimeout(() => {
            tooltipText.value = 'Copiar color';
        }, 1500);
    }

    onMounted(() => {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        isDark.value = prefersDark.matches;
        prefersDark.addEventListener('change', (e) => {
            isDark.value = e.matches;
        });
    });
</script>
