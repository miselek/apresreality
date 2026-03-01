<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue?: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const localValue = ref(props.modelValue || '');
let timeout: ReturnType<typeof setTimeout>;

watch(localValue, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('update:modelValue', val);
    }, 300);
});

watch(() => props.modelValue, (val) => {
    localValue.value = val || '';
});
</script>

<template>
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
            v-model="localValue"
            type="text"
            :placeholder="placeholder || 'Hledat...'"
            class="block w-full rounded-lg border border-navy-600 bg-navy-800 text-white placeholder-gray-500 pl-10 pr-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-gold-500"
        />
    </div>
</template>
