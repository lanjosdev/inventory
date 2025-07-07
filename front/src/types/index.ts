/**
 * Representa a estrutura do objeto de usuário retornado pela API.
 */
export interface User {
  id: number
  name: string
  email: string
  level: {
    id: number
    name: string
    permission: string
  }[]
  created_at: string
  updated_at: string
  deleted_at: string | null
}

/**
 * Representa a resposta de sucesso da API de login, com os dados aninhados.
 */
export interface LoginSuccessResponse {
  success: true
  message: string
  data: {
    access_token: string
    token_type: string
    user: User
  }
}

/**
 * Representa uma resposta de erro da API que ainda retorna um status 200 OK,
 * mas com uma falha lógica (ex: validação).
 */
export interface LoginErrorResponse {
  success: false
  message: string
  errors?: Record<string, string[]>
}

/**
 * União dos tipos de resposta possíveis para o endpoint de login.
 */
export type LoginApiResponse = LoginSuccessResponse | LoginErrorResponse

/**
 * Representa o objeto de retorno da action de login para o componente.
 */
export type ActionResult = {
  success: boolean
  message: string
  errors?: Record<string, string[] | undefined>
  user?: User
}

/**
 * Representa uma rede de supermercados.
 */
export interface Network {
  id: number
  name: string
  description: string
  created_at: string
  updated_at: string
  deleted_at: string | null
  stores_count?: number
}

/**
 * Representa uma loja de supermercado.
 */
export interface Store {
  id: number
  name: string
  address: string
  phone: string
  email: string
  network_id: number
  network?: Network
  created_at: string
  updated_at: string
  deleted_at: string | null
}

/**
 * Representa estatísticas gerais do sistema.
 */
export interface DashboardStats {
  total_networks: number
  total_stores: number
  active_networks: number
  active_stores: number
  recent_networks: Network[]
  recent_stores: Store[]
}

/**
 * Resposta da API para estatísticas do dashboard.
 */
export interface DashboardStatsResponse {
  success: boolean
  message: string
  data: DashboardStats
}

/**
 * Representa um contato de uma empresa/rede.
 */
export interface Contact {
  id_contact: number
  name: string
  email: string
  phone: string
  observation?: string | null
}

/**
 * Representa uma empresa/rede de supermercados baseada na API.
 */
export interface Company {
  id_company: number
  name: string
  created_at: string
  updated_at: string
  contacts: Contact[]
}

/**
 * Dados para criação de uma nova empresa/rede.
 */
export interface CreateCompanyRequest {
  name: string
  contacts: {
    name: string
    email: string
    phone: string
    observation?: string
  }[]
}

/**
 * Dados para atualização de uma empresa/rede.
 */
export interface UpdateCompanyRequest {
  name: string
  contacts: {
    id_contact?: number
    name: string
    email: string
    phone: string
    observation?: string
  }[]
}

/**
 * Resposta paginada da API para empresas/redes.
 */
export interface CompaniesResponse {
  success: boolean
  message: string
  data: {
    current_page: number
    data: Company[]
    total: number
    last_page: number
  }
}

/**
 * Resposta da API para uma empresa/rede específica.
 */
export interface CompanyResponse {
  success: boolean
  message: string
  data: Company
}

/**
 * Resposta da API para criação/atualização de empresa/rede.
 */
export interface CompanyCreateResponse {
  success: boolean
  message: string
  data: {
    id: number
    name: string
    contacts: {
      id: number
      name: string
      email: string
      phone: string
      observation?: string
    }[]
  }
}
