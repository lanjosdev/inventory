import { api } from '@/lib/api'
import type { 
  CompaniesResponse, 
  CompanyResponse, 
  CreateCompanyRequest, 
  UpdateCompanyRequest, 
  CompanyCreateResponse 
} from '@/types'

/**
 * Serviço para operações CRUD de empresas/redes.
 */
export class CompanyService {
  /**
   * Lista todas as empresas/redes com paginação.
   */
  static async getCompanies(page: number = 1, perPage: number = 10): Promise<CompaniesResponse> {
    const url = `/companies?page=${page}&per_page=${perPage}`
    return await api.authFetch<CompaniesResponse>(url)
  }

  /**
   * Busca uma empresa/rede específica pelo ID.
   */
  static async getCompanyById(id: number): Promise<CompanyResponse> {
    const url = `/api/companies/${id}`
    return await api.authFetch<CompanyResponse>(url)
  }

  /**
   * Cria uma nova empresa/rede.
   */
  static async createCompany(data: CreateCompanyRequest): Promise<CompanyCreateResponse> {
    const url = '/companies'
    return await api.authFetch<CompanyCreateResponse>(url, {
      method: 'POST',
      body: JSON.stringify(data)
    })
  }

  /**
   * Atualiza uma empresa/rede existente.
   */
  static async updateCompany(id: number, data: UpdateCompanyRequest): Promise<CompanyCreateResponse> {
    const url = `/api/companies/${id}`
    return await api.authFetch<CompanyCreateResponse>(url, {
      method: 'PUT',
      body: JSON.stringify(data)
    })
  }

  /**
   * Remove uma empresa/rede.
   */
  static async deleteCompany(id: number): Promise<{ success: boolean; message: string }> {
    const url = `/api/companies/${id}`
    return await api.authFetch<{ success: boolean; message: string }>(url, {
      method: 'DELETE'
    })
  }
}
