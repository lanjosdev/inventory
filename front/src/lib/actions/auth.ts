'use server'

import { loginSchema, type LoginFormData } from '@/lib/validations/auth'
import { z } from 'zod'
import { cookies } from 'next/headers'
import { login as loginService } from '@/services/authService'
import { ApiError } from '@/lib/api'

/**
 * Executa a autenticação do usuário, orquestrando a validação e a chamada ao serviço.
 * Em caso de sucesso, armazena o token de acesso em um cookie httpOnly.
 * @param data - Os dados do formulário de login (email e senha).
 * @returns Um objeto indicando o resultado da operação para o cliente.
 */
export async function loginAction(data: LoginFormData) {
    try {
        // 1. Validação dos dados de entrada com Zod.
        const validatedData = loginSchema.parse(data)

        // 2. Chamada ao serviço de autenticação, que encapsula a lógica da API.
        const response = await loginService(validatedData)
        console.error('Resposta do serviço de login:', response)

        if (response.success) {
            // 3. Tratamento da resposta de sucesso: armazenar o token em um cookie.
            cookies().set('auth_token', response.data.access_token, {
                httpOnly: true, // O cookie não pode ser acessado por JavaScript no cliente.
                secure: process.env.NODE_ENV === 'production', // Usar secure em produção (HTTPS).
            })

            return {
                success: true,
                message: response.message || 'Login realizado com sucesso!',
                user: response.data.user,
            }
        }
        else if (response.success === false) {
            // Se a resposta indicar falha, retornamos o erro diretamente.        
            return {
                success: false,
                message: response.message || 'Erro ao realizar login.',
                errors: response.errors || {},
            }
        }
        else {
            // Se a resposta não for um sucesso ou falha, tratamos como erro inesperado   
            throw new Error('Resposta inesperada do serviço de login.')
        }
    }
    catch (error) {
        // 4. Tratamento de erros.
        if (error instanceof z.ZodError) {
            // Erro de validação do Zod.
            return {
                success: false,
                message: 'Dados inválidos. Verifique as informações fornecidas.',
                errors: error.flatten().fieldErrors,
            }
        }

        if (error instanceof ApiError) {
            // Erro vindo da nossa classe de erro da API.
            // Isso nos dá acesso ao status da resposta e aos dados do erro.
            switch (error.response.status) {
                case 401:
                    return { success: false, message: 'Credenciais inválidas.' }
                case 422:
                    return {
                        success: false,
                        message: 'Erro de validação. Verifique os campos.',
                        errors: error.data.errors,
                    }
                default:
                    return { success: false, message: error.message }
            }
        }

        // Erro genérico e inesperado.
        console.error('Erro inesperado na action de login:', error)
        return {
            success: false,
            message:
                'Não foi possível conectar ao servidor. Tente novamente mais tarde.',
        }
    }
}
