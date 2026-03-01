<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Contract, ContractTemplate, PaginatedData } from '@/types';

defineProps<{
    contracts: PaginatedData<Contract>;
    templates: ContractTemplate[];
}>();

const showUpload = ref(false);
const uploadName = ref('');
const uploadFile = ref<File | null>(null);

function handleFileChange(e: Event) {
    const input = e.target as HTMLInputElement;
    uploadFile.value = input.files?.[0] || null;
}

function submitUpload() {
    if (!uploadFile.value || !uploadName.value) return;
    const formData = new FormData();
    formData.append('name', uploadName.value);
    formData.append('file', uploadFile.value);
    router.post('/contract-templates', formData, {
        onSuccess: () => {
            showUpload.value = false;
            uploadName.value = '';
            uploadFile.value = null;
        },
    });
}

const statusLabels: Record<string, string> = {
    koncept: 'Koncept',
    validace: 'Validace',
    zvalidovano: 'Zvalidováno',
    odeslano: 'Odesláno',
    podepsano: 'Podepsáno',
    zamitnuto: 'Zamítnuto',
};

const statusColors: Record<string, string> = {
    koncept: 'gray',
    validace: 'yellow',
    zvalidovano: 'green',
    odeslano: 'blue',
    podepsano: 'green',
    zamitnuto: 'red',
};

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ');
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Smlouvy</h1>
        </template>
        <template #actions>
            <div class="flex gap-2">
                <Button variant="secondary" size="sm" @click="showUpload = true">Nahrát šablonu</Button>
                <Link href="/contracts/create">
                    <Button size="sm">Nová smlouva</Button>
                </Link>
            </div>
        </template>

        <EmptyState
            v-if="contracts.data.length === 0"
            title="Žádné smlouvy"
            description="Nahrajte šablonu a vytvořte první smlouvu."
        >
            <div class="flex gap-2 justify-center">
                <Button variant="secondary" size="sm" @click="showUpload = true">Nahrát šablonu</Button>
                <Link href="/contracts/create">
                    <Button size="sm">Nová smlouva</Button>
                </Link>
            </div>
        </EmptyState>

        <div v-else class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-navy-800">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Smlouva</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden sm:table-cell">Kontakt</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Status</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden md:table-cell">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-700">
                    <tr
                        v-for="contract in contracts.data"
                        :key="contract.id"
                        class="hover:bg-navy-800 cursor-pointer"
                        @click="router.visit(`/contracts/${contract.id}`)"
                    >
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-white">{{ contract.template?.name || 'Bez šablony' }}</div>
                            <div class="text-xs text-gray-500">#{{ contract.id }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden sm:table-cell">
                            {{ contract.contact?.name || '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <Badge :color="statusColors[contract.status] as any" size="sm">
                                {{ statusLabels[contract.status] || contract.status }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-right hidden md:table-cell">
                            {{ formatDate(contract.created_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-navy-700">
                <Pagination :links="contracts.links" />
            </div>
        </div>

        <!-- Upload template modal -->
        <Modal :show="showUpload" @close="showUpload = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Nahrát šablonu smlouvy</h3>
                <div class="space-y-4">
                    <TextInput v-model="uploadName" label="Název šablony" required />
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Soubor (DOCX nebo MD)</label>
                        <input type="file" accept=".docx,.md,.txt" @change="handleFileChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gold-500/10 file:text-gold-500 hover:file:bg-gold-500/20" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <Button variant="secondary" @click="showUpload = false">Zrušit</Button>
                    <Button @click="submitUpload" :disabled="!uploadName || !uploadFile">Nahrát</Button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
