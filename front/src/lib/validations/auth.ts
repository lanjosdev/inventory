import { z } from 'zod'

export const loginSchema = z.object({
    email: z
        .string()
        .min(1, 'E-mail é obrigatório')
        .email('E-mail inválido'),
    password: z
        .string()
        .min(1, 'Senha é obrigatória')
        .min(8, 'Senha deve ter pelo menos 8 caracteres')
        .max(30, 'Senha deve ter no máximo 30 caracteres')
})

export type LoginFormData = z.infer<typeof loginSchema>
