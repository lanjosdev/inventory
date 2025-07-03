import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Dashboard | Plataforma de Mídia Ads',
  description: 'Visão geral do sistema de gerenciamento de supermercados',
};

export default function DashboardPage() {
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Dashboard</h1>
      <p className="text-slate-600">Sistema funcionando corretamente</p>
    </div>
  );
}