

// import { DashboardContent } from '@/components/dashboard/dashboard-content';
import { DashboardFallback } from '@/components/dashboard/dashboard-fallback';

export default function DashboardPage() {
  // Durante desenvolvimento, você pode alternar entre DashboardContent e DashboardFallback
  // DashboardContent: conecta com a API real
  // DashboardFallback: usa dados mockados para desenvolvimento
  
  return (
    <>
      {/* Usando DashboardFallback para desenvolvimento - mude para DashboardContent quando a API estiver pronta */}
      <DashboardFallback />
      
      {/* Descomente a linha abaixo quando a API estiver configurada */}
      {/* <DashboardContent /> */}
    </>
  );
}
