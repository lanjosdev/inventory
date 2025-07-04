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
