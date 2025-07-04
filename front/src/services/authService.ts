import { api, ApiError } from '@/lib/api';
import { type LoginFormData } from '@/lib/validations/auth';

export async function login(data: LoginFormData) {
    // Usa o cliente da lib para fazer a chamada
    const response = await api.fetch('/login', {
        method: 'POST',
        body: JSON.stringify(data),
    });
    return response;
}

// ... outras funções como logout(), register(), etc.