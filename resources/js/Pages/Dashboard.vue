<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatsCard from '@/Components/StatsCard.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import type { Task, Contact, ActivityLog, Property, PropertyEvent } from '@/types';
import { TAG_LABELS, TAG_COLORS, PRIORITY_LABELS, PRIORITY_COLORS, PROPERTY_STATUS_LABELS, PROPERTY_STATUS_COLORS, EVENT_TYPE_LABELS } from '@/types';

defineProps<{
    stats: {
        activeContacts: number;
        hotOpportunities: number;
        todayTasks: number;
        totalContacts: number;
    };
    todayTasks: Task[];
    hotContacts: Contact[];
    recentActivity: ActivityLog[];
    propertyStats: {
        activeListings: number;
        inAdvertising: number;
        reserved: number;
    };
    recentProperties: Property[];
    todayEvents: PropertyEvent[];
}>();

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function formatTime(dateStr: string): string {
    return new Date(dateStr).toLocaleTimeString('cs-CZ', { hour: '2-digit', minute: '2-digit' });
}

function formatPrice(price: number | null): string {
    if (!price) return '—';
    return new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'CZK', maximumFractionDigits: 0 }).format(price);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Dashboard</h1>
        </template>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatsCard label="Aktivní kontakty" :value="stats.activeContacts" color="blue" />
            <StatsCard label="Horké příležitosti" :value="stats.hotOpportunities" color="red" />
            <StatsCard label="Úkoly dnes" :value="stats.todayTasks" color="yellow" />
            <StatsCard label="Kontaktů celkem" :value="stats.totalContacts" color="purple" />
        </div>

        <!-- Property Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <StatsCard label="Aktivní nabídky" :value="propertyStats.activeListings" color="gold" />
            <StatsCard label="V inzerci" :value="propertyStats.inAdvertising" color="green" />
            <StatsCard label="Rezervované" :value="propertyStats.reserved" color="purple" />
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Today's tasks -->
            <div class="lg:col-span-2">
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-white">Úkoly na dnes</h2>
                        <Link href="/tasks" class="text-sm text-gold-500 hover:text-gold-400">Zobrazit vše</Link>
                    </div>
                    <div v-if="todayTasks.length === 0" class="text-sm text-gray-500 py-4 text-center">
                        Žádné úkoly na dnes. Skvělá práce!
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="task in todayTasks"
                            :key="task.id"
                            class="flex items-center gap-3 p-3 rounded-lg bg-navy-800"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ task.title }}</p>
                                <Link
                                    v-if="task.contact"
                                    :href="`/contacts/${task.contact.id}`"
                                    class="text-xs text-gold-500 hover:underline"
                                >
                                    {{ task.contact.name }}
                                </Link>
                            </div>
                            <Badge :color="PRIORITY_COLORS[task.priority] as any" size="sm">
                                {{ PRIORITY_LABELS[task.priority] }}
                            </Badge>
                            <Badge v-if="task.is_auto" color="purple" size="sm">AUTO</Badge>
                        </div>
                    </div>
                </div>

                <!-- Hot opportunities -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5 mt-6">
                    <h2 class="font-semibold text-white mb-4">Horké příležitosti</h2>
                    <div v-if="hotContacts.length === 0" class="text-sm text-gray-500 py-4 text-center">
                        Žádné horké příležitosti.
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="contact in hotContacts"
                            :key="contact.id"
                            class="p-3 rounded-lg bg-navy-800"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <Link
                                    :href="`/contacts/${contact.id}`"
                                    class="text-sm font-medium text-white hover:text-gold-500"
                                >
                                    {{ contact.name }}
                                </Link>
                                <Badge :color="TAG_COLORS[contact.tag || ''] as any" size="sm">
                                    {{ TAG_LABELS[contact.tag || ''] || contact.tag }}
                                </Badge>
                            </div>
                            <p v-if="contact.process" class="text-xs text-gray-500 mb-2">
                                {{ contact.process.name }} — Krok {{ contact.current_step }}/{{ contact.process.steps?.length }}
                            </p>
                            <ProgressBar
                                v-if="contact.process?.steps?.length"
                                :value="((contact.current_step || 0) / contact.process.steps.length) * 100"
                                :color="contact.process.color"
                                show-label
                            />
                        </div>
                    </div>
                </div>

                <!-- Today's Events -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-white">Události dnes</h2>
                        <Link href="/properties" class="text-sm text-gold-500 hover:text-gold-400">Nemovitosti</Link>
                    </div>
                    <div v-if="todayEvents.length === 0" class="text-sm text-gray-500 py-4 text-center">
                        Dnes nejsou naplánované žádné události.
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="event in todayEvents"
                            :key="event.id"
                            class="flex items-start gap-3 p-3 rounded-lg bg-navy-800"
                        >
                            <div class="flex-shrink-0 w-12 text-center">
                                <span class="text-sm font-bold text-gold-500">{{ formatTime(event.starts_at) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ event.title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <Badge color="blue" size="sm">{{ EVENT_TYPE_LABELS[event.type] }}</Badge>
                                    <Link
                                        v-if="event.property"
                                        :href="`/properties/${event.property_id}`"
                                        class="text-xs text-gold-500 hover:underline truncate"
                                    >
                                        {{ event.property.name }}
                                    </Link>
                                </div>
                                <p v-if="event.contact" class="text-xs text-gray-400 mt-0.5">
                                    {{ event.contact.name }}
                                </p>
                                <p v-if="event.location" class="text-xs text-gray-500 mt-0.5">
                                    {{ event.location }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="space-y-6">
                <!-- Recent Properties -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-white">Poslední nemovitosti</h2>
                        <Link href="/properties" class="text-sm text-gold-500 hover:text-gold-400">Zobrazit vše</Link>
                    </div>
                    <div v-if="recentProperties.length === 0" class="text-sm text-gray-500 py-4 text-center">
                        Zatím žádné nemovitosti.
                    </div>
                    <div v-else class="space-y-3">
                        <Link
                            v-for="property in recentProperties"
                            :key="property.id"
                            :href="`/properties/${property.id}`"
                            class="flex items-center gap-3 p-3 rounded-lg bg-navy-800 hover:bg-navy-700 transition-colors block"
                        >
                            <div class="flex-shrink-0 w-14 h-14 rounded-lg bg-navy-700 overflow-hidden">
                                <img
                                    v-if="property.primary_photo?.url"
                                    :src="property.primary_photo.url"
                                    :alt="property.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ property.name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge :color="(PROPERTY_STATUS_COLORS[property.status] as any) || 'gray'" size="sm">
                                        {{ PROPERTY_STATUS_LABELS[property.status] }}
                                    </Badge>
                                    <span class="text-xs text-gold-500 font-medium">{{ formatPrice(property.price) }}</span>
                                </div>
                                <p v-if="property.contact" class="text-xs text-gray-400 mt-0.5">
                                    {{ property.contact.name }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Recent activity -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h2 class="font-semibold text-white mb-4">Poslední aktivity</h2>
                    <div v-if="recentActivity.length === 0" class="text-sm text-gray-500 py-4 text-center">
                        Zatím žádné aktivity.
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="activity in recentActivity"
                            :key="activity.id"
                            class="border-l-2 border-navy-700 pl-3 py-1"
                        >
                            <p class="text-sm text-white">{{ activity.description }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <Link
                                    v-if="activity.contact"
                                    :href="`/contacts/${activity.contact.id}`"
                                    class="text-xs text-gold-500 hover:underline"
                                >
                                    {{ activity.contact.name }}
                                </Link>
                                <span class="text-xs text-gray-400">{{ formatDate(activity.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
