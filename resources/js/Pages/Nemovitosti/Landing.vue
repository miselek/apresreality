<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import { type Property, PROPERTY_TYPE_LABELS, PROPERTY_PRICE_TYPE_LABELS } from '@/types';

const props = defineProps<{
    property: Property;
}>();

function formatPrice(price: number | null | undefined): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}

const primaryPhoto = props.property.primary_photo;
const otherPhotos = (props.property.photos || []).filter(p => p.id !== primaryPhoto?.id);
</script>

<template>
    <PublicLayout>
        <div class="max-w-5xl mx-auto px-4 py-8">
            <!-- Hero -->
            <div class="mb-8">
                <!-- Primary Photo -->
                <div v-if="primaryPhoto" class="rounded-xl overflow-hidden mb-4 aspect-[16/9] bg-navy-800">
                    <img :src="primaryPhoto.url" :alt="property.name" class="w-full h-full object-cover" />
                </div>
                <div v-else class="rounded-xl bg-navy-800 aspect-[16/9] flex items-center justify-center mb-4">
                    <svg class="w-16 h-16 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>

                <!-- Photo Grid -->
                <div v-if="otherPhotos.length > 0" class="grid grid-cols-4 gap-2">
                    <div v-for="photo in otherPhotos.slice(0, 4)" :key="photo.id" class="aspect-video rounded-lg overflow-hidden bg-navy-800">
                        <img :src="photo.url" :alt="photo.caption || ''" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main -->
                <div class="lg:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-white font-serif mb-2">{{ property.name }}</h1>
                            <p class="text-gray-400">{{ property.address }}<span v-if="property.city">, {{ property.city }}</span></p>
                        </div>
                        <Badge color="gold">{{ PROPERTY_TYPE_LABELS[property.property_type] }}</Badge>
                    </div>

                    <!-- Key Facts -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                        <div v-if="property.disposition" class="bg-navy-900 rounded-lg p-4 border border-navy-700">
                            <div class="text-xs text-gray-400 mb-1">Dispozice</div>
                            <div class="text-lg font-semibold text-white">{{ property.disposition }}</div>
                        </div>
                        <div v-if="property.area" class="bg-navy-900 rounded-lg p-4 border border-navy-700">
                            <div class="text-xs text-gray-400 mb-1">Plocha</div>
                            <div class="text-lg font-semibold text-white">{{ property.area }} m²</div>
                        </div>
                        <div v-if="property.land_area" class="bg-navy-900 rounded-lg p-4 border border-navy-700">
                            <div class="text-xs text-gray-400 mb-1">Pozemek</div>
                            <div class="text-lg font-semibold text-white">{{ property.land_area }} m²</div>
                        </div>
                        <div class="bg-navy-900 rounded-lg p-4 border border-navy-700">
                            <div class="text-xs text-gray-400 mb-1">Typ</div>
                            <div class="text-lg font-semibold text-white">{{ PROPERTY_PRICE_TYPE_LABELS[property.price_type] }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="property.description" class="mb-8">
                        <h2 class="text-xl font-semibold text-white font-serif mb-4">O nemovitosti</h2>
                        <div class="text-gray-300 leading-relaxed whitespace-pre-line">{{ property.description }}</div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Price Box -->
                    <div class="bg-navy-900 rounded-xl border border-gold-500/30 p-6 mb-6 sticky top-8">
                        <div class="text-sm text-gray-400 mb-2">{{ PROPERTY_PRICE_TYPE_LABELS[property.price_type] }}</div>
                        <div class="text-3xl font-bold text-gold-500 mb-1">{{ formatPrice(property.price) }}</div>
                        <div v-if="property.price && property.area" class="text-sm text-gray-400 mb-6">
                            {{ formatPrice(Math.round(Number(property.price) / Number(property.area))) }} / m²
                        </div>

                        <!-- Contact CTA -->
                        <div class="border-t border-navy-700 pt-4">
                            <h3 class="text-sm font-medium text-white mb-3">Máte zájem? Kontaktujte nás</h3>
                            <div v-if="property.contact" class="space-y-2">
                                <p class="text-sm text-gray-300">{{ property.contact.name }}</p>
                                <a v-if="property.contact.phone" :href="`tel:${property.contact.phone}`" class="flex items-center gap-2 text-gold-500 hover:text-gold-400 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ property.contact.phone }}
                                </a>
                                <a v-if="property.contact.email" :href="`mailto:${property.contact.email}`" class="flex items-center gap-2 text-gold-500 hover:text-gold-400 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ property.contact.email }}
                                </a>
                            </div>
                            <p v-else class="text-sm text-gray-400">
                                Kontaktujte nás na
                                <a href="https://apresreality.cz" class="text-gold-500 hover:text-gold-400">apresreality.cz</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
