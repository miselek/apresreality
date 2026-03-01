<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import type { PriceAnalysis } from '@/types';

const props = defineProps<{
    analysis: PriceAnalysis;
}>();

function formatPrice(price: number | null): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}

function generateReport() {
    router.post(`/price-analyses/${props.analysis.id}/report`);
}

const typeLabels: Record<string, string> = {
    byt: 'Byt', dum: 'Dům', pozemek: 'Pozemek', komercni: 'Komerční',
};

const conditionLabels: Record<string, string> = {
    novostavba: 'Novostavba', dobry: 'Dobrý stav', spatny: 'Špatný stav',
};

const ownershipLabels: Record<string, string> = {
    osobni: 'Osobní', druzstevni: 'Družstevní', statni: 'Státní',
};
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/price-analyses" class="text-gray-500 hover:text-gray-300">Cenové analýzy</Link>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold font-serif text-white">{{ analysis.address }}</h1>
            </div>
        </template>
        <template #actions>
            <div class="flex gap-2">
                <Button size="sm" @click="generateReport">Generovat PDF</Button>
                <a v-if="analysis.report_url" :href="`/price-analyses/${analysis.id}/download`">
                    <Button variant="secondary" size="sm">Stáhnout PDF</Button>
                </a>
            </div>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Property details -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <h3 class="font-semibold text-white mb-4">Údaje o nemovitosti</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Adresa:</span>
                            <span class="ml-2 font-medium">{{ analysis.address }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Typ:</span>
                            <span class="ml-2">{{ typeLabels[analysis.property_type] || analysis.property_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Plocha:</span>
                            <span class="ml-2">{{ analysis.area }} m²</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Stav:</span>
                            <span class="ml-2">{{ conditionLabels[analysis.condition] || analysis.condition }}</span>
                        </div>
                        <div v-if="analysis.floor">
                            <span class="text-gray-500">Patro:</span>
                            <span class="ml-2">{{ analysis.floor }}</span>
                        </div>
                        <div v-if="analysis.ownership">
                            <span class="text-gray-500">Vlastnictví:</span>
                            <span class="ml-2">{{ ownershipLabels[analysis.ownership] || analysis.ownership }}</span>
                        </div>
                    </div>
                </div>

                <!-- Comparables -->
                <div v-if="analysis.comparables?.length" class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <h3 class="font-semibold text-white mb-4">Srovnávací nemovitosti</h3>
                    <div class="space-y-3">
                        <div v-for="(comp, i) in analysis.comparables" :key="i" class="border border-navy-700 rounded-lg p-3">
                            <pre class="text-xs text-gray-400 whitespace-pre-wrap">{{ JSON.stringify(comp, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price card -->
            <div class="space-y-6">
                <div class="bg-gold-500/10 rounded-xl border border-gold-600 p-6 text-center">
                    <p class="text-sm text-gold-500 font-medium mb-1">Odhadní cena</p>
                    <p class="text-3xl font-bold text-gold-500">{{ formatPrice(analysis.estimated_price) }}</p>
                    <p v-if="analysis.area && analysis.estimated_price" class="text-sm text-gold-400 mt-2">
                        {{ formatPrice(Math.round(Number(analysis.estimated_price) / Number(analysis.area))) }} / m²
                    </p>
                </div>

                <div v-if="analysis.contact" class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="font-semibold text-white mb-2">Kontakt</h3>
                    <Link :href="`/contacts/${analysis.contact.id}`" class="text-sm text-gold-500 hover:underline">
                        {{ analysis.contact.name }}
                    </Link>
                </div>

                <div v-if="analysis.report_url" class="bg-green-900/20 rounded-xl border border-green-700 p-4 text-center">
                    <Badge color="green">PDF vygenerováno</Badge>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
