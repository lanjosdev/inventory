<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

# Plataforma Midia Ads – Inventory API

API RESTful para gestão de inventário, empresas, lojas, setores, contatos e ativos, construída em Laravel 11, com autenticação via Sanctum, autorização granular (Spatie), importação de dados via CSV e documentação automática via Swagger.

---

## Sumário
- [Visão Geral](#visão-geral)
- [Entidades Principais](#entidades-principais)
- [Instalação e Setup](#instalação-e-setup)
- [Importação de Dados](#importação-de-dados)
- [Autenticação e Autorização](#autenticação-e-autorização)
- [Testes Automatizados](#testes-automatizados)
- [Documentação da API](#documentação-da-api)
- [Boas Práticas e Padrões](#boas-práticas-e-padrões)

---

## Visão Geral
Esta API permite:
- Gerenciar usuários, empresas, lojas, setores, contatos, status, ações e ativos (assets).
- Importar dados em massa via arquivos CSV.
- Autenticação e autorização robustas.
- Documentação automática dos endpoints (Swagger).
- Testes automatizados (unitários e de integração).

## Entidades Principais
- **Usuários**: cadastro, autenticação, permissões e papéis.
- **Empresas**: múltiplos contatos, relacionamento com lojas.
- **Lojas**: associadas a empresas, múltiplos contatos, CNPJ.
- **Setores**: agrupamento de lojas/ativos.
- **Contatos**: vinculados a empresas e lojas.
- **Status**: controle de situação de ativos.
- **Ações**: tipos de movimentações ou eventos.
- **Assets (Ativos)**: itens do inventário, vinculados a loja, setor, tipo e status.

## Instalação e Setup
1. Clone o repositório
2. Instale as dependências:
   ```bash
   composer install
   npm install
   ```
3. Configure o ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Ajuste as variáveis do .env conforme seu ambiente
   ```
4. Rode as migrations e seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

## Importação de Dados
Importe dados em massa usando comandos artisan customizados:

- Importar lojas e filiais:
  ```bash
  php artisan import:csv public/testeFiliais.csv
  ```
- Importar locais de veiculação:
  ```bash
  php artisan import:locais-veiculacao public/locais.csv
  ```

Consulte o arquivo `info.txt` para exemplos de uso.

## Autenticação e Autorização
- **Autenticação**: via Sanctum. Obtenha um token no endpoint de login e envie no header:
  ```http
  Authorization: Bearer {token}
  ```
- **Autorização**: baseada em papéis e permissões (Spatie Laravel Permission).
- **Rotas protegidas**: utilize o token para acessar endpoints autenticados.

## Testes Automatizados
- Execute todos os testes:
  ```bash
  php artisan test
  ```
- Estrutura de testes:
  - `tests/Unit`: regras de negócio, models, helpers.
  - `tests/Feature`: endpoints, controllers, fluxos completos.
- Use factories e transações para isolamento dos testes.
- Exemplos e boas práticas em [`TESTS.md`](./TESTS.md).

## Documentação da API
- Documentação automática via Swagger:
  ```bash
  php artisan l5-swagger:generate --all
  ```
- Acesse a documentação gerada em `/api/documentation` após rodar o comando acima.

## Boas Práticas e Padrões
- Siga sempre o guia de boas práticas em [`.github/copilot-instruction.md`](..github/copilot-instruction.md) (try/catch, transações, documentação, etc).
- Código limpo, validado e documentado.
- Utilize migrations, seeders e factories para manter o banco consistente.

---

> Dúvidas, sugestões ou problemas? Consulte a documentação ou abra uma issue.
