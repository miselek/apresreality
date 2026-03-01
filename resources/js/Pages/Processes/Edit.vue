<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import type { Process } from '@/types';

const props = defineProps<{
    process: Process;
}>();

const form = useForm({
    name: props.process.name,
    color: props.process.color,
    badge: props.process.badge || '',
    note: props.process.note || '',
    steps: (props.process.steps || []).map(s => ({
        id: s.id,
        name: s.name,
        description: s.description || '',
        duration_days: s.duration_days,
        is_auto: s.is_auto,
    })),
});

function addStep() {
    form.steps.push({
        id: null as any,
        name: '',
        description: '',
        duration_days: 1,
        is_auto: false,
    });
}

function removeStep(index: number) {
    form.steps.splice(index, 1);
}

function moveStep(index: number, direction: -1 | 1) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= form.steps.length) return;
    const temp = form.steps[index];
    form.steps[index] = form.steps[newIndex];
    form.steps[newIndex] = temp;
}

function submit() {
    form.put(`/processes/${props.process.id}`);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/processes" class="text-gray-500 hover:text-gray-300">Procesy</Link>
                <span class="text-gray-500">/</span>
                <Link :href="`/processes/${process.id}`" class="text-gray-500 hover:text-gray-300">{{ process.name }}</Link>
                <span class="text-gray-500">/</span>
                <h1 class="text-xl font-bold font-serif text-white">Upravit</h1>
            </div>
        </template>

        <div class="max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Process info -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 space-y-4">
                    <h3 class="font-semibold text-white">Základní údaje</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <TextInput v-model="form.name" label="Název procesu" required />
                        <TextInput v-model="form.color" label="Barva" type="color" />
                        <TextInput v-model="form.badge" label="Označení" placeholder="A, B, C..." />
                    </div>
                    <TextArea v-model="form.note" label="Poznámka" :rows="2" />
                </div>

                <!-- Steps -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-white">Kroky procesu</h3>
                        <Button type="button" size="sm" variant="secondary" @click="addStep">
                            Přidat krok
                        </Button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(step, index) in form.steps"
                            :key="index"
                            class="border border-navy-700 rounded-lg p-4"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex flex-col gap-1 pt-1">
                                    <button type="button" class="text-gray-500 hover:text-gray-300 text-xs" @click="moveStep(index, -1)" :disabled="index === 0">&#9650;</button>
                                    <span class="text-xs font-bold text-gray-500 text-center">{{ index + 1 }}</span>
                                    <button type="button" class="text-gray-500 hover:text-gray-300 text-xs" @click="moveStep(index, 1)" :disabled="index === form.steps.length - 1">&#9660;</button>
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="sm:col-span-2">
                                            <TextInput v-model="step.name" placeholder="Název kroku" required />
                                        </div>
                                        <TextInput v-model.number="step.duration_days" type="number" placeholder="Dní" />
                                    </div>
                                    <TextInput v-model="step.description" placeholder="Popis (nepovinné)" />
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" v-model="step.is_auto" class="rounded" />
                                        Automatický krok
                                    </label>
                                </div>
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-red-400 p-1"
                                    @click="removeStep(index)"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <Link :href="`/processes/${process.id}`">
                        <Button variant="secondary" type="button">Zrušit</Button>
                    </Link>
                    <Button type="submit" :loading="form.processing">Uložit změny</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
