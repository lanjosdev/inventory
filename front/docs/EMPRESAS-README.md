# Sistema de Gerenciamento de Empresas/Redes

Este sistema permite o gerenciamento completo de empresas/redes de supermercados e seus contatos através de operações CRUD (Create, Read, Update, Delete).

## Funcionalidades Implementadas

### ✅ CRUD Completo de Empresas
- **Criar**: Adicionar novas empresas com múltiplos contatos
- **Listar**: Visualizar todas as empresas com paginação
- **Editar**: Atualizar informações da empresa e contatos
- **Excluir**: Remover empresas com confirmação de segurança
- **Detalhes**: Visualizar informações completas de uma empresa

### ✅ Gerenciamento de Contatos
- Múltiplos contatos por empresa
- Campos: nome, e-mail, telefone, observações
- Validação completa dos dados
- Interface dinâmica para adicionar/remover contatos

### ✅ Interface Moderna
- Design responsivo com Tailwind CSS
- Componentes shadcn/ui para consistência
- Feedback visual com toasts
- Confirmações de ações destrutivas
- Skeleton loading para melhor UX

### ✅ Funcionalidades Avançadas
- Busca em tempo real
- Paginação automática
- Estatísticas em tempo real
- Validação de formulários com Zod
- Gerenciamento de estado otimizado

## Estrutura do Projeto

```
src/
├── app/
│   └── (auth)/
│       └── redes/
│           └── page.tsx              # Página principal
├── components/
│   ├── companies/
│   │   ├── companies-page.tsx        # Componente principal
│   │   ├── company-form.tsx          # Formulário de criação/edição
│   │   ├── company-list.tsx          # Lista de empresas
│   │   ├── company-details.tsx       # Detalhes da empresa
│   │   ├── delete-confirmation.tsx   # Modal de confirmação
│   │   ├── pagination.tsx            # Componente de paginação
│   │   └── index.ts                  # Exports
│   └── ui/                           # Componentes shadcn/ui
├── hooks/
│   └── use-companies.ts              # Hook personalizado
├── lib/
│   ├── actions/
│   │   └── companyActions.ts         # Server Actions
│   └── validations/
│       └── company.ts                # Schemas Zod
├── services/
│   └── companyService.ts             # Service para API
└── types/
    └── index.ts                      # Tipos TypeScript
```

## Tecnologias Utilizadas

- **Frontend**: Next.js 14 (App Router)
- **Linguagem**: TypeScript
- **Estilização**: Tailwind CSS
- **Componentes**: shadcn/ui
- **Validação**: Zod
- **Formulários**: React Hook Form
- **Estado**: React hooks personalizados

## Como Usar

### 1. Acessar a Página
Navegue para `/redes` para acessar o sistema de empresas.

### 2. Criar Nova Empresa
1. Clique em "Nova Empresa"
2. Preencha o nome da empresa
3. Adicione pelo menos um contato
4. Clique em "Criar"

### 3. Listar Empresas
- Visualize todas as empresas na página principal
- Use a barra de busca para filtrar
- Navegue entre páginas usando a paginação

### 4. Visualizar Detalhes
1. Clique no menu "⋮" na empresa desejada
2. Selecione "Visualizar"
3. Veja informações completas e estatísticas

### 5. Editar Empresa
1. Clique no menu "⋮" na empresa desejada
2. Selecione "Editar"
3. Modifique as informações necessárias
4. Clique em "Atualizar"

### 6. Excluir Empresa
1. Clique no menu "⋮" na empresa desejada
2. Selecione "Excluir"
3. Confirme a ação na modal de confirmação

### 7. Buscar Empresas
- Use a barra de busca para filtrar por:
  - Nome da empresa
  - Nome do contato
  - E-mail do contato

## Validações Implementadas

### Empresa
- Nome obrigatório
- Pelo menos um contato obrigatório

### Contato
- Nome obrigatório
- E-mail válido obrigatório
- Telefone obrigatório
- Observações opcionais

## Recursos de UX

### Feedback Visual
- Toasts de sucesso/erro
- Loading states
- Skeleton loading
- Confirmações de ações

### Responsividade
- Layout adaptativo
- Funciona em desktop, tablet e mobile
- Componentes otimizados para touch

### Acessibilidade
- Navegação por teclado
- Labels adequados
- Contraste de cores
- Feedback de erros

## API Integration

O sistema está integrado com a API REST conforme especificação Swagger:

### Endpoints Utilizados
- `GET /api/companies` - Listar empresas
- `POST /api/companies` - Criar empresa
- `GET /api/companies/{id}` - Buscar empresa
- `PUT /api/companies/{id}` - Atualizar empresa
- `DELETE /api/companies/{id}` - Excluir empresa

### Autenticação
- Bearer token automático
- Redirecionamento para login se não autenticado
- Refresh token quando necessário

## Comandos Úteis

```bash
# Instalar dependências
npm install

# Executar em desenvolvimento
npm run dev

# Fazer build
npm run build

# Executar testes
npm run test

# Linting
npm run lint
```

## Próximos Passos

1. **Testes**: Implementar testes unitários e e2e
2. **Otimizações**: Cache de queries, virtualization
3. **Exportação**: Funcionalidade de exportar dados
4. **Filtros**: Filtros avançados por data, status, etc.
5. **Dashboard**: Gráficos e métricas avançadas

## Estrutura de Dados

### Empresa/Rede
```typescript
interface Company {
  id_company: number
  name: string
  created_at: string
  updated_at: string
  contacts: Contact[]
}
```

### Contato
```typescript
interface Contact {
  id_contact: number
  name: string
  email: string
  phone: string
  observation?: string | null
}
```

## Considerações de Segurança

- Validação tanto no frontend quanto backend
- Sanitização de dados de entrada
- Confirmação para ações destrutivas
- Tokens de autenticação seguros
- Prevenção de XSS e CSRF
