// import { redirect } from 'next/navigation';
import AuthHeader from '@/components/auth/AuthHeader';

/**
 * Layout para área autenticada do sistema.
 * Inclui header de navegação e validação de autenticação.
 */
export default async function AuthLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // TODO: Implementar verificação de autenticação
  // const session = await getServerSession();
  // if (!session) {
  //   redirect('/login');
  // }

  return (
    <div className="min-h-screen bg-slate-50">
      <AuthHeader />
      
      <main className="container mx-auto px-4 py-8">
        {children}
      </main>
    </div>
  );
}