import axios from 'axios';

const requestDefaults = {
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
};

const http = axios.create(requestDefaults);

export const apiClient = axios.create({
    ...requestDefaults,
    baseURL: '/api/v1',
});

export async function prepareCsrfProtection(): Promise<void> {
    await http.get('/sanctum/csrf-cookie');
}
