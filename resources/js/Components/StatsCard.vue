<script setup lang="ts">
defineProps<{
    label: string;
    value: string | number;
    color?: 'blue' | 'green' | 'red' | 'yellow' | 'purple' | 'gold';
}>();
</script>

<template>
    <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
        <p class="text-sm font-medium text-gray-400">{{ label }}</p>
        <p
            :class="[
                'mt-1 text-2xl font-bold',
                {
                    'text-blue-400': color === 'blue' || !color,
                    'text-green-400': color === 'green',
                    'text-red-400': color === 'red',
                    'text-yellow-400': color === 'yellow',
                    'text-purple-400': color === 'purple',
                    'text-gold-500': color === 'gold',
                }
            ]"
        >
            {{ value }}
        </p>
    </div>
</template>
