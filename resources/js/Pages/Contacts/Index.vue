<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import type { Contact, PaginatedData } from '@/types';
import { SOURCE_LABELS, TYPE_LABELS, STATUS_LABELS, TAG_LABELS, TAG_COLORS } from '@/types';
import { ref, watch } from 'vue';

const props = defineProps<{
    contacts: PaginatedData<Contact>;
    filters: Record<string, string>;
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const type = ref(props.filters.type || '');
const source = ref(props.filters.source || '');
const tag = ref(props.filters.tag || '');

function applyFilters() {
    router.get('/contacts', {
        search: search.value || undefined,
        status: status.value || undefined,
        type: type.value || undefined,
        source: source.value || undefined,
        tag: tag.value || undefined,
    }, { preserveState: true, replace: true });
}

watch([status, type, source, tag], applyFilters);
watch(search, () => {
    applyFilters();
});

const sourceOptions = Object.entries(SOURCE_LABELS).map(([v, l]) => ({ value: v, label: l }));
const typeOptions = Object.entries(TYPE_LABELS).map(([v, l]) => ({ value: v, label: l }));
const statusOptions = Object.entries(STATUS_LABELS).map(([v, l]) => ({ value: v, label: l }));
const tagOptions = Object.entries(TAG_LABELS).map(([v, l]) => ({ value: v, label: l }));
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Kontakty</h1>
        </template>
        <template #actions>
            <Link href="/contacts/create">
                <Button>Nový kontakt</Button>
            </Link>
        </template>

        <!-- Filters -->
        <div class="bg-navy-900 rounded-xl border border-navy-700 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <SearchInput v-model="search" placeholder="Hledat jméno, email, telefon..." />
                <SelectInput v-model="status" :options="statusOptions" placeholder="Všechny statusy" />
                <SelectInput v-model="type" :options="typeOptions" placeholder="Všechny typy" />
                <SelectInput v-model="source" :options="sourceOptions" placeholder="Všechny zdroje" />
                <SelectInput v-model="tag" :options="tagOptions" placeholder="Všechny štítky" />
            </div>
        </div>

        <!-- Table -->
        <div class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden">
            <EmptyState
                v-if="contacts.data.length === 0"
                title="Žádné kontakty"
                description="Začněte přidáním prvního kontaktu."
            >
                <Link href="/contacts/create">
                    <Button size="sm">Přidat kontakt</Button>
                </Link>
            </EmptyState>

            <table v-else class="w-full">
                <thead class="bg-navy-800">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Jméno</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden sm:table-cell">Telefon</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden md:table-cell">Email</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Status</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden lg:table-cell">Typ</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Štítek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-700">
                    <tr
                        v-for="contact in contacts.data"
                        :key="contact.id"
                        class="hover:bg-navy-800 cursor-pointer"
                        @click="router.visit(`/contacts/${contact.id}`)"
                    >
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-white">{{ contact.name }}</div>
                            <div v-if="contact.process" class="text-xs text-gray-500">{{ contact.process.name }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden sm:table-cell">{{ contact.phone }}</td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden md:table-cell">{{ contact.email }}</td>
                        <td class="px-4 py-3">
                            <Badge :color="contact.status === 'aktivni' ? 'green' : contact.status === 'ceka' ? 'yellow' : 'gray'" size="sm">
                                {{ STATUS_LABELS[contact.status] }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden lg:table-cell">
                            {{ TYPE_LABELS[contact.type] }}
                        </td>
                        <td class="px-4 py-3">
                            <Badge v-if="contact.tag" :color="TAG_COLORS[contact.tag] as any" size="sm">
                                {{ TAG_LABELS[contact.tag] }}
                            </Badge>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="px-4 py-3 border-t border-navy-700">
                <Pagination :links="contacts.links" />
            </div>
        </div>
    </AppLayout>
</template>
