# Plataforma de Mídia Ads - Frontend

Este projeto é uma aplicação web frontend em Next.js 14 (App Router) que permite aos usuários realizar operações CRUD (Criar, Ler, Atualizar, Excluir) para redes de supermercados e suas filiais/lojas associadas. As requisições são feitas a um backend externo via API REST.

O objetivo é construir uma aplicação com código limpo, escalável, seguro e de fácil manutenção, seguindo princípios de arquitetura limpa e boas práticas de desenvolvimento de software.

## Tecnologias Principais

- **Framework:** Next.js 14 (com App Router)
- **Linguagem:** TypeScript
- **Estilização:** Tailwind CSS
- **Biblioteca de Componentes:** shadcn/ui
- **Formulários:** React Hook Form
- **Validação:** Zod



### Componentes Reutilizáveis

#### `PasswordInput`
```tsx
<PasswordInput
  id="password"
  {...form.register('password')}
  className="custom-styles"
/>
```

#### `FormMessage` e `FormError`
```tsx
<FormMessage type="success" message="Login realizado com sucesso!" />
<FormError message={form.formState.errors.email?.message} />
```

### Hook Personalizado

O hook `useLoginForm` encapsula toda a lógica do formulário:

```tsx
const { form, onSubmit, isPending, message } = useLoginForm()
```

## Estrutura do Projeto

```
src/
├── app/                    # Rotas do Next.js 14 (App Router)
├── components/            # Componentes reutilizáveis
│   ├── ui/               # Componentes base (shadcn/ui)
│   └── login-form.tsx    # Formulário de login
├── hooks/                # Hooks customizados
│   └── use-login-form.ts # Hook do formulário de login
├── lib/                  # Utilitários e configurações
│   ├── actions/          # Server Actions
│   ├── validations/      # Schemas de validação Zod
│   └── utils.ts          # Funções utilitárias
└── types/                # Definições de tipos TypeScript
```

## Primeiros Passos

Para executar o servidor de desenvolvimento:

```bash
npm run dev
# ou
yarn dev
# ou
pnpm dev
```

Abra [http://localhost:3000](http://localhost:3000) no seu navegador para ver o resultado.