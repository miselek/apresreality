<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: { url: string | null; label: string; active: boolean }[];
}>();
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-center gap-1 mt-6">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                    'px-3 py-1.5 text-sm rounded-lg transition-colors',
                    link.active
                        ? 'bg-gold-500 text-navy-950'
                        : 'text-gray-400 hover:bg-navy-800'
                ]"
                v-html="link.label"
                preserve-scroll
            />
            <span
                v-else
                class="px-3 py-1.5 text-sm text-gray-600"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
