<template>
    <div class="w-full my-3 px-1" data-md-input="true" ref="rootRef">
        <!-- Zona de drop -->
        <div
            class="flex flex-col items-center justify-center w-full h-24 px-4 py-6 border-2 border-dashed rounded-lg transition-colors duration-300 cursor-pointer"
            :class="[
                borderColor,
                dragging
                    ? 'bg-blue-50/30 dark:bg-blue-900/10'
                    : 'hover:bg-blue-50/20 dark:hover:bg-blue-900/10',
            ]"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="handleDrop"
            @click="triggerInput"
        >
            <div v-if="!fileNames">
                <i :class="iconClass" class="text-3xl" style="color: var(--color-primary)"></i>
            </div>
            <div v-else>
                <i class="text-3xl fa-solid fa-file-arrow-down" :class="errorText ? 'text-red-500' : 'text-green-500'"></i>
            </div>

            <p class="text-sm font-medium text-center mt-1 text-gray-600 dark:text-gray-400">
                {{ placeholderTitle }}
            </p>

            <p class="text-xs text-center text-gray-500 dark:text-gray-500 mt-1">
                {{ fileNames || placeholderSub }}
            </p>

            <input
                ref="inputRef"
                type="file"
                class="hidden"
                :accept="accept"
                :multiple="multiple"
                @change="handleInput"
            />
        </div>

        <!-- Texto de error -->
        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <div
                class="ml-1"
                :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'"
            >
                {{ errorText || helper }}
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, computed, onMounted } from 'vue';

    const props = defineProps({
        modelValue: [File, Array, null],
        helper: { type: String, default: '' },
        accept: { type: String, default: '.png,.jpg,.jpeg,.svg,.gif' },
        iconClass: { type: String, default: 'fa fa-upload' },
        multiple: { type: Boolean, default: false },
        required: { type: Boolean, default: false },
        errorText: String,
        success: Boolean,
        placeholderTitle: { type: String, default: 'Subir archivo' },
        placeholderSub: {
            type: String,
            default: 'Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo.',
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const fileList = ref([]);
    const inputRef = ref(null);
    const dragging = ref(false);
    const isDark = ref(false);
    const internalError = ref('');

    // ======= Actualiza fileList desde modelValue =======
    watch(() => props.modelValue, (newValue) => {
            if (newValue === null || newValue === undefined) {
                fileList.value = [];
            } else {
                if (props.multiple === true) {
                    fileList.value = newValue;
                } else {
                    fileList.value = [newValue];
                }
            }
        },
        { immediate: true }
    );

    // ============ Nombre de archivos ============
    const fileNames = computed(() => {
        return fileList.value.length
            ? fileList.value.map((f) => f.name).join(', ')
            : props.placeholderSub;
    });

    // ============ Tiene archivo ============
    const hasFile = computed(() => {
        return props.multiple
            ? Array.isArray(props.modelValue) && props.modelValue.length > 0
            : props.modelValue !== null && props.modelValue !== undefined;
    });

    // ============ Color del borde ============
    const borderColor = computed(() => {
        if (errorText.value) return 'border-[var(--color-complement-2)]';
        if (hasFile.value && fileNames.value) return 'border-green-400';
        return 'border-[var(--color-primary)]';
    });

    // ============ Error combinado (interno y externo) ============
    const errorText = computed(() => {
        if (internalError.value) return internalError.value;
        if (typeof props.errorText === 'string') return props.errorText;
        return null;
    });

    // ============ Validación común ============
    function validate(files = fileList.value) {
        internalError.value = '';

        const accepted = props.accept.split(',').map((type) => type.trim().toLowerCase());

        const validFiles = files.filter((file) => {
            if (!file || !file.name) return false;
            const ext = `.${file.name.split('.').pop().toLowerCase()}`;
            return accepted.includes(ext);
        });

        // No limpies nada, conserva los archivos pero muestra el error
        if (validFiles.length !== files.length) {
            internalError.value = 'Tipo de archivo no permitido';
        } else if (props.required && validFiles.length === 0) {
            internalError.value = 'Este campo es obligatorio';
        } else {
            internalError.value = '';
        }

        // Siempre guarda la lista completa, aunque tenga inválidos
        fileList.value = files;
        emit('update:modelValue', props.multiple ? files : files[0]);

        return validFiles.length === files.length;
    }

    // ============ Validación requerida ============
    watch(fileList, (files) => {
        if (props.required && (!files || files.length === 0)) {
            internalError.value = 'Este campo es obligatorio';
        } else if (internalError.value === 'Este campo es obligatorio') {
            internalError.value = '';
        }
    });

    // ============ Drop ============
    function handleDrop(e) {
        dragging.value = false;
        const files = Array.from(e.dataTransfer.files);
        if (validate(files)) internalError.value = '';
    }

    // ============ Input ============
    function handleInput(e) {
        const files = Array.from(e.target.files);
        if (validate(files)) internalError.value = '';
    }

    // ============ Abrir selector ============
    function triggerInput() {
        inputRef.value?.click();
    }

    // ============ Modo oscuro ============
    onMounted(() => {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        isDark.value = prefersDark.matches;
        prefersDark.addEventListener('change', (e) => {
            isDark.value = e.matches;
        });
    });

    defineExpose({ validate });
</script>
