'use client';

import { useEffect, useState } from 'react';
import { DashboardStats } from '@/types';
import { dashboardService } from '@/services/dashboardService';

/**
 * Hook para gerenciar dados do dashboard.
 */
export function useDashboard() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  /**
   * Busca estatísticas do dashboard.
   */
  const fetchStats = async () => {
    try {
      setLoading(true);
      setError(null);
      
      const response = await dashboardService.getDashboardStats();
      
      if (response.success) {
        setStats(response.data);
      } else {
        setError(response.message || 'Erro ao carregar estatísticas');
      }
    } catch (err) {
      console.error('Erro ao buscar estatísticas:', err);
      setError('Erro ao carregar estatísticas');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchStats();
  }, []);

  return {
    stats,
    loading,
    error,
    refetch: fetchStats,
  };
}
