<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import EmptyState from '@/Components/EmptyState.vue';
import type { ContractTemplate, Contact } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    templates: ContractTemplate[];
    contacts: Pick<Contact, 'id' | 'name' | 'email' | 'phone'>[];
}>();

const form = useForm({
    template_id: '',
    contact_id: '',
    data: {} as Record<string, string>,
});

const selectedTemplate = computed(() =>
    props.templates.find(t => String(t.id) === form.template_id)
);

function submit() {
    form.post('/contracts');
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/contracts" class="text-gray-500 hover:text-gray-300">Smlouvy</Link>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold font-serif text-white">Nová smlouva</h1>
            </div>
        </template>

        <div class="max-w-2xl">
            <EmptyState
                v-if="templates.length === 0"
                title="Žádné šablony"
                description="Nejprve nahrajte šablonu smlouvy v sekci Smlouvy."
            >
                <Link href="/contracts">
                    <Button size="sm">Zpět na smlouvy</Button>
                </Link>
            </EmptyState>

            <form v-else @submit.prevent="submit" class="bg-navy-900 rounded-xl border border-navy-700 p-6 space-y-5">
                <SelectInput
                    v-model="form.template_id"
                    label="Šablona"
                    :options="templates.map(t => ({ value: String(t.id), label: t.name }))"
                    placeholder="Vyberte šablonu..."
                    :error="form.errors.template_id"
                    required
                />

                <SelectInput
                    v-model="form.contact_id"
                    label="Kontakt"
                    :options="contacts.map(c => ({ value: String(c.id), label: c.name }))"
                    placeholder="Vyberte kontakt..."
                    :error="form.errors.contact_id"
                    required
                />

                <!-- Dynamic variable fields from template -->
                <div v-if="selectedTemplate?.variables?.length" class="space-y-4">
                    <h3 class="font-medium text-sm text-gray-300">Proměnné šablony</h3>
                    <TextInput
                        v-for="variable in selectedTemplate.variables"
                        :key="variable"
                        v-model="form.data[variable]"
                        :label="variable"
                        :placeholder="`Hodnota pro ${variable}`"
                    />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <Link href="/contracts">
                        <Button variant="secondary" type="button">Zrušit</Button>
                    </Link>
                    <Button type="submit" :loading="form.processing">Vytvořit smlouvu</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
