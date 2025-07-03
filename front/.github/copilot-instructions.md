# Instruções para o GitHub Copilot (Copilot Instructions)

Este projeto é uma aplicação web **frontend** em **Next.js 14 (App Router)** que permite aos usuários realizar operações **CRUD** (Criar, Ler, Atualizar, Excluir) para **redes de supermercados** e suas **filiais/lojas** associadas a cada rede. 

As requisições são feitas a um **backend externo** via **API REST**.  

Nosso objetivo é garantir que o código seja **limpo, escalável, seguro e de fácil manutenção**, seguindo princípios de **arquitetura limpa** e **boas práticas de desenvolvimento de software**.

## Tecnologias Principais

O Copilot deve priorizar o uso das seguintes tecnologias e padrões:

- **Framework:** Next.js 14 (com App Router)
- **Linguagem:** TypeScript
- **Estilização:** Tailwind CSS
- **Biblioteca de Componentes:** shadcn/ui

## Padrões de Codificação

### Geral
- Utilize **arquitetura limpa** para organizar os componentes e páginas de forma modular e escalável.
- Garanta **segurança** ao validar entradas de usuários utilizando Zod e ao restringir acessos mediante autenticação.
- Escreva código **manutenível** com comentários claros e seguindo boas práticas de desenvolvimento.

### TypeScript
- Utilize **TypeScript** para tipagem estática e evite o uso de `any` sempre que possível.
- Utilize **camelCase** para nomes de variáveis, funções e arquivos não relacionados a componentes.
- Utilize **PascalCase** para nomes de componentes e classes.
- Utilize aspas simples para strings.
- Utilize **async/await** para lidar com código assíncrono.
- Utilize **const** por padrão; use **let** somente se precisar reatribuir.  
- Utilize **template literals** para strings que contém variáveis.
- Use **.tsx** para componentes e **.ts** para lógica pura.  
- Utilize os recursos mais recentes do JavaScript (ES6+) sempre que possível.

### Next.js 14 (App Router)
- Prefira **Server Components** para buscar dados e lógica de servidor, aproveitando a renderização no lado do servidor e otimização de cache.
- Utilize **Client Components** para interatividade, gerenciamento de estado do cliente e hooks do React (e.g., `useState`, `useEffect`).
- Para chamadas de API, utilize o `fetch` nativo com `async/await`, aproveitando os mecanismos de cache do Next.js quando apropriado.
- Organize as rotas e componentes dentro do diretório `app/`.

### Estilo e Design
- Utilize **Tailwind CSS** para estilização e foque na criação de componentes reutilizáveis.
- Utilize **shadcn/ui** para componentes de UI consistentes e acessíveis.
- Personalize os componentes de `shadcn/ui` através das props ou sobrepondo classes do Tailwind para manter a consistência visual do projeto.
- Garanta que todas as páginas sigam um **padrão visual consistente**, mantendo identidade, espaçamentos, tipografia e componentes alinhados ao design do projeto.
- Garanta a **responsividade** e acessibilidade em todos os componentes.

### Validação e Formulários
- Garanta boas validações de dados.
- Certifique-se de sempre validar os dados antes de enviá-los à API.

## Resumo de Boas Práticas
- Componentização reutilizável.
- Validação e segurança robusta.
- Modularização e separação de preocupações.
- Testes automatizados e documentação clara.

## Segurança

- Nunca exponha **tokens** ou **chaves** no código; use variáveis de ambiente.  
- Valide e sanitize todos os dados antes de enviar à API.  
- Evite `dangerouslySetInnerHTML`; se necessário, sanitize o conteúdo.  
- Ative **Strict Mode** no TypeScript.

## Documentação & Comentários

- Comentários JSDoc apenas em funções complexas (services, hooks).  
- Atualize o **README.md** com instruções de setup, exemplos de uso dos CRUDs e comandos comuns.  
- Use **commit messages** descritivos e consistentes (Ex.: “feat: add createNetwork mutation”).