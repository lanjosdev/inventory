# Dashboard de Supermercados

Este dashboard fornece uma visão geral do sistema de supermercados, exibindo estatísticas importantes e permitindo acesso rápido às funcionalidades principais.

## Componentes

### DashboardContent
Componente principal que conecta com a API real para buscar dados do sistema.

**Funcionalidades:**
- Busca estatísticas em tempo real
- Exibe loading state durante carregamento
- Trata erros de conexão com a API
- Atualiza automaticamente os dados

### DashboardFallback
Componente para desenvolvimento que usa dados mockados quando a API não está disponível.

**Uso durante desenvolvimento:**
```tsx
// Para desenvolvimento (dados mockados)
<DashboardFallback />

// Para produção (API real)
<DashboardContent />
```

## Seções do Dashboard

### 1. Cards de Estatísticas
- **Total de Redes**: Número total de redes cadastradas (cor azul)
- **Total de Lojas**: Número total de lojas cadastradas (cor verde)
- **Redes Ativas**: Redes em operação (cor azul)
- **Lojas Ativas**: Lojas em operação (cor verde)

### 2. Ações Rápidas
- **Nova Rede**: Criar uma nova rede de supermercados (hover azul)
- **Nova Loja**: Adicionar uma nova loja (hover verde)
- **Buscar**: Pesquisar redes ou lojas
- **Configurações**: Gerenciar configurações do sistema

### 3. Atividade Recente
- Lista das últimas redes e lojas adicionadas
- Badges coloridos: azul para redes, verde para lojas
- Mostra data de criação e informações básicas
- Limitado aos 5 itens mais recentes

## Sistema de Cores

O dashboard utiliza um sistema de cores consistente:

### 🔵 Azul - Redes
- **Cards**: Fundo azul claro com borda azul
- **Ícones**: Azul (#2563eb)
- **Badges**: Fundo azul claro com texto azul escuro
- **Hover**: Efeito azul nos botões de ação

### 🟢 Verde - Lojas/Filiais
- **Cards**: Fundo verde claro com borda verde
- **Ícones**: Verde (#16a34a)
- **Badges**: Fundo verde claro com texto verde escuro
- **Hover**: Efeito verde nos botões de ação

### Modo Escuro
Todas as cores são adaptadas automaticamente para o modo escuro, mantendo a consistência visual.

## API Endpoints

O dashboard consome os seguintes endpoints:

```
GET /dashboard/stats
```

**Resposta esperada:**
```json
{
  "success": true,
  "message": "Estatísticas carregadas com sucesso",
  "data": {
    "total_networks": 12,
    "total_stores": 89,
    "active_networks": 11,
    "active_stores": 85,
    "recent_networks": [...],
    "recent_stores": [...]
  }
}
```

## Configuração

### Variáveis de Ambiente
Certifique-se de que a variável `NEXT_PUBLIC_API_URL` esteja configurada no arquivo `.env.local`:

```
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### Autenticação
O dashboard requer autenticação. O token é automaticamente incluído nas requisições através do serviço de API.

## Desenvolvimento

Para trabalhar no dashboard:

1. Use `DashboardFallback` durante desenvolvimento
2. Implemente as navegações nas ações rápidas
3. Adicione novas estatísticas conforme necessário
4. Teste com dados reais usando `DashboardContent`

## Customização

### Adicionar Nova Estatística
```tsx
<StatsCard
  title="Nova Métrica"
  value={stats.nova_metrica}
  description="Descrição da métrica"
  icon={<NovoIcon className="h-4 w-4" />}
  variant="networks" // ou "stores" para definir a cor
/>
```

### Adicionar Nova Ação Rápida
Edite o array `actions` em `QuickActions`:

```tsx
{
  id: 'nova-acao',
  title: 'Nova Ação',
  description: 'Descrição da ação',
  icon: <NovoIcon className="h-4 w-4" />,
  onClick: () => {
    // Implementar navegação
  },
  colorClass: 'hover:bg-blue-50 hover:border-blue-200', // Para cor azul
  // ou colorClass: 'hover:bg-green-50 hover:border-green-200', // Para cor verde
}
```

### Variantes de Cores Disponíveis
- `variant="networks"`: Aplica tema azul (redes)
- `variant="stores"`: Aplica tema verde (lojas)
- `variant="default"`: Sem cor específica (padrão)
