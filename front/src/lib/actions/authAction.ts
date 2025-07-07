'use server'

import { loginSchema, type LoginFormData } from '@/lib/validations/auth'
import { z } from 'zod'
import { cookies } from 'next/headers'
import { login as loginService } from '@/services/authService'
import { ApiError } from '@/lib/api'
import type { ActionResult, LoginSuccessResponse } from '@/types'

/**
 * Executa a autenticação do usuário, orquestrando a validação e a chamada ao serviço.
 * Em caso de sucesso, armazena o token de acesso em um cookie httpOnly.
 * @param data - Os dados do formulário de login (email e senha).
 * @returns Um objeto indicando o resultado da operação para o cliente.
 */
export async function loginAction(data: LoginFormData): Promise<ActionResult> {
    try {
        // 1. Validação dos dados de entrada com Zod.
        const validatedData = loginSchema.parse(data)

        // 2. Chamada ao serviço de autenticação, que encapsula a lógica da API.
        const response: LoginSuccessResponse = await loginService(validatedData)
        // console.log('Resposta do serviço de login:', response)

        // 3. Tratamento da resposta de sucesso: armazenar o token em um cookie.
        const authTokenName = process.env.NEXT_COOKIE_AUTH_TOKEN_NAME || 'auth_token_bizsys'
        cookies().set(authTokenName, response.data.access_token, {
            httpOnly: true, // O cookie não pode ser acessado por JavaScript no cliente.
            secure: process.env.NODE_ENV === 'production', // Usar secure em produção (HTTPS).
            path: '/', // Disponível em toda a aplicação
            maxAge: 60 * 60 * 24 * 7, // 1 semana
        })

        return {
            success: true,
            message: 'Login realizado com sucesso!',
            user: response.data.user,
        }
    } catch (error) {
        // console.error('EEErro ao processar a ação de login:', error)
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
            const apiMessage = error.data.message || 'Ocorreu um erro.'
            let errorMessage = apiMessage

            // Se a mensagem for um objeto, extrai a primeira mensagem de erro.
            if (typeof apiMessage === 'object' && apiMessage !== null) {
                const firstErrorKey = Object.keys(apiMessage)[0]
                if (firstErrorKey && Array.isArray(apiMessage[firstErrorKey])) {
                    errorMessage = apiMessage[firstErrorKey][0]
                }
            }

            return {
                success: false,
                message: errorMessage as string,
                errors: error.data.errors,
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

/**
 * Realiza o logout do usuário, removendo o cookie de autenticação.
 * @returns Um objeto indicando o resultado da operação.
 */
export async function logoutAction(): Promise<ActionResult> {
    try {
        const authTokenName = process.env.NEXT_COOKIE_AUTH_TOKEN_NAME || 'auth_token_bizsys'
        cookies().delete(authTokenName)

        return {
            success: true,
            message: 'Logout realizado com sucesso!',
        }
    } catch (error) {
        console.error('Erro inesperado na action de logout:', error)
        return {
            success: false,
            message: 'Ocorreu um erro ao tentar fazer logout. Tente novamente.',
        }
    }
}