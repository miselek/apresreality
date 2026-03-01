<script setup lang="ts">
import { ref } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import {
    type Property, type Contact,
    PROPERTY_STATUS_LABELS, PROPERTY_STATUS_COLORS, PROPERTY_TYPE_LABELS,
    PROPERTY_PRICE_TYPE_LABELS, INTEREST_TYPE_LABELS, INTEREST_TYPE_COLORS,
    EVENT_TYPE_LABELS,
} from '@/types';

const props = defineProps<{
    property: Property;
    stats: {
        interest_count: number;
        visit_count: number;
        reservation_count: number;
        event_count: number;
        upcoming_events: number;
    };
    contacts: Pick<Contact, 'id' | 'name' | 'phone'>[];
}>();

// Status pipeline
const allStatuses = ['nabor', 'priprava', 'inzerce', 'prohlidky', 'rezervace', 'smlouva', 'prodano'] as const;
const currentStatusOrder = allStatuses.indexOf(props.property.status as any);

// Advance status
const showAdvanceConfirm = ref(false);
function advanceStatus() {
    router.post(`/nemovitosti/${props.property.id}/advance`, {}, {
        preserveScroll: true,
        onSuccess: () => { showAdvanceConfirm.value = false; },
    });
}

// Delete confirm
const showDeleteConfirm = ref(false);
function deleteProperty() {
    router.delete(`/nemovitosti/${props.property.id}`);
}

// Photo upload
const photoInput = ref<HTMLInputElement | null>(null);
function uploadPhotos(event: Event) {
    const files = (event.target as HTMLInputElement).files;
    if (!files || files.length === 0) return;
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('photos[]', files[i]);
    }
    router.post(`/nemovitosti/${props.property.id}/photos`, formData, {
        preserveScroll: true,
        onSuccess: () => {
            if (photoInput.value) photoInput.value.value = '';
        },
    });
}

function deletePhoto(photoId: number) {
    router.delete(`/nemovitosti/${props.property.id}/photos/${photoId}`, { preserveScroll: true });
}

function setPrimary(photoId: number) {
    router.post(`/nemovitosti/${props.property.id}/photos/${photoId}/primary`, {}, { preserveScroll: true });
}

// Interest form
const showInterestModal = ref(false);
const interestForm = useForm({
    contact_id: '',
    type: 'zajemce',
    note: '',
    visited_at: '',
});

function addInterest() {
    interestForm.post(`/nemovitosti/${props.property.id}/interests`, {
        preserveScroll: true,
        onSuccess: () => {
            showInterestModal.value = false;
            interestForm.reset();
        },
    });
}

function removeInterest(interestId: number) {
    router.delete(`/nemovitosti/${props.property.id}/interests/${interestId}`, { preserveScroll: true });
}

// Event form
const showEventModal = ref(false);
const eventForm = useForm({
    title: '',
    type: 'prohlidka',
    contact_id: '',
    starts_at: '',
    ends_at: '',
    location: '',
    description: '',
    notes: '',
});

function addEvent() {
    eventForm.post(`/nemovitosti/${props.property.id}/events`, {
        preserveScroll: true,
        onSuccess: () => {
            showEventModal.value = false;
            eventForm.reset();
        },
    });
}

function deleteEvent(eventId: number) {
    router.delete(`/nemovitosti/${props.property.id}/events/${eventId}`, { preserveScroll: true });
}

function completeEvent(eventId: number) {
    router.post(`/nemovitosti/${props.property.id}/events/${eventId}/complete`, {}, { preserveScroll: true });
}

function formatPrice(price: number | null | undefined): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('cs-CZ');
}

