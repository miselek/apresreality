<script setup lang="ts">
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import type { Contact, Process } from '@/types';
import { SOURCE_LABELS, TYPE_LABELS, STATUS_LABELS, TAG_LABELS, TAG_COLORS, PRIORITY_LABELS, PRIORITY_COLORS } from '@/types';

const props = defineProps<{
    contact: Contact;
    processes: Process[];
}>();

const showAdvanceConfirm = ref(false);
const showDeleteConfirm = ref(false);
const showAssignProcess = ref(false);

const activityForm = useForm({
    type: 'poznamka',
    description: '',
});

const processForm = useForm({
    process_id: '',
});

function submitActivity() {
    activityForm.post(`/contacts/${props.contact.id}/activity`, {
        onSuccess: () => {
            activityForm.reset();
        },
    });
}

function advanceStep() {
    router.post(`/contacts/${props.contact.id}/advance-step`);
    showAdvanceConfirm.value = false;
}

function assignProcess() {
    processForm.post(`/contacts/${props.contact.id}/assign-process`, {
        onSuccess: () => {
            showAssignProcess.value = false;
            processForm.reset();
        },
    });
}

function deleteContact() {
    router.delete(`/contacts/${props.contact.id}`);
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ', {
        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
}

function formatDateShort(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'short' });
}

const activityTypeOptions = [
    { value: 'poznamka', label: 'Poznámka' },
    { value: 'telefon', label: 'Telefonát' },
    { value: 'email', label: 'E-mail' },
    { value: 'schuzka', label: 'Schůzka' },
    { value: 'sms', label: 'SMS' },
];

