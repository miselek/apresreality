<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import type { Contract } from '@/types';

const props = defineProps<{
    contract: Contract;
}>();

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

function validateAI() {
    router.post(`/contracts/${props.contract.id}/validate-ai`);
}

function verifyClient() {
    router.post(`/contracts/${props.contract.id}/verify-client`);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/contracts" class="text-gray-500 hover:text-gray-300">Smlouvy</Link>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold font-serif text-white">
                    {{ contract.template?.name || 'Smlouva' }} #{{ contract.id }}
                </h1>
            </div>
        </template>
        <template #actions>
            <div class="flex gap-2">
                <Button variant="secondary" size="sm" @click="verifyClient">Ověřit klienta</Button>
                <Button variant="secondary" size="sm" @click="validateAI">Validovat AI</Button>
                <a :href="`/contracts/${contract.id}/download`">
                    <Button size="sm">Stáhnout PDF</Button>
                </a>
            </div>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Contract data -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-white">Data smlouvy</h3>
                        <Badge :color="statusColors[contract.status] as any">
                            {{ statusLabels[contract.status] || contract.status }}
                        </Badge>
                    </div>

                    <div v-if="contract.data && Object.keys(contract.data).length" class="space-y-2">
                        <div v-for="(value, key) in contract.data" :key="key" class="grid grid-cols-3 gap-2 text-sm">
                            <span class="text-gray-500 font-medium">{{ key }}:</span>
                            <span class="col-span-2 text-white">{{ value }}</span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">Žádná data.</p>
                </div>

                <!-- AI Validation result -->
                <div v-if="contract.validation_result" class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <h3 class="font-semibold text-white mb-3">AI validace</h3>
                    <div class="prose prose-sm max-w-none text-gray-300 whitespace-pre-wrap">
                        {{ contract.validation_result }}
                    </div>
                </div>

                <!-- Client verification -->
                <div v-if="contract.verification_result" class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <h3 class="font-semibold text-white mb-3">Ověření klienta</h3>
                    <div class="space-y-3">
                        <div v-if="contract.verification_result.ares" class="border border-navy-700 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-gray-300 mb-1">ARES</h4>
                            <pre class="text-xs text-gray-400 whitespace-pre-wrap overflow-x-auto">{{ JSON.stringify(contract.verification_result.ares, null, 2) }}</pre>
                        </div>
                        <div v-if="contract.verification_result.isir" class="border border-navy-700 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-gray-300 mb-1">ISIR (Insolvenční rejstřík)</h4>
                            <pre class="text-xs text-gray-400 whitespace-pre-wrap overflow-x-auto">{{ JSON.stringify(contract.verification_result.isir, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="font-semibold text-white mb-3">Informace</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Šablona:</span>
                            <span class="ml-2 text-white">{{ contract.template?.name || '—' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Kontakt:</span>
                            <Link v-if="contract.contact" :href="`/contacts/${contract.contact.id}`" class="ml-2 text-gold-500 hover:underline">
                                {{ contract.contact.name }}
                            </Link>
                        </div>
                        <div>
                            <span class="text-gray-500">Vytvořeno:</span>
                            <span class="ml-2 text-white">{{ new Date(contract.created_at).toLocaleDateString('cs-CZ') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
