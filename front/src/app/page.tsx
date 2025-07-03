import Link from "next/link";

export default function HomePage() {
  return (
    <div className="min-h-screen-mobile flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 p-4 sm:p-8">
      <div className="max-w-4xl w-full text-center space-y-6 sm:space-y-8 flex-1 flex flex-col justify-center">
        <section>
          <h1 className="text-3xl sm:text-5xl font-bold tracking-tight text-slate-900 mb-4 sm:mb-6">
            Sistema de Gerenciamento de Mídias Publicitárias
            <span className="text-blue-600" aria-hidden="true">
              .
            </span>
          </h1>

          <p className="text-lg sm:text-xl text-slate-700 max-w-3xl mx-auto leading-relaxed">
            Plataforma completa para gerenciamento de campanhas publicitárias em
            redes de supermercados. Controle total das suas mídias em uma única
            solução.
          </p>
        </section>

        <section className="pt-6 sm:pt-8">
          <Link
            href="/login"
            className="inline-block px-8 py-4 sm:px-10 sm:py-5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-200 shadow-lg hover:shadow-xl font-semibold text-base sm:text-lg"
            aria-label="Acessar área de login do sistema"
          >
            Fazer Login
          </Link>
        </section>
      </div>

      <footer className="mt-auto pt-6 sm:pt-8 text-center text-xs sm:text-sm text-slate-500">
        <p>
          &copy; 2025 Plataforma de Mídia Ads. Todos os direitos reservados.
        </p>
      </footer>
    </div>
  );
}
