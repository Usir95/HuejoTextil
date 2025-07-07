<template>
  <div
    class="w-full my-3 px-1"
    :data-md-input="true"
    ref="rootRef"
  >
    <!-- Radio -->
    <label
      class="flex items-start gap-3 cursor-pointer select-none group"
      :class="{ 'opacity-60 pointer-events-none': disabled }"
    >
      <!-- Visual del radio -->
      <span
        class="relative w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200"
        :style="{
          borderColor: errorText ? 'var(--color-complement-2)' : 'var(--color-primary)',
        }"
      >
        <span
          v-if="isSelected"
          class="w-2.5 h-2.5 rounded-full"
          :style="{
            backgroundColor: errorText ? 'var(--color-complement-2)' : 'var(--color-primary)',
          }"
        ></span>

        <input
          type="radio"
          class="absolute inset-0 opacity-0 cursor-pointer"
          :checked="isSelected"
          :disabled="disabled"
          @change="selectOption"
        />
      </span>

      <!-- Label y textos -->
      <div class="text-sm leading-snug">
        <div :class="labelClass">
          <slot>{{ label }}</slot>
          <span v-if="required && !isSelected" class="text-red-500 font-bold text-base ml-1 leading-none">*</span>
        </div>
        <p v-if="helper" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ helper }}</p>
        <p v-if="errorText" class="text-xs text-red-500 mt-1">{{ errorText }}</p>
      </div>
    </label>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

/* Props */
const props = defineProps({
  modelValue: [String, Number],
  value: [String, Number],
  label: String,
  required: Boolean,
  helper: String,
  errorText: String,
  disabled: Boolean
})

/* Emits */
const emit = defineEmits(['update:modelValue'])

/* Estado */
const internalError = ref('')
const rootRef = ref(null)

const isSelected = computed(() => props.modelValue === props.value)

watch(() => props.modelValue, () => {
  if (props.required && props.modelValue === undefined) {
    internalError.value = 'Este campo es obligatorio'
  } else {
    internalError.value = ''
  }
})

function selectOption() {
  if (props.disabled) return
  emit('update:modelValue', props.value)
}

/* Computed */
const errorText = computed(() => internalError.value || props.errorText || '')

const labelClass = computed(() => {
  return errorText.value
    ? 'text-red-500 font-medium'
    : 'text-gray-700 dark:text-gray-200'
})

/* Validación */
function validate() {
  if (props.required && (props.modelValue === null || props.modelValue === undefined || props.modelValue === '')) {
    internalError.value = 'Este campo es obligatorio'
    return false
  }
  internalError.value = ''
  return true
}

defineExpose({ validate })
</script>
