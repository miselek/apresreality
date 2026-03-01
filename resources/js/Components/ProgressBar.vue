<script setup lang="ts">
defineProps<{
    value: number;
    color?: string;
    showLabel?: boolean;
}>();
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="flex-1 bg-navy-800 rounded-full h-2 overflow-hidden">
            <div
                class="h-full rounded-full transition-all duration-300"
                :style="{
                    width: `${Math.min(100, Math.max(0, value))}%`,
                    backgroundColor: color || '#D4A843'
                }"
            />
        </div>
        <span v-if="showLabel" class="text-xs font-medium text-gray-400 w-10 text-right">
            {{ Math.round(value) }}%
        </span>
    </div>
</template>
