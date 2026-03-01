<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import type { Contact, Process } from '@/types';
import { SOURCE_LABELS, TYPE_LABELS, STATUS_LABELS, TAG_LABELS } from '@/types';

const props = defineProps<{
    contact: Contact;
    processes: Process[];
}>();

const form = useForm({
    name: props.contact.name,
    phone: props.contact.phone || '',
    email: props.contact.email || '',
    source: props.contact.source,
    type: props.contact.type,
    status: props.contact.status,
    tag: props.contact.tag || '',
    process_id: props.contact.process_id ? String(props.contact.process_id) : '',
    notes: props.contact.notes || '',
});

function submit() {
    form.put(`/contacts/${props.contact.id}`);
}

const sourceOptions = Object.entries(SOURCE_LABELS).map(([v, l]) => ({ value: v, label: l }));
const typeOptions = Object.entries(TYPE_LABELS).map(([v, l]) => ({ value: v, label: l }));
const statusOptions = Object.entries(STATUS_LABELS).map(([v, l]) => ({ value: v, label: l }));
const tagOptions = Object.entries(TAG_LABELS).map(([v, l]) => ({ value: v, label: l }));
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/contacts" class="text-gray-500 hover:text-gray-300">Kontakty</Link>
                <span class="text-gray-500">/</span>
                <Link :href="`/contacts/${contact.id}`" class="text-gray-500 hover:text-gray-300">{{ contact.name }}</Link>
                <span class="text-gray-500">/</span>
                <h1 class="text-xl font-bold font-serif text-white">Upravit</h1>
            </div>
        </template>

        <div class="max-w-2xl">
            <form @submit.prevent="submit" class="bg-navy-900 rounded-xl border border-navy-700 p-6 space-y-5">
                <TextInput v-model="form.name" label="Jméno" :error="form.errors.name" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.phone" label="Telefon" :error="form.errors.phone" />
                    <TextInput v-model="form.email" label="E-mail" type="email" :error="form.errors.email" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <SelectInput v-model="form.source" label="Zdroj" :options="sourceOptions" :error="form.errors.source" required />
                    <SelectInput v-model="form.type" label="Typ" :options="typeOptions" :error="form.errors.type" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <SelectInput v-model="form.status" label="Status" :options="statusOptions" :error="form.errors.status" required />
                    <SelectInput v-model="form.tag" label="Štítek" :options="tagOptions" placeholder="Bez štítku" :error="form.errors.tag" />
                </div>

                <SelectInput
                    v-model="form.process_id"
                    label="Proces"
                    :options="processes.map(p => ({ value: String(p.id), label: p.name }))"
                    placeholder="Bez procesu"
                    :error="form.errors.process_id"
                />

                <TextArea v-model="form.notes" label="Poznámky" :error="form.errors.notes" />

                <div class="flex justify-end gap-3 pt-2">
                    <Link :href="`/contacts/${contact.id}`">
                        <Button variant="secondary" type="button">Zrušit</Button>
                    </Link>
                    <Button type="submit" :loading="form.processing" :disabled="form.processing">
                        Uložit změny
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
