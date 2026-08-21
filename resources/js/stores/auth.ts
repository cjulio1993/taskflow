import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

import {
    fetchAuthenticatedUser,
    login as loginRequest,
    logout as logoutRequest,
    register as registerRequest,
    type AuthenticatedUser,
    type LoginCredentials,
    type RegistrationData,
} from '../api/auth-api';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<AuthenticatedUser | null>(null);
    const initialized = ref(false);
    const loading = ref(false);

    const isAuthenticated = computed(() => user.value !== null);

    async function initialize(): Promise<void> {
        if (initialized.value) return;

        try {
            user.value = await fetchAuthenticatedUser();
        } catch {
            user.value = null;
        } finally {
            initialized.value = true;
        }
    }

    async function login(credentials: LoginCredentials): Promise<void> {
        loading.value = true;
        try {
            user.value = await loginRequest(credentials);
        } finally {
            loading.value = false;
        }
    }

    async function register(data: RegistrationData): Promise<void> {
        loading.value = true;
        try {
            user.value = await registerRequest(data);
        } finally {
            loading.value = false;
        }
    }

    async function logout(): Promise<void> {
        loading.value = true;
        try {
            await logoutRequest();
        } finally {
            user.value = null;
            loading.value = false;
        }
    }

    return { user, initialized, loading, isAuthenticated, initialize, login, register, logout };
});
