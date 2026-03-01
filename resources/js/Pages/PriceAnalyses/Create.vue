<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import type { Contact } from '@/types';

defineProps<{
    contacts: Pick<Contact, 'id' | 'name'>[];
    hasValuoKey: boolean;
}>();

const form = useForm({
    contact_id: '',
    address: '',
    area: '',
    property_type: 'byt',
    condition: 'dobry',
    floor: '',
    ownership: 'osobni',
    estimated_price: '',
});

function submit() {
    form.post('/price-analyses');
}

const typeOptions = [
    { value: 'byt', label: 'Byt' },
    { value: 'dum', label: 'Dům' },
    { value: 'pozemek', label: 'Pozemek' },
    { value: 'komercni', label: 'Komerční' },
];

const conditionOptions = [
    { value: 'novostavba', label: 'Novostavba' },
    { value: 'dobry', label: 'Dobrý stav' },
    { value: 'spatny', label: 'Špatný stav' },
];

const ownershipOptions = [
    { value: 'osobni', label: 'Osobní' },
    { value: 'druzstevni', label: 'Družstevní' },
    { value: 'statni', label: 'Státní' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/price-analyses" class="text-gray-500 hover:text-gray-300">Cenové analýzy</Link>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold font-serif text-white">Nová analýza</h1>
            </div>
        </template>

        <div class="max-w-2xl">
            <div v-if="!hasValuoKey" class="bg-yellow-900/20 border border-yellow-700 text-yellow-400 px-4 py-3 rounded-lg text-sm mb-6">
                Valuo API klíč není nastaven. Odhad ceny bude manuální. Nastavte <code>VALUO_API_KEY</code> v .env pro automatický odhad.
            </div>

            <form @submit.prevent="submit" class="bg-navy-900 rounded-xl border border-navy-700 p-6 space-y-5">
                <SelectInput
                    v-model="form.contact_id"
                    label="Kontakt"
                    :options="contacts.map(c => ({ value: String(c.id), label: c.name }))"
                    placeholder="Bez přiřazení"
                    :error="form.errors.contact_id"
                />

                <TextInput v-model="form.address" label="Adresa nemovitosti" :error="form.errors.address" required placeholder="Ulice, město, PSČ" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <TextInput v-model="form.area" label="Plocha (m²)" type="number" :error="form.errors.area" required />
                    <SelectInput v-model="form.property_type" label="Typ nemovitosti" :options="typeOptions" :error="form.errors.property_type" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <SelectInput v-model="form.condition" label="Stav" :options="conditionOptions" :error="form.errors.condition" required />
                    <TextInput v-model="form.floor" label="Patro" type="number" :error="form.errors.floor" />
                    <SelectInput v-model="form.ownership" label="Vlastnictví" :options="ownershipOptions" :error="form.errors.ownership" />
                </div>

                <TextInput
                    v-if="!hasValuoKey"
                    v-model="form.estimated_price"
                    label="Odhadní cena (Kč)"
                    type="number"
                    :error="form.errors.estimated_price"
                    placeholder="Manuální zadání ceny"
                />

                <div class="flex justify-end gap-3 pt-2">
                    <Link href="/price-analyses">
                        <Button variant="secondary" type="button">Zrušit</Button>
                    </Link>
                    <Button type="submit" :loading="form.processing">Vytvořit analýzu</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
