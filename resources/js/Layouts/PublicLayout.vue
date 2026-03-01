<script setup lang="ts">
</script>

<template>
    <div class="min-h-screen bg-navy-950 text-white">
        <!-- Header -->
        <header class="border-b border-navy-700">
            <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gold-500 rounded-lg flex items-center justify-center font-bold text-sm text-navy-950">
                        AR
                    </div>
                    <span class="font-semibold text-white">Apres Reality</span>
                </div>
                <a href="https://apresreality.cz" target="_blank" class="text-sm text-gray-400 hover:text-gold-500 transition-colors">
                    apresreality.cz
                </a>
            </div>
        </header>

        <!-- Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-navy-700 mt-12">
            <div class="max-w-5xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
                <p>&copy; {{ new Date().getFullYear() }} Apres Reality. Všechna práva vyhrazena.</p>
            </div>
        </footer>
    </div>
</template>
