<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import type { NotificationTemplate } from '@/types';

const props = defineProps<{
    template: NotificationTemplate;
}>();

const form = useForm({
    name: props.template.name,
    type: props.template.type,
    subject: props.template.subject || '',
    body: props.template.body,
});

function submit() {
    form.put(`/notifications/${props.template.id}`);
}

const typeOptions = [
    { value: 'sms', label: 'SMS' },
    { value: 'email', label: 'E-mail' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/notifications" class="text-gray-500 hover:text-gray-300">Notifikace</Link>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold font-serif text-white">Upravit šablonu</h1>
            </div>
        </template>

        <div class="max-w-2xl">
            <form @submit.prevent="submit" class="bg-navy-900 rounded-xl border border-navy-700 p-6 space-y-5">
                <TextInput v-model="form.name" label="Název šablony" :error="form.errors.name" required />
                <SelectInput v-model="form.type" label="Typ" :options="typeOptions" :error="form.errors.type" required />
                <TextInput v-if="form.type === 'email'" v-model="form.subject" label="Předmět" :error="form.errors.subject" />
                <TextArea v-model="form.body" label="Text zprávy" :rows="6" :error="form.errors.body" required />

                <div class="bg-navy-800 rounded-lg p-3">
                    <p class="text-xs font-medium text-gray-500 mb-1">Dostupné proměnné:</p>
                    <p class="text-xs text-gray-400">
                        <code class="bg-navy-700 px-1 rounded">{'{{jmeno}}'}</code>
                        <code class="bg-navy-700 px-1 rounded ml-1">{'{{telefon}}'}</code>
                        <code class="bg-navy-700 px-1 rounded ml-1">{'{{email}}'}</code>
                        <code class="bg-navy-700 px-1 rounded ml-1">{'{{datum}}'}</code>
                        <code class="bg-navy-700 px-1 rounded ml-1">{'{{castka}}'}</code>
                        <code class="bg-navy-700 px-1 rounded ml-1">{'{{adresa}}'}</code>
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <Link href="/notifications">
                        <Button variant="secondary" type="button">Zrušit</Button>
                    </Link>
                    <Button type="submit" :loading="form.processing">Uložit</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
