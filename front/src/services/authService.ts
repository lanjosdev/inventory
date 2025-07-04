import { api } from '@/lib/api';
import { type LoginFormData } from '@/lib/validations/auth';
import type { LoginSuccessResponse } from '@/types';

export async function login(data: LoginFormData): Promise<LoginSuccessResponse> {
    // Usa o cliente da lib para fazer a chamada
    const response = await api.fetch<LoginSuccessResponse>('/login', {
        method: 'POST',
        body: JSON.stringify(data),
    });
    return response;
}

// ... outras funções como logout(), register(), etc.