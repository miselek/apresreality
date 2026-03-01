<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import type { NotificationTemplate } from '@/types';

defineProps<{
    templates: NotificationTemplate[];
}>();

const deleteTemplate = ref<NotificationTemplate | null>(null);

function doDelete() {
    if (!deleteTemplate.value) return;
    router.delete(`/notifications/${deleteTemplate.value.id}`);
    deleteTemplate.value = null;
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold font-serif text-white">Notifikace</h1>
        </template>
        <template #actions>
            <Link href="/notifications/create">
                <Button size="sm">Nová šablona</Button>
            </Link>
        </template>

        <EmptyState
            v-if="templates.length === 0"
            title="Žádné šablony"
            description="Vytvořte šablonu pro SMS nebo e-mail notifikace."
        >
            <Link href="/notifications/create">
                <Button size="sm">Vytvořit šablonu</Button>
            </Link>
        </EmptyState>

        <div v-else class="bg-navy-900 rounded-xl border border-navy-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-navy-800">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Název</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3">Typ</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-4 py-3 hidden md:table-cell">Předmět</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase px-4 py-3">Akce</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-700">
                    <tr v-for="tpl in templates" :key="tpl.id" class="hover:bg-navy-800">
                        <td class="px-4 py-3 text-sm font-medium text-white">{{ tpl.name }}</td>
                        <td class="px-4 py-3">
                            <Badge :color="tpl.type === 'sms' ? 'green' : 'blue'" size="sm">
                                {{ tpl.type === 'sms' ? 'SMS' : 'E-mail' }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400 hidden md:table-cell">{{ tpl.subject || '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <Link :href="`/notifications/${tpl.id}/edit`">
                                    <Button variant="ghost" size="sm">Upravit</Button>
                                </Link>
                                <Button variant="ghost" size="sm" @click="deleteTemplate = tpl">Smazat</Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmDialog
            :show="!!deleteTemplate"
            title="Smazat šablonu"
            :message="`Opravdu chcete smazat šablonu '${deleteTemplate?.name}'?`"
            variant="danger"
            confirm-text="Smazat"
            @confirm="doDelete"
            @cancel="deleteTemplate = null"
        />
    </AppLayout>
</template>
