<script setup lang="ts">
defineProps<{ title: string }>();
defineEmits<{ close: [] }>();
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4" @click.self="$emit('close')">
        <section class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-xl border border-slate-700 bg-slate-900 shadow-2xl">
            <header class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
                <h2 class="font-semibold text-white">{{ title }}</h2>
                <button class="rounded px-2 py-1 text-slate-400 hover:bg-slate-800 hover:text-white" aria-label="Close" @click="$emit('close')">×</button>
            </header>
            <div class="p-5"><slot /></div>
        </section>
    </div>
</template>
