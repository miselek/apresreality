<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import SearchInput from '@/Components/SearchInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/EmptyState.vue';
import {
    type Property, type PaginatedData,
    PROPERTY_TYPE_LABELS, PROPERTY_STATUS_LABELS, PROPERTY_STATUS_COLORS,
    PROPERTY_PRICE_TYPE_LABELS,
} from '@/types';

const props = defineProps<{
    properties: PaginatedData<Property>;
    filters: Record<string, string>;
    statusCounts: Record<string, number>;
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const propertyType = ref(props.filters.property_type || '');
const priceType = ref(props.filters.price_type || '');

function applyFilters() {
    router.get('/nemovitosti', {
        search: search.value || undefined,
        status: status.value || undefined,
        property_type: propertyType.value || undefined,
        price_type: priceType.value || undefined,
    }, { preserveState: true, replace: true });
}

watch([status, propertyType, priceType], applyFilters);

function onSearch(val: string) {
    search.value = val;
    applyFilters();
}

function formatPrice(price: number | null): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}

const statusOptions = Object.entries(PROPERTY_STATUS_LABELS).map(([value, label]) => ({ value, label }));
const typeOptions = Object.entries(PROPERTY_TYPE_LABELS).map(([value, label]) => ({ value, label }));
const priceTypeOptions = Object.entries(PROPERTY_PRICE_TYPE_LABELS).map(([value, label]) => ({ value, label }));
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-white font-serif">Nemovitosti</h1>
        </template>
        <template #actions>
            <Link href="/nemovitosti/create">
                <Button>Nová nemovitost</Button>
            </Link>
        </template>

        <!-- Filters -->
        <div class="bg-navy-900 rounded-xl border border-navy-700 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <SearchInput :model-value="search" placeholder="Hledat nemovitost..." @update:model-value="onSearch" />
                <SelectInput v-model="status" :options="statusOptions" placeholder="Všechny stavy" />
                <SelectInput v-model="propertyType" :options="typeOptions" placeholder="Všechny typy" />
                <SelectInput v-model="priceType" :options="priceTypeOptions" placeholder="Prodej/Pronájem" />
            </div>
        </div>

        <!-- Property Cards Grid -->
        <div v-if="properties.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <Link
                v-for="property in properties.data"
                :key="property.id"
                :href="`/nemovitosti/${property.id}`"
                class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden hover:border-navy-600 transition-colors group"
            >
                <!-- Photo -->
                <div class="aspect-video bg-navy-800 overflow-hidden">
                    <img
                        v-if="property.primary_photo"
                        :src="property.primary_photo.url"
                        :alt="property.name"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-sm font-semibold text-white leading-tight">{{ property.name }}</h3>
                        <Badge :color="(PROPERTY_STATUS_COLORS[property.status] as any)" size="sm">
                            {{ PROPERTY_STATUS_LABELS[property.status] }}
                        </Badge>
                    </div>

                    <p class="text-xs text-gray-400 mb-3">{{ property.address }}</p>

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-gold-500">{{ formatPrice(property.price) }}</span>
                        <span v-if="property.disposition" class="text-xs text-gray-400">
                            {{ property.disposition }}
                            <span v-if="property.area"> · {{ property.area }} m²</span>
                        </span>
                    </div>

                    <!-- Progress -->
                    <ProgressBar :value="property.progress || 0" :show-label="true" />

                    <!-- Bottom row -->
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-navy-700">
                        <span v-if="property.commission_percent" class="text-xs text-gray-400">
                            Provize: {{ property.commission_percent }}%
                        </span>
                        <span v-if="property.contact" class="text-xs text-gray-500">
                            {{ property.contact.name }}
                        </span>
                    </div>
                </div>
            </Link>
        </div>

        <div v-else class="bg-navy-900 rounded-xl border border-navy-700 p-8">
            <EmptyState
                title="Žádné nemovitosti"
                description="Zatím nemáte žádné nemovitosti. Přidejte první nemovitost."
            >
                <Link href="/nemovitosti/create">
                    <Button>Přidat nemovitost</Button>
                </Link>
            </EmptyState>
        </div>

        <Pagination :links="properties.links" />
    </AppLayout>
</template>
