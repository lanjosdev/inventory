import { z } from 'zod'

/**
 * Schema de validação para contatos.
 */
export const contactSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  email: z.string().email('E-mail inválido'),
  phone: z.string().min(1, 'Telefone é obrigatório'),
  observation: z.string().optional()
})

/**
 * Schema de validação para criação de empresa/rede.
 */
export const createCompanySchema = z.object({
  name: z.string().min(1, 'Nome da empresa é obrigatório'),
  contacts: z.array(contactSchema).min(1, 'Pelo menos um contato é obrigatório')
})

/**
 * Schema de validação para atualização de empresa/rede.
 */
export const updateCompanySchema = z.object({
  name: z.string().min(1, 'Nome da empresa é obrigatório'),
  contacts: z.array(contactSchema.extend({
    id_contact: z.number().optional()
  })).min(1, 'Pelo menos um contato é obrigatório')
})

/**
 * Tipos derivados dos schemas.
 */
export type CreateCompanyForm = z.infer<typeof createCompanySchema>
export type UpdateCompanyForm = z.infer<typeof updateCompanySchema>
export type ContactForm = z.infer<typeof contactSchema>
