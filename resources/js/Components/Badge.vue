<script setup lang="ts">
defineProps<{
    color?: 'red' | 'blue' | 'green' | 'yellow' | 'gray' | 'purple' | 'indigo' | 'gold';
    size?: 'sm' | 'md';
}>();
</script>

<template>
    <span
        :class="[
            'inline-flex items-center font-medium rounded-full',
            size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-xs',
            {
                'bg-red-900/40 text-red-400': color === 'red',
                'bg-blue-900/40 text-blue-400': color === 'blue',
                'bg-green-900/40 text-green-400': color === 'green',
                'bg-yellow-900/40 text-yellow-400': color === 'yellow',
                'bg-navy-800 text-gray-400': color === 'gray' || !color,
                'bg-purple-900/40 text-purple-400': color === 'purple',
                'bg-indigo-900/40 text-indigo-400': color === 'indigo',
                'bg-gold-500/20 text-gold-500': color === 'gold',
            }
        ]"
    >
        <slot />
    </span>
</template>
