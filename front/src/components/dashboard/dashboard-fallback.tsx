'use client';

import { StatsCard } from './stats-card';
import { QuickActions } from './quick-actions';
import { RecentActivity } from './recent-activity';
import { Building2, Store, Activity, TrendingUp } from 'lucide-react';

/**
 * Componente de fallback com dados mockados para desenvolvimento.
 * Será usado quando a API não estiver disponível.
 */
export function DashboardFallback() {
  // Dados mockados para desenvolvimento
  const mockStats = {
    total_networks: 12,
    total_stores: 89,
    active_networks: 11,
    active_stores: 85,
    recent_networks: [
      {
        id: 1,
        name: 'Rede SuperMax',
        description: 'Rede de supermercados focada em produtos premium',
        created_at: '2025-01-05T10:30:00Z',
        updated_at: '2025-01-05T10:30:00Z',
        deleted_at: null,
        stores_count: 15,
      },
      {
        id: 2,
        name: 'Mercado Bom Preço',
        description: 'Supermercados com foco em economia e variedade',
        created_at: '2025-01-03T14:20:00Z',
        updated_at: '2025-01-03T14:20:00Z',
        deleted_at: null,
        stores_count: 8,
      },
    ],
    recent_stores: [
      {
        id: 1,
        name: 'SuperMax Centro',
        address: 'Rua das Flores, 123 - Centro',
        phone: '(11) 3456-7890',
        email: 'centro@supermax.com.br',
        network_id: 1,
        created_at: '2025-01-06T09:15:00Z',
        updated_at: '2025-01-06T09:15:00Z',
        deleted_at: null,
      },
      {
        id: 2,
        name: 'Mercado Bom Preço Vila Nova',
        address: 'Av. Brasil, 456 - Vila Nova',
        phone: '(11) 2345-6789',
        email: 'vilanova@bompreco.com.br',
        network_id: 2,
        created_at: '2025-01-04T16:45:00Z',
        updated_at: '2025-01-04T16:45:00Z',
        deleted_at: null,
      },
    ],
  };

  return (
    <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
      {/* Stats Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatsCard
          title="Total de Redes"
          value={mockStats.total_networks}
          description="Redes cadastradas no sistema"
          icon={<Building2 className="h-4 w-4" />}
          variant="networks"
        />
        <StatsCard
          title="Total de Lojas"
          value={mockStats.total_stores}
          description="Lojas cadastradas no sistema"
          icon={<Store className="h-4 w-4" />}
          variant="stores"
        />
        <StatsCard
          title="Redes Ativas"
          value={mockStats.active_networks}
          description="Redes em operação"
          icon={<Activity className="h-4 w-4" />}
          variant="networks"
        />
        <StatsCard
          title="Lojas Ativas"
          value={mockStats.active_stores}
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
          recentNetworks={mockStats.recent_networks}
          recentStores={mockStats.recent_stores}
        />
      </div>
    </div>
  );
}
