<script setup>
import { computed, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    show: Boolean,
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['close'])

const close = () => {
    if (props.closeable) emit('close')
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault()
        close()
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

watch(() => props.show, (val) => {
    document.body.style.overflow = val ? 'hidden' : ''
})

const maxWidthClass = computed(() => ({
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl',
}[props.maxWidth] || 'sm:max-w-2xl'))
</script>

<template>
    <Teleport to="body">
        <transition name="fade">
            <div
                v-if="show"
                role="dialog"
                aria-modal="true"
                class="fixed inset-0 z-50 flex items-start justify-center px-4 pt-10 sm:px-0"
                style="background-color: rgba(0, 0, 0, 0.5);"
                @click.self="close"
            >
                <transition name="modal">
                    <div
                        v-if="show"
                        class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl transform transition-all w-full custom-scrollbar"
                        :class="maxWidthClass"
                    >
                        <slot />
                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>