function formatDateTime(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleString('cs-CZ', { day: 'numeric', month: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

const contactOptions = props.contacts.map(c => ({ value: String(c.id), label: `${c.name} (${c.phone || ''})` }));
const interestTypeOptions = Object.entries(INTEREST_TYPE_LABELS).map(([value, label]) => ({ value, label }));
const eventTypeOptions = Object.entries(EVENT_TYPE_LABELS).map(([value, label]) => ({ value, label }));

const upcomingEvents = (props.property.events || []).filter(e => !e.is_completed && new Date(e.starts_at) >= new Date());
const pastEvents = (props.property.events || []).filter(e => e.is_completed || new Date(e.starts_at) < new Date());
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2 min-w-0">
                <Link href="/nemovitosti" class="hidden sm:inline text-gray-500 hover:text-gray-300 shrink-0">Nemovitosti</Link>
                <span class="hidden sm:inline text-gray-500 shrink-0">/</span>
                <h1 class="text-lg sm:text-xl font-bold text-white font-serif truncate">{{ property.name }}</h1>
            </div>
        </template>
        <template #actions>
            <div class="flex items-center gap-2">
                <Link :href="`/nemovitosti/${property.id}/edit`">
                    <Button variant="secondary" size="sm">Upravit</Button>
                </Link>
                <Button size="sm" @click="showDeleteConfirm = true" variant="danger">Smazat</Button>
            </div>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Photo Gallery -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-white">Fotogalerie</h2>
                        <div>
                            <input ref="photoInput" type="file" multiple accept="image/*" class="hidden" @change="uploadPhotos" />
                            <Button size="sm" variant="secondary" @click="photoInput?.click()">Nahrát fotky</Button>
                        </div>
                    </div>

                    <div v-if="property.photos && property.photos.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div
                            v-for="photo in property.photos"
                            :key="photo.id"
                            class="relative group aspect-video rounded-lg overflow-hidden bg-navy-800"
                        >
                            <img :src="photo.url" :alt="photo.caption || ''" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button
                                    @click.prevent="setPrimary(photo.id)"
                                    class="p-1.5 rounded-lg bg-navy-900/80 text-gold-500 hover:bg-navy-900 text-xs"
                                    :class="{ 'ring-2 ring-gold-500': photo.is_primary }"
                                >
                                    ★
                                </button>
                                <button
                                    @click.prevent="deletePhoto(photo.id)"
                                    class="p-1.5 rounded-lg bg-navy-900/80 text-red-400 hover:bg-navy-900 text-xs"
                                >
                                    ✕
                                </button>
                            </div>
                            <div v-if="photo.is_primary" class="absolute top-2 left-2">
                                <Badge color="gold" size="sm">Hlavní</Badge>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 text-sm">
                        Zatím žádné fotky. Nahrajte první fotku.
                    </div>
                </div>

                <!-- Workflow Progress -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-white">Průběh</h2>
                        <Button
                            v-if="property.status !== 'prodano' && property.status !== 'archiv'"
                            size="sm"
                            @click="showAdvanceConfirm = true"
                        >
                            Posunout stav →
                        </Button>
                    </div>

                    <!-- Status Pipeline -->
                    <div class="flex items-center gap-1 mb-4 overflow-x-auto pb-2">
                        <template v-for="(status, index) in allStatuses" :key="status">
                            <div
                                :class="[
                                    'flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors',
                                    index < currentStatusOrder ? 'bg-green-900/20 text-green-400' :
                                    index === currentStatusOrder ? 'bg-gold-500/20 text-gold-500 ring-1 ring-gold-500/50' :
                                    'bg-navy-800 text-gray-500'
                                ]"
                            >
                                <span v-if="index < currentStatusOrder" class="text-green-400">✓</span>
                                <span v-else-if="index === currentStatusOrder" class="w-2 h-2 rounded-full bg-gold-500" />
                                {{ PROPERTY_STATUS_LABELS[status] }}
                            </div>
                            <svg v-if="index < allStatuses.length - 1" class="w-4 h-4 text-navy-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </template>
                    </div>

                    <ProgressBar :value="property.progress || 0" :show-label="true" />
                </div>

                <!-- Calendar & Events -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-white">Kalendář & Schůzky</h2>
                        <Button size="sm" variant="secondary" @click="showEventModal = true">Nová událost</Button>
                    </div>

                    <!-- Upcoming events -->
                    <div v-if="upcomingEvents.length > 0" class="space-y-3 mb-4">
                        <h3 class="text-sm font-medium text-gold-500">Nadcházející</h3>
                        <div
                            v-for="event in upcomingEvents"
                            :key="event.id"
                            class="flex items-center justify-between gap-3 p-3 rounded-lg bg-navy-800"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-white">{{ event.title }}</span>
                                    <Badge :color="event.type === 'prohlidka' ? 'yellow' : event.type === 'foceni' ? 'purple' : 'blue'" size="sm">
                                        {{ EVENT_TYPE_LABELS[event.type] }}
                                    </Badge>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ formatDateTime(event.starts_at) }}
                                    <span v-if="event.location"> · {{ event.location }}</span>
                                    <span v-if="event.contact"> · {{ event.contact.name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="completeEvent(event.id)" class="p-1.5 rounded text-green-400 hover:bg-navy-700" title="Dokončit">✓</button>
                                <button @click="deleteEvent(event.id)" class="p-1.5 rounded text-red-400 hover:bg-navy-700" title="Smazat">✕</button>
                            </div>
                        </div>
                    </div>

                    <!-- Past events -->
                    <div v-if="pastEvents.length > 0" class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-500">Minulé</h3>
                        <div
                            v-for="event in pastEvents"
                            :key="event.id"
                            class="flex items-center justify-between gap-3 p-3 rounded-lg bg-navy-800/50"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400" :class="{ 'line-through': event.is_completed }">{{ event.title }}</span>
                                    <Badge :color="event.is_completed ? 'green' : 'gray'" size="sm">
                                        {{ event.is_completed ? 'Dokončeno' : EVENT_TYPE_LABELS[event.type] }}
                                    </Badge>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ formatDateTime(event.starts_at) }}</div>
                            </div>
                            <button @click="deleteEvent(event.id)" class="p-1.5 rounded text-gray-500 hover:text-red-400 hover:bg-navy-700">✕</button>
                        </div>
                    </div>

                    <div v-if="upcomingEvents.length === 0 && pastEvents.length === 0" class="text-center py-6 text-gray-500 text-sm">
                        Žádné naplánované události.
                    </div>
                </div>

                <!-- Interested Parties -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-white">Zájemci</h2>
                        <Button size="sm" variant="secondary" @click="showInterestModal = true">Přidat zájemce</Button>
                    </div>

                    <!-- Stats row -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-navy-800 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-blue-400">{{ stats.interest_count }}</div>
                            <div class="text-xs text-gray-400">Celkem</div>
                        </div>
                        <div class="bg-navy-800 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-yellow-400">{{ stats.visit_count }}</div>
                            <div class="text-xs text-gray-400">Návštěvy</div>
                        </div>
                        <div class="bg-navy-800 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-green-400">{{ stats.reservation_count }}</div>
                            <div class="text-xs text-gray-400">Rezervace</div>
                        </div>
                    </div>

                    <div v-if="property.interests && property.interests.length > 0" class="divide-y divide-navy-700">
                        <div
                            v-for="interest in property.interests"
                            :key="interest.id"
                            class="flex items-center justify-between gap-3 py-3"
                        >
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <Link
                                        v-if="interest.contact"
                                        :href="`/contacts/${interest.contact.id}`"
                                        class="text-sm font-medium text-gold-500 hover:text-gold-400"
                                    >
                                        {{ interest.contact.name }}
                                    </Link>
                                    <Badge :color="(INTEREST_TYPE_COLORS[interest.type] as any)" size="sm">
                                        {{ INTEREST_TYPE_LABELS[interest.type] }}
                                    </Badge>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <span v-if="interest.note">{{ interest.note }}</span>
                                    <span v-if="interest.visited_at"> · Návštěva: {{ formatDate(interest.visited_at) }}</span>
                                </div>
                            </div>
                            <button @click="removeInterest(interest.id)" class="p-1.5 rounded text-gray-500 hover:text-red-400 hover:bg-navy-700">✕</button>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-gray-500 text-sm">
                        Zatím žádní zájemci.
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">

                <!-- Price Card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-gray-400">Cena</h3>
                        <Badge :color="property.price_type === 'prodej' ? 'gold' : 'blue'" size="sm">
                            {{ PROPERTY_PRICE_TYPE_LABELS[property.price_type] }}
                        </Badge>
                    </div>
                    <p class="text-2xl font-bold text-gold-500">{{ formatPrice(property.price) }}</p>
                    <p v-if="property.price && property.area" class="text-sm text-gray-400 mt-1">
                        {{ formatPrice(Math.round(Number(property.price) / Number(property.area))) }} / m²
                    </p>
                    <div v-if="property.commission_computed" class="mt-3 pt-3 border-t border-navy-700">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Provize</span>
                            <span class="text-white font-medium">
                                {{ formatPrice(property.commission_computed) }}
                                <span v-if="property.commission_percent" class="text-gray-500 text-xs">({{ property.commission_percent }}%)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ad Budget Card -->
                <div v-if="property.ad_budget" class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Rozpočet na reklamu</h3>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-400">Rozpočet</span>
                        <span class="text-white font-medium">{{ formatPrice(property.ad_budget) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-400">Utraceno</span>
                        <span class="text-white font-medium">{{ formatPrice(property.ad_spent) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-3">
                        <span class="text-gray-400">Zbývá</span>
                        <span class="font-medium" :class="property.ad_remaining && property.ad_remaining > 0 ? 'text-green-400' : 'text-red-400'">
                            {{ formatPrice(property.ad_remaining) }}
                        </span>
                    </div>
                    <ProgressBar
                        :value="property.ad_budget ? (Number(property.ad_spent || 0) / Number(property.ad_budget)) * 100 : 0"
                        :show-label="true"
                        :color="Number(property.ad_spent || 0) > Number(property.ad_budget) ? '#EF4444' : '#D4A843'"
                    />
                </div>

                <!-- Stats Card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Statistiky</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Dny na trhu</span>
                            <span class="text-white font-medium">{{ property.days_on_market ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Zájemci</span>
                            <span class="text-white font-medium">{{ stats.interest_count }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Návštěvy</span>
                            <span class="text-white font-medium">{{ stats.visit_count }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Nadcházející události</span>
                            <span class="text-white font-medium">{{ stats.upcoming_events }}</span>
                        </div>
                    </div>
                </div>

                <!-- Owner Card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Vlastník</h3>
                    <div v-if="property.contact">
                        <Link :href="`/contacts/${property.contact.id}`" class="text-sm font-medium text-gold-500 hover:text-gold-400">
                            {{ property.contact.name }}
                        </Link>
                        <p v-if="property.contact.phone" class="text-xs text-gray-400 mt-1">{{ property.contact.phone }}</p>
                        <p v-if="property.contact.email" class="text-xs text-gray-400">{{ property.contact.email }}</p>
                    </div>
                    <p v-else class="text-sm text-gray-500">Nepřiřazeno</p>
                </div>

                <!-- Price Analysis Card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Cenová analýza</h3>
                    <div v-if="property.price_analysis">
                        <Link :href="`/price-analyses/${property.price_analysis.id}`" class="text-sm font-medium text-gold-500 hover:text-gold-400">
                            {{ property.price_analysis.address }}
                        </Link>
                        <p v-if="property.price_analysis.estimated_price" class="text-xs text-gray-400 mt-1">
                            Odhad: {{ formatPrice(Number(property.price_analysis.estimated_price)) }}
                        </p>
                    </div>
                    <div v-else>
                        <p class="text-sm text-gray-500 mb-2">Nepřiřazena</p>
                        <Link href="/price-analyses/create">
                            <Button size="sm" variant="secondary">Vytvořit analýzu</Button>
                        </Link>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Informace</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Typ</span>
                            <span class="text-white">{{ PROPERTY_TYPE_LABELS[property.property_type] }}</span>
                        </div>
                        <div v-if="property.disposition" class="flex justify-between">
                            <span class="text-gray-400">Dispozice</span>
                            <span class="text-white">{{ property.disposition }}</span>
                        </div>
                        <div v-if="property.area" class="flex justify-between">
                            <span class="text-gray-400">Plocha</span>
                            <span class="text-white">{{ property.area }} m²</span>
                        </div>
                        <div v-if="property.land_area" class="flex justify-between">
                            <span class="text-gray-400">Pozemek</span>
                            <span class="text-white">{{ property.land_area }} m²</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Adresa</span>
                            <span class="text-white text-right">{{ property.address }}<br v-if="property.city" /><span class="text-gray-400">{{ property.city }} {{ property.zip }}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Landing Page Link -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Landing page</h3>
                    <a
                        :href="`/nemovitosti/${property.id}/landing`"
                        target="_blank"
                        class="text-sm text-gold-500 hover:text-gold-400 underline"
                    >
                        Otevřít veřejnou stránku →
                    </a>
                </div>
            </div>
        </div>

        <!-- Advance Status Confirm -->
        <ConfirmDialog
            :show="showAdvanceConfirm"
            title="Posunout stav"
            :message="`Opravdu chcete posunout stav nemovitosti?`"
            confirm-text="Posunout"
            @confirm="advanceStatus"
            @cancel="showAdvanceConfirm = false"
        />

        <!-- Delete Confirm -->
        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Smazat nemovitost"
            message="Opravdu chcete smazat tuto nemovitost? Budou smazány i všechny fotky, zájemci a události."
            confirm-text="Smazat"
            variant="danger"
            @confirm="deleteProperty"
            @cancel="showDeleteConfirm = false"
        />

        <!-- Add Interest Modal -->
        <Modal :show="showInterestModal" max-width="md" @close="showInterestModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Přidat zájemce</h3>
                <form @submit.prevent="addInterest" class="space-y-4">
                    <SelectInput v-model="interestForm.contact_id" label="Kontakt" :options="contactOptions" placeholder="Vyberte kontakt" :error="interestForm.errors.contact_id" required />
                    <SelectInput v-model="interestForm.type" label="Typ" :options="interestTypeOptions" :error="interestForm.errors.type" required />
                    <TextArea v-model="interestForm.note" label="Poznámka" :error="interestForm.errors.note" :rows="2" />
                    <TextInput v-model="interestForm.visited_at" label="Datum návštěvy" type="date" :error="interestForm.errors.visited_at" />
                    <div class="flex justify-end gap-3">
                        <Button variant="secondary" type="button" @click="showInterestModal = false">Zrušit</Button>
                        <Button type="submit" :loading="interestForm.processing">Přidat</Button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Add Event Modal -->
        <Modal :show="showEventModal" max-width="lg" @close="showEventModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Nová událost</h3>
                <form @submit.prevent="addEvent" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <TextInput v-model="eventForm.title" label="Název" :error="eventForm.errors.title" required placeholder="např. Prohlídka s Novák" />
                        <SelectInput v-model="eventForm.type" label="Typ" :options="eventTypeOptions" :error="eventForm.errors.type" required />
                        <TextInput v-model="eventForm.starts_at" label="Začátek" type="datetime-local" :error="eventForm.errors.starts_at" required />
                        <TextInput v-model="eventForm.ends_at" label="Konec" type="datetime-local" :error="eventForm.errors.ends_at" />
                        <SelectInput v-model="eventForm.contact_id" label="Kontakt" :options="contactOptions" placeholder="Volitelné" :error="eventForm.errors.contact_id" />
                        <TextInput v-model="eventForm.location" label="Místo" :error="eventForm.errors.location" placeholder="Adresa schůzky" />
                    </div>
                    <TextArea v-model="eventForm.notes" label="Poznámky" :error="eventForm.errors.notes" :rows="2" />
                    <div class="flex justify-end gap-3">
                        <Button variant="secondary" type="button" @click="showEventModal = false">Zrušit</Button>
                        <Button type="submit" :loading="eventForm.processing">Vytvořit</Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
