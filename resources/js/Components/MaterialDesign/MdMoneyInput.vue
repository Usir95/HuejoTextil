<template>
    <div class="relative w-full my-3 px-1" data-md-input="true" ref="rootRef">
        <label
            v-if="label"
            :for="id"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || internalValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3',
            ]"
            :style="{ backgroundColor }"
        >
            <span
                v-if="required && !internalValue && !errorText"
                class="text-red-500 font-bold text-base leading-none"
                >*</span
            >
            <svg
                v-else-if="success && !errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-primary-hover)] ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <svg
                v-else-if="errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-complement-2)] ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ label }}
        </label>

        <div
            v-if="iconLeft || iconClass"
            class="absolute left-3 h-10 flex items-center pointer-events-none"
            :style="{ color: borderColor }"
        >
            <slot name="iconLeft" v-if="iconLeft" />
            <i v-else :class="iconClass" class="text-base leading-none"></i>
        </div>

        <input
            :id="id"
            type="text"
            :name="name"
            ref="inputRef"
            class="w-full h-10 border rounded-xl text-gray-800 dark:text-gray-100 placeholder-white focus:outline-none focus:ring-2 transition-all duration-300 ease-in-out shadow-sm focus:shadow-md"
            :class="{
                'opacity-50 cursor-not-allowed': disabled || readonly,
                'pl-10 pr-4': iconLeft || iconClass,
                'px-4': !iconLeft && !iconClass,
                'ring-1 ring-inset': success,
            }"
            :placeholder="showRealPlaceholder ? placeholder : ' '"
            :disabled="disabled"
            :readonly="readonly"
            :minlength="minlength"
            :maxlength="maxlength"
            @focus="onFocus"
            @blur="onBlur"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                transition: 'border-color 0.3s ease, background-color 0.3s ease',
            }"
        />

        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <div
                class="ml-1"
                :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'"
            >
                {{ errorText || helper }}
            </div>
            <div v-if="showCharCounter" :class="charCountColor">
                {{ internalValue.length }} / {{ maxlength }}
            </div>
        </div>

        <button
            v-if="internalValue && !readonly && !disabled"
            class="absolute right-3 transform -translate-y-1/2 cursor-pointer flex items-center justify-center text-[var(--color-primary)] hover:text-[var(--color-primary-light)]"
            style="top: calc(1 / 2 * 73%)"
            @click.prevent="limpiar"
            tabindex="-1"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 28 28"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>

<script setup>
    import { ref, watch, computed, useSlots, onMounted } from 'vue';
    import IMask from 'imask';

    const props = defineProps({
        modelValue: [String, Number],
        required: Boolean,
        label: String,
        placeholder: String,
        name: String,
        id: String,
        disabled: Boolean,
        readonly: Boolean,
        success: Boolean,
        iconClass: String,
        minlength: Number,
        maxlength: Number,
        helper: String,
        error: [Boolean, String, Array],
    });

    const emit = defineEmits(['update:modelValue', 'focus', 'blur']);
    const isFocused = ref(false);
    const internalValue = ref(props.modelValue?.toString() ?? '');
    const internalError = ref('');
    const isDark = ref(false);
    const inputRef = ref(null);
    const slots = useSlots();
    const showRealPlaceholder = false;

    const iconLeft = computed(() => !!slots.iconLeft);
    const backgroundColor = computed(() => (isDark.value ? '#101828' : '#f3f4f6'));
    const borderColor = computed(() => {
        if (props.error || internalError.value) return 'var(--color-complement-2)';
        if (props.success) return 'var(--color-primary-hover)';
        if (isFocused.value) return 'var(--color-primary)';
        return 'var(--color-primary-light)';
    });
    const labelColor = computed(() =>
        props.error ? 'text-[var(--color-complement-2)]' : 'text-[var(--color-primary)]'
    );
    const errorText = computed(() =>
        Array.isArray(props.error)
            ? props.error[0]
            : typeof props.error === 'string'
            ? props.error
            : internalError.value
    );
    const charCountColor = computed(() => {
        const len = internalValue.value?.length || 0;
        if (!props.maxlength) return 'text-gray-500';
        const percent = (len / props.maxlength) * 100;
        if (len > props.maxlength) return 'text-[var(--color-complement-2)]';
        if (percent >= 90) return 'text-[var(--color-primary)]';
        return 'text-gray-500';
    });
    const showCharCounter = computed(
        () => typeof props.maxlength === 'number' && !props.readonly && !props.disabled
    );

    function onFocus(e) {
        isFocused.value = true;
        emit('focus', e);
    }

    function onBlur(e) {
        isFocused.value = false;
        emit('blur', e);
    }

    function limpiar() {
        internalValue.value = '';
        emit('update:modelValue', '');
    }

    defineExpose({ validate });

    function validate() {
        let message = '';
        if (props.required && internalValue.value === '') message = 'Este campo es obligatorio';
        internalError.value = message;
        return message === '';
    }

    onMounted(() => {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        isDark.value = prefersDark.matches;
        prefersDark.addEventListener('change', (e) => (isDark.value = e.matches));

        if (inputRef.value) {
            const mask = IMask(inputRef.value, {
                mask: Number,
                scale: 2,
                signed: false,
                thousandsSeparator: ',',
                padFractionalZeros: true,
                normalizeZeros: true,
                radix: '.',
                mapToRadix: [','],
                min: 0,
            });

            mask.on('accept', () => {
                internalValue.value = mask.unmaskedValue;
                emit('update:modelValue', Number(mask.unmaskedValue));
            });
        }
    });
</script>
