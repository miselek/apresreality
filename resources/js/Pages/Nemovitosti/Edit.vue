<script setup lang="ts">
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { type Property, type Contact, type PriceAnalysis, PROPERTY_TYPE_LABELS, PROPERTY_PRICE_TYPE_LABELS } from '@/types';

const props = defineProps<{
    property: Property;
    contacts: Pick<Contact, 'id' | 'name' | 'phone'>[];
    priceAnalyses: Pick<PriceAnalysis, 'id' | 'address' | 'estimated_price'>[];
}>();

const form = useForm({
    name: props.property.name,
    address: props.property.address,
    city: props.property.city || '',
    zip: props.property.zip || '',
    gps_lat: props.property.gps_lat ? String(props.property.gps_lat) : '',
    gps_lng: props.property.gps_lng ? String(props.property.gps_lng) : '',
    property_type: props.property.property_type,
    disposition: props.property.disposition || '',
    area: props.property.area ? String(props.property.area) : '',
    land_area: props.property.land_area ? String(props.property.land_area) : '',
    price: props.property.price ? String(props.property.price) : '',
    price_type: props.property.price_type,
    commission_percent: props.property.commission_percent ? String(props.property.commission_percent) : '',
    commission_amount: props.property.commission_amount ? String(props.property.commission_amount) : '',
    ad_budget: props.property.ad_budget ? String(props.property.ad_budget) : '',
    ad_spent: props.property.ad_spent ? String(props.property.ad_spent) : '0',
    description: props.property.description || '',
    contact_id: props.property.contact_id ? String(props.property.contact_id) : '',
    price_analysis_id: props.property.price_analysis_id ? String(props.property.price_analysis_id) : '',
    notes: props.property.notes || '',
});

const typeOptions = Object.entries(PROPERTY_TYPE_LABELS).map(([value, label]) => ({ value, label }));
const priceTypeOptions = Object.entries(PROPERTY_PRICE_TYPE_LABELS).map(([value, label]) => ({ value, label }));
const contactOptions = props.contacts.map(c => ({ value: String(c.id), label: `${c.name} (${c.phone || ''})` }));
const paOptions = props.priceAnalyses.map(pa => ({ value: String(pa.id), label: `${pa.address} — ${pa.estimated_price ? new Intl.NumberFormat('cs-CZ').format(Number(pa.estimated_price)) + ' Kč' : 'bez odhadu'}` }));

const commissionPreview = computed(() => {
    if (form.price && form.commission_percent) {
        const val = Number(form.price) * Number(form.commission_percent) / 100;
        return new Intl.NumberFormat('cs-CZ').format(Math.round(val)) + ' Kč';
    }
    return null;
});

function submit() {
    form.put(`/nemovitosti/${props.property.id}`);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/nemovitosti" class="text-gray-500 hover:text-gray-300">Nemovitosti</Link>
                <span class="text-gray-500">/</span>
                <Link :href="`/nemovitosti/${property.id}`" class="text-gray-500 hover:text-gray-300">{{ property.name }}</Link>
                <span class="text-gray-500">/</span>
                <h1 class="text-xl font-bold text-white font-serif">Upravit</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl">
            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Základní informace</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.name" label="Název nemovitosti" :error="form.errors.name" required />
                    <SelectInput v-model="form.property_type" label="Typ nemovitosti" :options="typeOptions" :error="form.errors.property_type" required />
                    <TextInput v-model="form.disposition" label="Dispozice" :error="form.errors.disposition" />
                    <TextInput v-model="form.address" label="Adresa" :error="form.errors.address" required />
                    <TextInput v-model="form.city" label="Město" :error="form.errors.city" />
                    <TextInput v-model="form.zip" label="PSČ" :error="form.errors.zip" />
                </div>
            </div>

            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Plochy</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.area" label="Užitná plocha (m²)" type="number" :error="form.errors.area" />
                    <TextInput v-model="form.land_area" label="Pozemek (m²)" type="number" :error="form.errors.land_area" />
                </div>
            </div>

            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Finance</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.price" label="Cena (Kč)" type="number" :error="form.errors.price" />
                    <SelectInput v-model="form.price_type" label="Typ" :options="priceTypeOptions" :error="form.errors.price_type" required />
                    <div>
                        <TextInput v-model="form.commission_percent" label="Provize (%)" type="number" :error="form.errors.commission_percent" />
                        <p v-if="commissionPreview" class="mt-1 text-xs text-gold-500">≈ {{ commissionPreview }}</p>
                    </div>
                    <TextInput v-model="form.commission_amount" label="Provize fixní (Kč)" type="number" :error="form.errors.commission_amount" />
                    <TextInput v-model="form.ad_budget" label="Rozpočet na reklamu (Kč)" type="number" :error="form.errors.ad_budget" />
                    <TextInput v-model="form.ad_spent" label="Utraceno za reklamu (Kč)" type="number" :error="form.errors.ad_spent" />
                </div>
            </div>

            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Popis</h2>
                <TextArea v-model="form.description" label="Popis nemovitosti" :error="form.errors.description" :rows="5" />
                <div class="mt-4">
                    <TextArea v-model="form.notes" label="Interní poznámky" :error="form.errors.notes" :rows="3" />
                </div>
            </div>

            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Propojení</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <SelectInput v-model="form.contact_id" label="Vlastník (kontakt)" :options="contactOptions" placeholder="Vyberte kontakt" :error="form.errors.contact_id" />
                    <SelectInput v-model="form.price_analysis_id" label="Cenová analýza" :options="paOptions" placeholder="Vyberte analýzu" :error="form.errors.price_analysis_id" />
                </div>
            </div>

            <div class="bg-navy-900 rounded-xl border border-navy-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">GPS souřadnice</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.gps_lat" label="Zeměpisná šířka" type="number" :error="form.errors.gps_lat" />
                    <TextInput v-model="form.gps_lng" label="Zeměpisná délka" type="number" :error="form.errors.gps_lng" />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link :href="`/nemovitosti/${property.id}`">
                    <Button variant="secondary">Zrušit</Button>
                </Link>
                <Button type="submit" :loading="form.processing">Uložit změny</Button>
            </div>
        </form>
    </AppLayout>
</template>
