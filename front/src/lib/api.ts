// import { env } from '@/env.mjs';
import { cookies } from 'next/headers';

/**
 * Classe de erro personalizada para representar erros da API.
 */
export class ApiError extends Error {
  response: Response;
  data: { message: string; errors?: Record<string, string[]> };

  constructor(
    message: string,
    response: Response,
    data: { message: string; errors?: Record<string, string[]> }
  ) {
    super(message);
    this.name = 'ApiError';
    this.response = response;
    this.data = data;
  }
}


/**
 * Classe para encapsular a lógica de fetch da API.
 */
class ApiClient {
  private baseUrl: string;

  constructor() {
    // Acessa a variável de ambiente diretamente.
    this.baseUrl = process.env.NEXT_PUBLIC_API_URL || '';

    if (!this.baseUrl) {
      console.error(
        'A variável de ambiente NEXT_PUBLIC_API_URL não está definida.'
      );
      
      // Lançar um erro aqui impede que a aplicação funcione com configuração faltando.
      throw new Error(
        'A variável de ambiente NEXT_PUBLIC_API_URL não está definida.'
      );
    }
  }

  /**
   * Realiza uma requisição PÚBLICA para a API (sem token de autenticação).
   * @param endpoint - O endpoint para o qual a requisição será feita (ex: '/login').
   * @param options - As opções da requisição (method, body, headers, etc.).
   * @returns A resposta da requisição.
   */
  async fetch<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const url = `${this.baseUrl}${endpoint}`;

    const defaultHeaders = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };

    const config: RequestInit = {
      ...options,
      headers: {
        ...defaultHeaders,
        ...options.headers,
      },
    };

    const response = await fetch(url, config);

    if (!response.ok) {
      let errorData;
      try {
        errorData = await response.json();
      } catch {
        errorData = { message: response.statusText || 'Erro desconhecido' };
      }

      throw new ApiError(
        errorData.message || 'Erro na requisição à API',
        response,
        errorData
      );
    }

    return response.json() as Promise<T>;
  }

  /**
   * Realiza uma requisição AUTENTICADA para a API, incluindo o Bearer Token.
   * @param endpoint - O endpoint para o qual a requisição será feita.
   * @param options - As opções da requisição.
   * @returns A resposta da requisição.
   */
  async authFetch<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const token = cookies().get('auth_token')?.value;

    if (!token) {
      // Lança um erro específico se o token não for encontrado.
      // Isso pode ser capturado para redirecionar o usuário para a página de login.
      throw new ApiError(
        'Token de autenticação não encontrado.',
        new Response(JSON.stringify({ message: 'Não autorizado' }), { status: 401 }),
        { message: 'Token de autenticação não encontrado.' }
      );
    }

    const authHeaders = {
      ...options.headers,
      Authorization: `Bearer ${token}`,
    };

    // Chama o método fetch original com o cabeçalho de autorização.
    return this.fetch<T>(endpoint, {
      ...options,
      headers: authHeaders,
    });
  }
}

// Exporta uma instância única do cliente para ser usada em todo o app.
export const api = new ApiClient();