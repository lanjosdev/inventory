'use server'

import { CompanyService } from '@/services/companyService'
import type { CreateCompanyRequest, UpdateCompanyRequest } from '@/types'
import { revalidatePath } from 'next/cache'

/**
 * Server action para listar empresas/redes.
 */
export async function getCompaniesAction(page: number = 1, perPage: number = 10) {
  try {
    const response = await CompanyService.getCompanies(page, perPage)
    return {
      success: true,
      data: response.data,
      message: response.message
    }
  } catch (error) {
    console.error('Erro ao buscar redes:', error)
    return {
      success: false,
      message: 'Erro ao buscar redes. Tente novamente.',
      data: null
    }
  }
}

/**
 * Server action para buscar uma empresa/rede específica.
 */
export async function getCompanyByIdAction(id: number) {
  try {
    const response = await CompanyService.getCompanyById(id)
    return {
      success: true,
      data: response.data,
      message: response.message
    }
  } catch (error) {
    console.error('Erro ao buscar rede:', error)
    return {
      success: false,
      message: 'Erro ao buscar rede. Tente novamente.',
      data: null
    }
  }
}

/**
 * Server action para criar uma nova empresa/rede.
 */
export async function createCompanyAction(data: CreateCompanyRequest) {
  try {
    const response = await CompanyService.createCompany(data)
    
    // Revalidar a página para atualizar os dados
    revalidatePath('/redes')
    
    return {
      success: true,
      data: response.data,
      message: response.message
    }
  } catch (error) {
    console.error('Erro ao criar rede:', error)
    return {
      success: false,
      message: 'Erro ao criar rede. Tente novamente.',
      data: null
    }
  }
}

/**
 * Server action para atualizar uma empresa/rede.
 */
export async function updateCompanyAction(id: number, data: UpdateCompanyRequest) {
  try {
    const response = await CompanyService.updateCompany(id, data)
    
    // Revalidar a página para atualizar os dados
    revalidatePath('/redes')
    
    return {
      success: true,
      data: response.data,
      message: response.message
    }
  } catch (error) {
    console.error('Erro ao atualizar rede:', error)
    return {
      success: false,
      message: 'Erro ao atualizar rede. Tente novamente.',
      data: null
    }
  }
}

/**
 * Server action para deletar uma empresa/rede.
 */
export async function deleteCompanyAction(id: number) {
  try {
    const response = await CompanyService.deleteCompany(id)
    console.log('Resposta do deleteCompanyAction:', response)
    
    // Revalidar a página para atualizar os dados
    revalidatePath('/redes')
    
    return {
      success: true,
      message: response.message
    }
  } catch (error) {
    console.error('Erro ao deletar rede:', error)
    return {
      success: false,
      message: 'Erro ao deletar rede. Tente novamente.'
    }
  }
}
