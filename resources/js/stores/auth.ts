import { computed, ref } from 'vue';
import { defineStore } from 'pinia';

import client, { prepareCsrf } from '../api/client';

export interface User {
    id: number;
    name: string;
    email: string;
}

interface Credentials {
    email: string;
    password: string;
}

interface RegistrationData extends Credentials {
    name: string;
    password_confirmation: string;
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const initialized = ref(false);
    const loading = ref(false);

    const isAuthenticated = computed(() => user.value !== null);

    async function initialize(): Promise<void> {
        if (initialized.value) return;

        try {
            user.value = (await client.get<{ user: User }>('/me')).data.user;
        } catch {
            user.value = null;
        } finally {
            initialized.value = true;
        }
    }

    async function login(credentials: Credentials): Promise<void> {
        loading.value = true;
        try {
            await prepareCsrf();
            user.value = (await client.post<{ user: User }>('/auth/login', credentials)).data.user;
        } finally {
            loading.value = false;
        }
    }

    async function register(data: RegistrationData): Promise<void> {
        loading.value = true;
        try {
            await prepareCsrf();
            user.value = (await client.post<{ user: User }>('/auth/register', data)).data.user;
        } finally {
            loading.value = false;
        }
    }

    async function logout(): Promise<void> {
        loading.value = true;
        try {
            await client.post('/auth/logout');
        } finally {
            user.value = null;
            loading.value = false;
        }
    }

    return { user, initialized, loading, isAuthenticated, initialize, login, register, logout };
});