const currentStep = props.contact.process?.steps?.find(s => s.order === props.contact.current_step);
const nextStep = props.contact.process?.steps?.find(s => s.order === (props.contact.current_step || 0) + 1);
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link href="/contacts" class="text-gray-500 hover:text-gray-300">Kontakty</Link>
                <span class="text-gray-500">/</span>
                <h1 class="text-xl font-bold font-serif text-white">{{ contact.name }}</h1>
            </div>
        </template>
        <template #actions>
            <div class="flex gap-2">
                <Link :href="`/contacts/${contact.id}/edit`">
                    <Button variant="secondary" size="sm">Upravit</Button>
                </Link>
                <Button variant="danger" size="sm" @click="showDeleteConfirm = true">Smazat</Button>
            </div>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact card -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-white">{{ contact.name }}</h2>
                            <p class="text-sm text-gray-500">{{ TYPE_LABELS[contact.type] }} &middot; {{ SOURCE_LABELS[contact.source] }}</p>
                        </div>
                        <div class="flex gap-2">
                            <Badge :color="contact.status === 'aktivni' ? 'green' : contact.status === 'ceka' ? 'yellow' : 'gray'">
                                {{ STATUS_LABELS[contact.status] }}
                            </Badge>
                            <Badge v-if="contact.tag" :color="TAG_COLORS[contact.tag] as any">
                                {{ TAG_LABELS[contact.tag] }}
                            </Badge>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Telefon:</span>
                            <span class="ml-2 text-white">{{ contact.phone || '—' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">E-mail:</span>
                            <span class="ml-2 text-white">{{ contact.email || '—' }}</span>
                        </div>
                    </div>
                    <div v-if="contact.notes" class="mt-4 pt-4 border-t border-navy-700">
                        <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ contact.notes }}</p>
                    </div>
                </div>

                <!-- Process progress -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-white">Proces</h3>
                        <Button v-if="!contact.process" size="sm" variant="secondary" @click="showAssignProcess = true">
                            Přiřadit proces
                        </Button>
                    </div>

                    <div v-if="!contact.process" class="text-sm text-gray-500 text-center py-4">
                        Kontakt není přiřazen k žádnému procesu.
                    </div>

                    <div v-else>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: contact.process.color }" />
                            <span class="font-medium text-sm">{{ contact.process.name }}</span>
                            <span class="text-xs text-gray-500">
                                (Krok {{ contact.current_step }}/{{ contact.process.steps?.length }})
                            </span>
                        </div>

                        <ProgressBar
                            :value="((contact.current_step || 0) / (contact.process.steps?.length || 1)) * 100"
                            :color="contact.process.color"
                            show-label
                        />

                        <!-- Step list -->
                        <div class="mt-4 space-y-2">
                            <div
                                v-for="step in contact.process.steps"
                                :key="step.id"
                                :class="[
                                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm',
                                    step.order < (contact.current_step || 0) ? 'bg-green-900/20 text-green-400' :
                                    step.order === contact.current_step ? 'bg-gold-500/10 text-gold-500 font-medium' :
                                    'text-gray-400'
                                ]"
                            >
                                <div :class="[
                                    'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0',
                                    step.order < (contact.current_step || 0) ? 'bg-green-500 text-white' :
                                    step.order === contact.current_step ? 'bg-gold-500 text-white' :
                                    'bg-navy-700 text-gray-500'
                                ]">
                                    <template v-if="step.order < (contact.current_step || 0)">&#10003;</template>
                                    <template v-else>{{ step.order }}</template>
                                </div>
                                <span>{{ step.name }}</span>
                                <Badge v-if="step.is_auto" color="purple" size="sm">AUTO</Badge>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-navy-700">
                            <Button
                                v-if="nextStep"
                                size="sm"
                                @click="showAdvanceConfirm = true"
                            >
                                Dokončit krok a pokračovat
                            </Button>
                            <p v-else class="text-sm text-green-400 font-medium">Všechny kroky dokončeny!</p>
                        </div>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-6">
                    <h3 class="font-semibold text-white mb-4">Úkoly</h3>
                    <div v-if="!contact.tasks?.length" class="text-sm text-gray-500 text-center py-4">
                        Žádné úkoly.
                    </div>
                    <div v-else class="space-y-2">
                        <div
                            v-for="task in contact.tasks"
                            :key="task.id"
                            :class="[
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm',
                                task.is_done ? 'bg-navy-800 text-gray-400 line-through' : 'bg-navy-800'
                            ]"
                        >
                            <span class="flex-1">{{ task.title }}</span>
                            <span class="text-xs text-gray-500">{{ formatDateShort(task.due_date) }}</span>
                            <Badge :color="PRIORITY_COLORS[task.priority] as any" size="sm">
                                {{ PRIORITY_LABELS[task.priority] }}
                            </Badge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Activity log -->
            <div class="space-y-6">
                <!-- Add activity form -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="font-semibold text-white mb-3">Přidat aktivitu</h3>
                    <form @submit.prevent="submitActivity" class="space-y-3">
                        <SelectInput v-model="activityForm.type" :options="activityTypeOptions" />
                        <TextArea v-model="activityForm.description" placeholder="Popis aktivity..." :rows="2" />
                        <Button type="submit" size="sm" :loading="activityForm.processing" :disabled="!activityForm.description">
                            Přidat
                        </Button>
                    </form>
                </div>

                <!-- Activity timeline -->
                <div class="bg-navy-900 rounded-xl border border-navy-700 p-5">
                    <h3 class="font-semibold text-white mb-4">Historie</h3>
                    <div v-if="!contact.activity_logs?.length" class="text-sm text-gray-500 text-center py-4">
                        Zatím žádné záznamy.
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="log in contact.activity_logs"
                            :key="log.id"
                            class="border-l-2 border-navy-700 pl-3 py-1"
                        >
                            <p class="text-sm text-white">{{ log.description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(log.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advance step confirm -->
        <ConfirmDialog
            :show="showAdvanceConfirm"
            title="Dokončit krok"
            :message="`Označit krok '${currentStep?.name}' jako hotový a vytvořit úkol pro další krok '${nextStep?.name}'?`"
            confirm-text="Dokončit a pokračovat"
            @confirm="advanceStep"
            @cancel="showAdvanceConfirm = false"
        />

        <!-- Delete confirm -->
        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Smazat kontakt"
            :message="`Opravdu chcete smazat kontakt '${contact.name}'? Tato akce je nevratná.`"
            variant="danger"
            confirm-text="Smazat"
            @confirm="deleteContact"
            @cancel="showDeleteConfirm = false"
        />

        <!-- Assign process modal -->
        <ConfirmDialog
            v-if="showAssignProcess"
            :show="showAssignProcess"
            title="Přiřadit proces"
            message="Vyberte proces, ke kterému chcete přiřadit kontakt."
            confirm-text="Přiřadit"
            @confirm="assignProcess"
            @cancel="showAssignProcess = false"
        >
        </ConfirmDialog>
    </AppLayout>
</template>
