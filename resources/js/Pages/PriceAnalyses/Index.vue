<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import type { PriceAnalysis, PaginatedData } from '@/types';

defineProps<{
    analyses: PaginatedData<PriceAnalysis>;
}>();

function formatPrice(price: number | null): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ');
}

const typeLabels: Record<string, string> = {
    byt: 'Byt',
    dum: 'Dům',
    pozemek: 'Pozemek',
    komercni: 'Komerční',
};
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Cenové analýzy</h1>
        </template>
        <template #actions>
            <Link href="/price-analyses/create">
                <Button size="sm">Nová analýza</Button>
            </Link>
        </template>

        <EmptyState
            v-if="analyses.data.length === 0"
            title="Žádné cenové analýzy"
            description="Vytvořte první cenovou analýzu pro odhad tržní ceny nemovitosti."
        >
            <Link href="/price-analyses/create">
                <Button size="sm">Vytvořit analýzu</Button>
            </Link>
        </EmptyState>

        <div v-else class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-navy-800">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Adresa</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden sm:table-cell">Typ</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden md:table-cell">Kontakt</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase px-4 py-3">Odhadní cena</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden lg:table-cell">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-700">
                    <tr
                        v-for="a in analyses.data"
                        :key="a.id"
                        class="hover:bg-navy-800 cursor-pointer"
                        @click="$inertia.visit(`/price-analyses/${a.id}`)"
                    >
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-white">{{ a.address }}</div>
                            <div class="text-xs text-gray-500">{{ a.area }} m²</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden sm:table-cell">
                            {{ typeLabels[a.property_type] || a.property_type }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden md:table-cell">
                            {{ a.contact?.name || '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-white text-right">
                            {{ formatPrice(a.estimated_price) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-right hidden lg:table-cell">
                            {{ formatDate(a.created_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-navy-700">
                <Pagination :links="analyses.links" />
            </div>
        </div>
    </AppLayout>
</template>
