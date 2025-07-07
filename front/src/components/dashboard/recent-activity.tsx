import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Network, Store } from '@/types';
import { Building2, Store as StoreIcon, Clock } from 'lucide-react';
import { cn } from '@/lib/utils';

interface RecentActivityProps {
  recentNetworks: Network[];
  recentStores: Store[];
}

export function RecentActivity({ recentNetworks, recentStores }: RecentActivityProps) {
  // Combinar e ordenar por data de criação
  const allActivities = [
    ...recentNetworks.map(network => ({
      id: `network-${network.id}`,
      type: 'network' as const,
      name: network.name,
      description: network.description,
      createdAt: network.created_at,
      icon: <Building2 className="h-4 w-4" />,
      badge: 'Rede',
      badgeVariant: 'networks' as const,
    })),
    ...recentStores.map(store => ({
      id: `store-${store.id}`,
      type: 'store' as const,
      name: store.name,
      description: store.address,
      createdAt: store.created_at,
      icon: <StoreIcon className="h-4 w-4" />,
      badge: 'Loja',
      badgeVariant: 'stores' as const,
    })),
  ]
    .sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
    .slice(0, 5); // Mostrar apenas os 5 mais recentes

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle>Atividade Recente</CardTitle>
        <CardDescription>
          Últimas redes e lojas adicionadas ao sistema
        </CardDescription>
      </CardHeader>
      <CardContent className="p-4 xs:p-6">
        <div className="space-y-3">
          {allActivities.length > 0 ? (
            allActivities.map((activity) => (
              <div key={activity.id} className="flex flex-col xs:flex-row xs:items-center gap-2 xs:gap-3 p-2 xs:p-3 rounded-lg border">
                <div className="flex items-center gap-2 xs:gap-3">
                  <div className="flex-shrink-0">
                    {activity.icon}
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start xs:items-center gap-2 mb-1">
                      <p className="font-medium text-sm xs:text-base leading-tight flex-1 min-w-0 break-words">
                        {activity.name}
                      </p>
                      <Badge 
                        variant="secondary" 
                        className={cn(
                          'text-xs flex-shrink-0 h-fit',
                          activity.badgeVariant === 'networks' 
                            ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-200' 
                            : 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200'
                        )}
                      >
                        {activity.badge}
                      </Badge>
                    </div>
                    <div className="flex items-center gap-1 mt-1">
                      <Clock className="h-3 w-3 text-muted-foreground flex-shrink-0" />
                      <span className="text-xs text-muted-foreground">
                        {formatDate(activity.createdAt)}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <div className="text-center py-6 xs:py-8 text-muted-foreground">
              <Clock className="h-6 w-6 xs:h-8 xs:w-8 mx-auto mb-2" />
              <p className="text-sm xs:text-base">Nenhuma atividade recente encontrada</p>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
