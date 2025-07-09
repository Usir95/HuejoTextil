<template>
  <div
    class="my-3 px-1"
    :data-md-input="true"
    ref="rootRef"
  >
    <!-- Checkbox -->
    <label
      class="flex items-start gap-3 cursor-pointer select-none group"
      :class="{ 'opacity-60 pointer-events-none': disabled }"
    >
      <!-- Input visual -->
<!-- Input visual -->
<span
  class="relative w-5 h-5 flex items-center justify-center border-2 rounded transition-all duration-200"
  :style="{
    borderColor: errorText ? 'var(--color-complement-2)' : 'var(--color-primary)',
    backgroundColor: checked ? 'var(--color-primary)' : 'transparent',
    color: checked ? 'white' : 'transparent'
  }"
>
  <svg v-if="checked" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
  </svg>
  <input
    type="checkbox"
    class="absolute inset-0 opacity-0 cursor-pointer"
    :checked="checked"
    :disabled="disabled"
    @change="toggle"
  />
</span>


      <!-- Label y texto -->
      <div class="text-sm leading-snug">
        <div :class="labelClass">
          <slot>{{ label }}</slot>
          <span v-if="required && !checked" class="text-red-500 font-bold text-base ml-1 leading-none">*</span>
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
  modelValue: { type: Boolean, default: false },
  label: String,
  required: Boolean,
  helper: { type: String, default: '' },
  errorText: String,
  disabled: Boolean
})

/* Emits */
const emit = defineEmits(['update:modelValue'])

/* Refs y lógica */
const checked = ref(!!props.modelValue)
const internalError = ref('')
const rootRef = ref(null)

watch(() => props.modelValue, val => {
  checked.value = !!val
  if (props.required && !checked.value) {
    internalError.value = 'Este campo es obligatorio'
  } else {
    internalError.value = ''
  }
})

function toggle() {
  if (props.disabled) return
  checked.value = !checked.value
  emit('update:modelValue', checked.value)
  if (props.required && !checked.value) {
    internalError.value = 'Este campo es obligatorio'
  } else {
    internalError.value = ''
  }
}

/* Computed */
const errorText = computed(() => internalError.value || props.errorText || '')

const labelClass = computed(() => {
  return errorText.value
    ? 'text-red-500 font-medium'
    : 'text-gray-700 dark:text-gray-200'
})

/* Método de validación */
function validate() {
  if (props.required && !checked.value) {
    internalError.value = 'Este campo es obligatorio'
    return false
  }
  internalError.value = ''
  return true
}

defineExpose({ validate })
</script>
