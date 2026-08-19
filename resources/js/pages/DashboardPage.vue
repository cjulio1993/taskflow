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
    <main class="min-h-screen bg-slate-950 px-6 py-10 text-slate-100">
        <section class="mx-auto max-w-5xl">
            <header class="flex items-center justify-between border-b border-slate-800 pb-6">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">TaskFlow</p>
                    <h1 class="mt-2 text-3xl font-bold">Seu espaço de trabalho</h1>
                </div>
                <button class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:border-cyan-300 hover:text-cyan-200" @click="logout">Sair</button>
            </header>
            <div class="mt-10 grid gap-6 md:grid-cols-[1.4fr_1fr]">
                <section class="rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-7">
                    <p class="text-sm text-cyan-200">Ola, {{ auth.user?.name }}</p>
                    <h2 class="mt-3 text-2xl font-semibold">Tudo pronto para o proximo passo.</h2>
                    <p class="mt-3 max-w-xl text-slate-300">Sua conta esta autenticada e protegida pela sessao segura do TaskFlow.</p>
                </section>
                <section class="rounded-2xl border border-slate-800 bg-slate-900 p-7">
                    <p class="text-sm text-slate-400">Conta ativa</p>
                    <p class="mt-3 break-words text-lg font-medium">{{ auth.user?.email }}</p>
                </section>
            </div>
        </section>
    </main>
</template>
