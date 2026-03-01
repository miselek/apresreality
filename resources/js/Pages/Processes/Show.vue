<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import type { Process, Contact } from '@/types';
import { TAG_LABELS, TAG_COLORS } from '@/types';

defineProps<{
    process: Process;
    contactsByStep: Record<number, Contact[]>;
}>();
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/processes" class="text-gray-500 hover:text-gray-300">Procesy</Link>
                <span class="text-gray-500">/</span>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" :style="{ backgroundColor: process.color }" />
                    <h1 class="text-xl font-bold font-serif text-white">{{ process.name }}</h1>
                </div>
            </div>
        </template>
        <template #actions>
            <Link :href="`/processes/${process.id}/edit`">
                <Button variant="secondary" size="sm">Upravit kroky</Button>
            </Link>
        </template>

        <!-- Pipeline view -->
        <div class="flex gap-4 overflow-x-auto pb-4">
            <div
                v-for="step in process.steps"
                :key="step.id"
                class="min-w-[280px] max-w-[320px] flex-shrink-0"
            >
                <div class="bg-navy-800 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-medium text-sm text-white">
                            {{ step.order }}. {{ step.name }}
                        </h3>
                        <span class="text-xs text-gray-500 bg-navy-900 px-2 py-0.5 rounded-full">
                            {{ contactsByStep[step.order]?.length || 0 }}
                        </span>
                    </div>
                    <p v-if="step.description" class="text-xs text-gray-500 mb-3">{{ step.description }}</p>
                    <div class="text-xs text-gray-400 mb-3">{{ step.duration_days }} dní</div>

                    <div class="space-y-2">
                        <Link
                            v-for="contact in contactsByStep[step.order] || []"
                            :key="contact.id"
                            :href="`/contacts/${contact.id}`"
                            class="block bg-navy-900 border border-navy-700 rounded-lg p-3 hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-white">{{ contact.name }}</span>
                                <Badge v-if="contact.tag" :color="TAG_COLORS[contact.tag] as any" size="sm">
                                    {{ TAG_LABELS[contact.tag] }}
                                </Badge>
                            </div>
                            <p v-if="contact.phone" class="text-xs text-gray-500 mt-1">{{ contact.phone }}</p>
                        </Link>

                        <div v-if="!contactsByStep[step.order]?.length" class="text-xs text-gray-400 text-center py-4">
                            Žádné kontakty
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
