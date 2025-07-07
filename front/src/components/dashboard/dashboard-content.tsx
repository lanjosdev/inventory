'use client';

import { useDashboard } from '@/hooks/use-dashboard';
import { StatsCard } from './stats-card';
import { QuickActions } from './quick-actions';
import { RecentActivity } from './recent-activity';
import { DashboardSkeleton } from './dashboard-skeleton';
import { Building2, Store, Activity, TrendingUp } from 'lucide-react';

export function DashboardContent() {
  const { stats, loading, error } = useDashboard();

  if (loading) {
    return <DashboardSkeleton />;
  }

  if (error) {
    return (
      <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
        <div className="flex items-center justify-center min-h-[400px]">
          <div className="text-center">
            <div className="text-red-500 mb-2">
              <Activity className="h-8 w-8 mx-auto" />
            </div>
            <h3 className="text-lg font-semibold mb-2">Erro ao carregar dados</h3>
            <p className="text-muted-foreground">{error}</p>
          </div>
        </div>
      </div>
    );
  }

  if (!stats) {
    return null;
  }

  return (
    <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
      {/* Stats Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatsCard
          title="Total de Redes"
          value={stats.total_networks}
          description="Redes cadastradas no sistema"
          icon={<Building2 className="h-4 w-4" />}
          variant="networks"
        />
        <StatsCard
          title="Total de Lojas"
          value={stats.total_stores}
          description="Lojas cadastradas no sistema"
          icon={<Store className="h-4 w-4" />}
          variant="stores"
        />
        <StatsCard
          title="Redes Ativas"
          value={stats.active_networks}
          description="Redes em operação"
          icon={<Activity className="h-4 w-4" />}
          variant="networks"
        />
        <StatsCard
          title="Lojas Ativas"
          value={stats.active_stores}
          description="Lojas em operação"
          icon={<TrendingUp className="h-4 w-4" />}
          variant="stores"
        />
      </div>

      {/* Content Grid */}
      <div className="grid gap-4 md:grid-cols-2">
        {/* Quick Actions */}
        <QuickActions />

        {/* Recent Activity */}
        <RecentActivity
          recentNetworks={stats.recent_networks}
          recentStores={stats.recent_stores}
        />
      </div>
    </div>
  );
}
