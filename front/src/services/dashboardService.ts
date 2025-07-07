import { api } from '@/lib/api';
import { DashboardStatsResponse } from '@/types';

/**
 * Serviço para gerenciar dados do dashboard.
 */
class DashboardService {
  /**
   * Busca estatísticas gerais do sistema.
   * @returns Estatísticas do dashboard
   */
  async getDashboardStats(): Promise<DashboardStatsResponse> {
    return api.authFetch<DashboardStatsResponse>('/dashboard/stats');
  }
}

export const dashboardService = new DashboardService();
