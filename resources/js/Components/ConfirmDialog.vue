<script setup lang="ts">
import Modal from './Modal.vue';
import Button from './Button.vue';

defineProps<{
    show: boolean;
    title?: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'primary' | 'danger';
}>();

defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <Modal :show="show" max-width="sm" @close="$emit('cancel')">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-white mb-2">
                {{ title || 'Potvrzení' }}
            </h3>
            <p class="text-sm text-gray-400 mb-6">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="$emit('cancel')">
                    {{ cancelText || 'Zrušit' }}
                </Button>
                <Button
                    :variant="variant || 'primary'"
                    size="sm"
                    @click="$emit('confirm')"
                >
                    {{ confirmText || 'Potvrdit' }}
                </Button>
            </div>
        </div>
    </Modal>
</template>
