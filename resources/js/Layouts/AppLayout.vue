<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const page = usePage();

const navigation = [
    { name: 'Dashboard', href: '/', icon: 'home' },
    { name: 'Kontakty', href: '/contacts', icon: 'users' },
    { name: 'Nemovitosti', href: '/nemovitosti', icon: 'building' },
    { name: 'Procesy', href: '/processes', icon: 'git-branch' },
    { name: 'Úkoly', href: '/tasks', icon: 'check-square' },
    { name: 'Cenové analýzy', href: '/price-analyses', icon: 'bar-chart' },
    { name: 'Smlouvy', href: '/contracts', icon: 'file-text' },
    { name: 'Notifikace', href: '/notifications', icon: 'bell' },
];

function isActive(href: string): boolean {
    const url = page.url;
    if (href === '/') return url === '/';
    return url.startsWith(href);
}
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/60 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-navy-950 border-r border-navy-700 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex items-center gap-3 px-6 py-5 border-b border-navy-700">
                <div class="w-8 h-8 bg-gold-500 rounded-lg flex items-center justify-center font-bold text-sm text-navy-950">
                    AR
                </div>
                <div>
                    <div class="font-semibold text-sm text-white">Apres Reality</div>
                    <div class="text-xs text-gray-400">CRM systém</div>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                        isActive(item.href)
                            ? 'bg-navy-800 text-gold-500'
                            : 'text-gray-400 hover:bg-navy-800 hover:text-gold-400'
                    ]"
                    @click="sidebarOpen = false"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <template v-if="item.icon === 'home'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </template>
                        <template v-else-if="item.icon === 'users'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </template>
                        <template v-else-if="item.icon === 'building'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </template>
                        <template v-else-if="item.icon === 'git-branch'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </template>
                        <template v-else-if="item.icon === 'check-square'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </template>
                        <template v-else-if="item.icon === 'bar-chart'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </template>
                        <template v-else-if="item.icon === 'file-text'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </template>
                        <template v-else-if="item.icon === 'bell'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </template>
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="bg-navy-900 border-b border-navy-700 px-4 sm:px-6 py-4 flex flex-wrap items-center gap-3 sm:gap-4">
                <button
                    class="lg:hidden p-2 -ml-2 text-gray-400 hover:text-white"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>
                <div class="shrink-0">
                    <slot name="actions" />
                </div>
            </header>

            <!-- Flash messages -->
            <div v-if="$page.props.flash" class="px-4 sm:px-6 pt-4">
                <div
                    v-if="($page.props.flash as any).success"
                    class="bg-green-900/30 border border-green-700 text-green-400 px-4 py-3 rounded-lg text-sm"
                >
                    {{ ($page.props.flash as any).success }}
                </div>
                <div
                    v-if="($page.props.flash as any).error"
                    class="bg-red-900/30 border border-red-700 text-red-400 px-4 py-3 rounded-lg text-sm"
                >
                    {{ ($page.props.flash as any).error }}
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
