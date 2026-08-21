import { apiClient, prepareCsrfProtection } from './http';

export interface AuthenticatedUser {
    id: number;
    name: string;
    email: string;
}

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegistrationData extends LoginCredentials {
    name: string;
    password_confirmation: string;
}

interface UserResponse {
    data: AuthenticatedUser;
}

export async function fetchAuthenticatedUser(): Promise<AuthenticatedUser> {
    return (await apiClient.get<UserResponse>('/me')).data.data;
}

export async function login(credentials: LoginCredentials): Promise<AuthenticatedUser> {
    await prepareCsrfProtection();

    return (await apiClient.post<UserResponse>('/auth/login', credentials)).data.data;
}

export async function register(data: RegistrationData): Promise<AuthenticatedUser> {
    await prepareCsrfProtection();

    return (await apiClient.post<UserResponse>('/auth/register', data)).data.data;
}

export async function logout(): Promise<void> {
    await apiClient.post('/auth/logout');
}
