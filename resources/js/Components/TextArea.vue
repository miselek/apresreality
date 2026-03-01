<script setup lang="ts">
defineProps<{
    modelValue?: string;
    label?: string;
    error?: string;
    placeholder?: string;
    rows?: number;
}>();

defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-300 mb-1">
            {{ label }}
        </label>
        <textarea
            :value="modelValue"
            :placeholder="placeholder"
            :rows="rows || 3"
            :class="[
                'block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-gold-500 placeholder-gray-500',
                error ? 'border-red-500 text-red-400 bg-navy-800' : 'border-navy-600 text-white bg-navy-800'
            ]"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-400">{{ error }}</p>
    </div>
</template>
