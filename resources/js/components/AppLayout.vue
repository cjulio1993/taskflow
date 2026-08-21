<script setup lang="ts">
import { useRouter } from 'vue-router';

import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();

async function logout(): Promise<void> {
    await auth.logout();
    await router.push({ name: 'login' });
}
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-slate-800 bg-slate-950/95">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 sm:px-8">
                <RouterLink :to="{ name: 'dashboard' }" class="text-lg font-bold tracking-tight text-cyan-300">TaskFlow</RouterLink>
                <nav class="flex items-center gap-1 text-sm">
                    <RouterLink :to="{ name: 'projects' }" class="rounded-md px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Projects</RouterLink>
                    <RouterLink :to="{ name: 'dashboard' }" class="rounded-md px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Dashboard</RouterLink>
                    <span class="hidden border-l border-slate-800 pl-4 text-slate-500 sm:inline">{{ auth.user?.name }}</span>
                    <button class="rounded-md px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white" @click="logout">Logout</button>
                </nav>
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8"><RouterView /></main>
    </div>
</template>
