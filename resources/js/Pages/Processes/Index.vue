<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import type { Process } from '@/types';

defineProps<{
    processes: Process[];
}>();
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Procesy</h1>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Link
                v-for="process in processes"
                :key="process.id"
                :href="`/processes/${process.id}`"
                class="bg-navy-900 rounded-xl border border-navy-700 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                            :style="{ backgroundColor: process.color }"
                        >
                            {{ process.badge || process.name[0] }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">{{ process.name }}</h3>
                            <p class="text-xs text-gray-500">{{ process.steps?.length || 0 }} kroků</p>
                        </div>
                    </div>
                    <Badge color="blue" size="sm">
                        {{ process.contacts_count || 0 }} kontaktů
                    </Badge>
                </div>
                <p v-if="process.note" class="text-sm text-gray-500 mb-3">{{ process.note }}</p>
                <div class="flex flex-wrap gap-1">
                    <span
                        v-for="step in process.steps"
                        :key="step.id"
                        class="text-xs bg-navy-800 text-gray-400 px-2 py-0.5 rounded"
                    >
                        {{ step.order }}. {{ step.name }}
                    </span>
                </div>
            </Link>
        </div>
    </AppLayout>
</template>
