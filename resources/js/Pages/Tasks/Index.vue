<script setup lang="ts">
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import type { Task, Contact } from '@/types';
import { PRIORITY_LABELS, PRIORITY_COLORS } from '@/types';

defineProps<{
    overdue: Task[];
    today: Task[];
    tomorrow: Task[];
    upcoming: Task[];
    done: Task[];
    contacts: Pick<Contact, 'id' | 'name'>[];
}>();

const showCreateModal = ref(false);
const confirmTask = ref<Task | null>(null);

const createForm = useForm({
    title: '',
    contact_id: '',
    due_date: new Date().toISOString().split('T')[0],
    priority: 'stredni',
});

function submitCreate() {
    createForm.post('/tasks', {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
}

function completeTask(task: Task) {
    confirmTask.value = task;
}

function doComplete() {
    if (!confirmTask.value) return;
    router.post(`/tasks/${confirmTask.value.id}/complete`, {}, {
        preserveScroll: true,
    });
    confirmTask.value = null;
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'short' });
}

const priorityOptions = Object.entries(PRIORITY_LABELS).map(([v, l]) => ({ value: v, label: l }));

const sections = [
    { key: 'overdue', label: 'Po termínu', color: 'text-red-400', bgColor: 'bg-red-900/20' },
    { key: 'today', label: 'Dnes', color: 'text-gold-500', bgColor: 'bg-gold-500/10' },
    { key: 'tomorrow', label: 'Zítra', color: 'text-gray-300', bgColor: '' },
    { key: 'upcoming', label: 'Nadcházející', color: 'text-gray-400', bgColor: '' },
    { key: 'done', label: 'Hotové', color: 'text-gray-400', bgColor: '' },
] as const;
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Úkoly</h1>
        </template>
        <template #actions>
            <Button size="sm" @click="showCreateModal = true">Nový úkol</Button>
        </template>

        <div class="space-y-6">
            <template v-for="section in sections" :key="section.key">
                <div v-if="$props[section.key]?.length > 0" class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden">
                    <div class="px-5 py-3 border-b border-navy-700 flex items-center gap-2">
                        <h2 :class="['font-semibold text-sm', section.color]">{{ section.label }}</h2>
                        <Badge :color="section.key === 'overdue' ? 'red' : section.key === 'today' ? 'blue' : 'gray'" size="sm">
                            {{ $props[section.key].length }}
                        </Badge>
                    </div>
                    <div class="divide-y divide-navy-700">
                        <div
                            v-for="task in $props[section.key]"
                            :key="task.id"
                            :class="[
                                'flex items-center gap-3 px-5 py-3',
                                task.is_done ? 'opacity-50' : '',
                                section.bgColor
                            ]"
                        >
                            <button
                                v-if="!task.is_done"
                                class="w-5 h-5 rounded border-2 border-navy-600 hover:border-gold-500 shrink-0 transition-colors"
                                @click="completeTask(task)"
                            />
                            <div v-else class="w-5 h-5 rounded bg-green-500 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p :class="['text-sm', task.is_done ? 'line-through text-gray-400' : 'text-white']">
                                    {{ task.title }}
                                </p>
                                <Link
                                    v-if="task.contact"
                                    :href="`/contacts/${task.contact.id}`"
                                    class="text-xs text-gold-500 hover:underline"
                                >
                                    {{ task.contact.name }}
                                </Link>
                            </div>
                            <span class="text-xs text-gray-500 shrink-0">{{ formatDate(task.due_date) }}</span>
                            <Badge :color="PRIORITY_COLORS[task.priority] as any" size="sm">
                                {{ PRIORITY_LABELS[task.priority] }}
                            </Badge>
                            <Badge v-if="task.is_auto" color="purple" size="sm">AUTO</Badge>
                        </div>
                    </div>
                </div>
            </template>

            <EmptyState
                v-if="!overdue.length && !today.length && !tomorrow.length && !upcoming.length && !done.length"
                title="Žádné úkoly"
                description="Úkoly se vytvářejí automaticky při postupu procesem, nebo je můžete přidat ručně."
            >
                <Button size="sm" @click="showCreateModal = true">Vytvořit úkol</Button>
            </EmptyState>
        </div>

        <!-- Create task modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Nový úkol</h3>
                <div class="space-y-4">
                    <TextInput v-model="createForm.title" label="Název úkolu" :error="createForm.errors.title" required />
                    <SelectInput
                        v-model="createForm.contact_id"
                        label="Kontakt"
                        :options="contacts.map(c => ({ value: String(c.id), label: c.name }))"
                        placeholder="Vyberte kontakt..."
                        :error="createForm.errors.contact_id"
                        required
                    />
                    <TextInput v-model="createForm.due_date" label="Termín" type="date" :error="createForm.errors.due_date" required />
                    <SelectInput v-model="createForm.priority" label="Priorita" :options="priorityOptions" :error="createForm.errors.priority" />
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <Button variant="secondary" type="button" @click="showCreateModal = false">Zrušit</Button>
                    <Button type="submit" :loading="createForm.processing">Vytvořit</Button>
                </div>
            </form>
        </Modal>

        <!-- Complete confirm -->
        <ConfirmDialog
            :show="!!confirmTask"
            title="Dokončit úkol"
            :message="`Označit úkol '${confirmTask?.title}' jako hotový?`"
            confirm-text="Hotovo"
            @confirm="doComplete"
            @cancel="confirmTask = null"
        />
    </AppLayout>
</template>
