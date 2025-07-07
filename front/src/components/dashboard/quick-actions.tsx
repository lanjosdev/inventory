'use client';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Store, Building2, Search, Settings } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useRouter } from 'next/navigation';

interface QuickAction {
  id: string;
  title: string;
  description: string;
  icon: React.ReactNode;
  onClick: () => void;
  variant?: 'default' | 'outline' | 'secondary';
  colorClass?: string;
}

export function QuickActions() {
  const router = useRouter();

  const actions: QuickAction[] = [
    {
      id: 'add-network',
      title: 'Nova Rede',
      description: 'Criar uma nova rede de supermercados',
      icon: <Building2 className="h-4 w-4 text-blue-600 dark:text-blue-400" />,
      onClick: () => {
        router.push('/redes/cadastrar');
      },
      variant: 'default',
      colorClass: 'hover:bg-blue-50 hover:border-blue-200 dark:hover:bg-blue-950/50 border-blue-100 bg-blue-50/30 text-blue-700 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-300',
    },
    {
      id: 'add-store',
      title: 'Nova Loja',
      description: 'Adicionar uma nova loja a uma rede',
      icon: <Store className="h-4 w-4 text-green-600 dark:text-green-400" />,
      onClick: () => {
        // TODO: Ajustar a rota para o cadastro de loja quando estiver pronta
        console.log('/lojas/cadastrar');
      },
      variant: 'default',
      colorClass: 'hover:bg-green-50 hover:border-green-200 dark:hover:bg-green-950/50 border-green-100 bg-green-50/30 text-green-700 dark:border-green-800 dark:bg-green-950/30 dark:text-green-300',
    },
    {
      id: 'search',
      title: 'Buscar',
      description: 'Pesquisar redes ou lojas',
      icon: <Search className="h-4 w-4" />,
      onClick: () => {
        // TODO: Implementar navegação para busca
        console.log('Buscar');
      },
      variant: 'outline',
    },
    {
      id: 'settings',
      title: 'Configurações',
      description: 'Gerenciar configurações do sistema',
      icon: <Settings className="h-4 w-4" />,
      onClick: () => {
        // TODO: Implementar navegação para configurações
        console.log('Configurações');
      },
      variant: 'outline',
    },
  ];

  return (
    <Card className="w-full">
      <CardHeader className="pb-3 sm:pb-6">
        <CardTitle className="text-base sm:text-lg">Ações Rápidas</CardTitle>
        <CardDescription className="text-sm">
          Acesse rapidamente as funcionalidades mais utilizadas
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div className="grid gap-3 sm:grid-cols-2 sm:gap-4">
          {actions.map((action) => (
            <Button
              key={action.id}
              variant={action.variant}
              className={cn(
                'h-auto min-h-[70px] sm:min-h-[80px] p-3 sm:p-4 justify-start transition-colors whitespace-normal text-left overflow-hidden',
                action.colorClass
              )}
              onClick={action.onClick}
            >
              <div className="flex items-start gap-2 sm:gap-3 w-full">
                <div className="flex-shrink-0 mt-0.5">
                  {action.icon}
                </div>
                <div className="flex-1 min-w-0 max-w-full">
                  <div className="font-medium text-sm leading-tight mb-1 truncate">
                    {action.title}
                  </div>
                  <div className="text-xs text-muted-foreground leading-tight break-words">
                    {action.description}
                  </div>
                </div>
              </div>
            </Button>
          ))}
        </div>
      </CardContent>
    </Card>
  );
}
