<script setup lang="ts">
defineProps<{
    modelValue?: string | number;
    label?: string;
    error?: string;
    type?: string;
    placeholder?: string;
    required?: boolean;
}>();

defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-300 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-400">*</span>
        </label>
        <input
            :type="type || 'text'"
            :value="modelValue"
            :placeholder="placeholder"
            :class="[
                'block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-gold-500 placeholder-gray-500',
                error ? 'border-red-500 text-red-400 bg-navy-800' : 'border-navy-600 text-white bg-navy-800'
            ]"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-400">{{ error }}</p>
    </div>
</template>
