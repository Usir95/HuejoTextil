<script setup>
import Modal from './MdModal.vue'

const emit = defineEmits(['close'])

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
})

const close = () => {
    emit('close')
}
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="max-h-[90vh] flex flex-col relative">
            <!-- Botón cerrar -->
            <button
                @click="close"
                class="absolute top-4 right-4 z-20 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"
                aria-label="Cerrar"
                role="button"
                title="Cerrar"
            >
                ✕
            </button>

            <!-- Header fijo -->
            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <slot name="title" />
                </div>
            </div>

            <!-- Contenido con scroll -->
            <div class="overflow-y-auto px-6 py-4 text-sm text-gray-600 dark:text-gray-400 custom-scrollbar shadow-inner">
                <slot name="content" />
            </div>

            <!-- Footer fijo -->
            <div class="sticky bottom-0 z-10 bg-gray-100 dark:bg-gray-800 px-6 py-4 border-t border-gray-300 dark:border-gray-700 flex justify-end">
                <slot name="footer" />
            </div>
        </div>
    </Modal>
</template>
