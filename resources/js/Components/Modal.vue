<script setup lang="ts">
defineProps<{
    show: boolean;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl';
}>();

defineEmits<{
    close: [];
}>();
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/70" @click="$emit('close')" />
                <div
                    :class="[
                        'relative bg-navy-900 border border-navy-700 rounded-xl shadow-xl w-full transform transition-all',
                        {
                            'max-w-sm': maxWidth === 'sm',
                            'max-w-md': !maxWidth || maxWidth === 'md',
                            'max-w-lg': maxWidth === 'lg',
                            'max-w-xl': maxWidth === 'xl',
                        }
                    ]"
                >
                    <slot />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
