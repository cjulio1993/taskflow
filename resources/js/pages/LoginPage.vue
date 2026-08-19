<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { AxiosError } from 'axios';

import { useAuthStore } from '../stores/auth';

const router = useRouter();
const auth = useAuthStore();
const email = ref('');
const password = ref('');
const error = ref('');

async function submit(): Promise<void> {
    error.value = '';
    try {
        await auth.login({ email: email.value, password: password.value });
        await router.push({ name: 'dashboard' });
    } catch (exception) {
        error.value = exception instanceof AxiosError && exception.response?.status === 422
            ? 'Confira seu e-mail e senha.'
            : 'Nao foi possivel entrar agora.';
    }
}
</script>

<template>
    <main class="grid min-h-screen place-items-center bg-slate-950 px-6 py-12 text-slate-100">
        <form class="w-full max-w-md space-y-6 rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-2xl" @submit.prevent="submit">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">TaskFlow</p>
                <h1 class="mt-3 text-3xl font-bold">Bem-vindo de volta</h1>
                <p class="mt-2 text-slate-400">Entre para continuar organizando seu trabalho.</p>
            </div>
            <p v-if="error" class="rounded-lg border border-rose-500/40 bg-rose-500/10 p-3 text-sm text-rose-200">{{ error }}</p>
            <label class="block text-sm text-slate-300">E-mail<input v-model="email" type="email" required autocomplete="email" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2.5 text-slate-100 outline-none focus:border-cyan-300"></label>
            <label class="block text-sm text-slate-300">Senha<input v-model="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2.5 text-slate-100 outline-none focus:border-cyan-300"></label>
            <button :disabled="auth.loading" class="w-full rounded-lg bg-cyan-300 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-200 disabled:cursor-wait disabled:opacity-60">{{ auth.loading ? 'Entrando...' : 'Entrar' }}</button>
            <p class="text-center text-sm text-slate-400">Ainda nao tem conta? <RouterLink class="font-semibold text-cyan-300 hover:text-cyan-200" :to="{ name: 'register' }">Criar conta</RouterLink></p>
        </form>
    </main>
</template>
