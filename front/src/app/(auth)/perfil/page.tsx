export default function PerfilPage() {
  return (
    <div className="flex-1 space-y-4 p-4 pt-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold tracking-tight">Perfil do Usuário</h1>
      </div>
      
      <div className="grid gap-4">
        <div className="rounded-lg border p-4">
          <h2 className="text-lg font-semibold mb-4">Informações Pessoais</h2>
          <p className="text-sm text-muted-foreground">
            Em breve você poderá visualizar e editar suas informações pessoais aqui.
          </p>
        </div>
        
        <div className="rounded-lg border p-4">
          <h2 className="text-lg font-semibold mb-4">Configurações</h2>
          <p className="text-sm text-muted-foreground">
            Em breve você poderá configurar suas preferências aqui.
          </p>
        </div>
      </div>
    </div>
  )
}
