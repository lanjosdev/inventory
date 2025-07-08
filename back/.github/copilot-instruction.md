# Diretrizes para o Copilot - Guia Central de Boas Práticas e Documentação da Aplicação (Versão Swagger)

# 1. Padrão de Tratamento de Erros (try/catch)
Toda rota de atualização, remoção ou criação deve obrigatoriamente envolver a lógica principal em blocos try/catch.

O bloco try/catch deve capturar ValidationException, QueryException e Exception, garantindo respostas padronizadas e logs de erro.

Utilize sempre um ResponseHelper para padronizar o retorno de sucesso e erro.

# 2. Uso de Transações
Sempre inicie uma transação (DB::beginTransaction()) em rotas de atualização, remoção ou criação de dados.

Em caso de erro, utilize DB::rollBack() para desfazer alterações.

Após sucesso, finalize com DB::commit().

Isso garante integridade dos dados e evita inconsistências em operações críticas.

# 3. Documentação da API (com OpenAPI/Swagger) (SEÇÃO ATUALIZADA)
Todas as rotas RESTful devem ser documentadas usando anotações OpenAPI (Swagger) diretamente no código, acima do método do Controller correspondente. A documentação em API.md está descontinuada.

Paginação: Todos os endpoints de listagem (métodos index) devem ser paginados, com um padrão de 10 itens por página.

Os parâmetros de paginação (page, per_page) devem ser documentados na anotação @OA\Parameter.

Preservação de Filtros: A implementação no controller continua obrigatória com o uso de .appends(request()->all()). Os filtros utilizados também devem ser documentados como @OA\Parameter.

Para cada endpoint, a anotação @OA deve documentar:

Método e URL (@OA\Get(path="/api/..."), @OA\Post(...), etc.)

Tags, Summary e Description para organização.

Parâmetros de entrada (@OA\Parameter para query/path, @OA\RequestBody para body).

Exemplos de respostas de sucesso e erro (@OA\Response).

Schemas de dados (@OA\Schema) para reutilizar modelos de request e response.

Após qualquer alteração nas anotações, é obrigatório rodar o comando: php artisan l5-swagger:generate.

## 3.1. Parâmetros de URL (Path Parameters) - Boas Práticas

Sempre utilize nomes claros, amigáveis e padronizados para os parâmetros de rota (path parameters). O nome do parâmetro deve indicar o recurso relacionado, facilitando o entendimento da URL e da documentação.

**Exemplo de boas práticas:**

- `/companies/{id_company}` (e não apenas `/companies/{id}`)
- `/stores/{id_store}/assets/{id_asset}`

**Regras:**
- Use sempre o prefixo `id_` seguido do nome do recurso no singular (ex: `id_user`, `id_brand`).
- Evite nomes genéricos como apenas `{id}`.
- Documente o parâmetro corretamente na anotação Swagger, usando o mesmo nome da rota.
- No controller, utilize o mesmo nome do parâmetro na assinatura do método.

**Exemplo de anotação Swagger:**

```php
@OA\Parameter(
    name="id_company",
    in="path",
    required=true,
    description="ID da empresa",
    @OA\Schema(type="integer")
)
```

Seguindo esse padrão, a API fica mais legível, intuitiva e fácil de consumir e documentar.

## 3.2. Tabelas Pivot e Nomenclatura de Chaves Estrangeiras

Para tabelas de relacionamento (pivot), utilize sempre o padrão de nomenclatura para colunas de chave estrangeira:

- Use o prefixo `fk_` seguido do nome do recurso no singular, por exemplo: `fk_user`, `fk_brand`, `fk_store`, `fk_role`.
- Nunca use apenas `user_id`, `brand_id`, etc. O padrão deve ser sempre `fk_nome`.
- Esse padrão deve ser seguido em todas as migrations, models, controllers e anotações Swagger.
- Em tabelas pivot, utilize sempre duas (ou mais) colunas com esse padrão, ex: `fk_user`, `fk_brand`.
- O nome da tabela pivot deve ser o nome dos recursos no plural, separados por underline, em ordem alfabética (ex: `brand_user`, `role_user`).

### 3.2.1. Definição de Chaves Estrangeiras com `foreignId` (Boas Práticas)

Para definir chaves estrangeiras nas migrations, utilize o método `foreignId()` combinado com `constrained()`. Essa abordagem é mais limpa, legível e segue as convenções do Laravel, além de facilitar a manutenção.

**Padrão recomendado:**

- Use `foreignId('fk_nome_do_recurso')` para criar a coluna, seguindo o padrão `fk_`.
- Use `constrained('nome_da_tabela_referenciada')` para adicionar a restrição de chave estrangeira.
- Sempre defina as ações `onUpdate` e `onDelete` para garantir a integridade referencial.
    - `onUpdate('cascade')`: Atualiza a chave estrangeira se o ID na tabela pai for alterado.
    - `onDelete('restrict')`: Impede a exclusão do registro pai se houver registros filhos associados. Use `onDelete('cascade')` se os registros filhos devam ser excluídos junto com o pai.

**Exemplo de uso em uma migration:**

```php
Schema::create('store_contacts', function (Blueprint $table) {
        $table->id();
        // Padrão recomendado para chaves estrangeiras
        $table->foreignId('fk_store')->constrained('stores')->onUpdate('cascade')->onDelete('restrict');
        $table->foreignId('fk_contact')->constrained('contacts')->onUpdate('cascade')->onDelete('restrict');
        $table->timestamps();
});
```


**Exemplo de migration para tabela pivot:**

```php
Schema::create('brand_user', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('fk_brand');
    $table->unsignedBigInteger('fk_user');
    $table->foreign('fk_brand')->references('id')->on('brands');
    $table->foreign('fk_user')->references('id')->on('users');
    $table->timestamps();
});
```

Seguindo esse padrão, o relacionamento entre entidades fica claro, padronizado e fácil de manter.

## 3.4. Uso obrigatório de SoftDeletes

Todas as tabelas e todos os models devem implementar o uso de SoftDeletes para garantir que os registros não sejam removidos fisicamente do banco de dados, permitindo restauração e auditoria.

- Em todas as migrations, adicione a chamada `$table->softDeletes();`.
- Em todos os models, utilize o trait `use SoftDeletes;` e adicione o import `use Illuminate\Database\Eloquent\SoftDeletes;`.
- Certifique-se de que as queries padrão do Eloquent considerem apenas registros não deletados, e utilize `withTrashed()` ou `onlyTrashed()` quando necessário.

**Exemplo em migration:**

```php
$table->softDeletes();
```

**Exemplo em model:**

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use SoftDeletes;
    // ...
}
```

O uso de SoftDeletes é obrigatório para todos os recursos do sistema.

# 4. Guia de Software (SEÇÃO ATUALIZADA)
O README.md principal deve servir como um portal para a documentação, contendo:

Descrição do projeto e objetivo.

Como rodar o projeto localmente (instalação, dependências, comandos principais).

Como rodar testes.

Como importar dados via CSV.

Como autenticar e consumir a API.

Instruções para acessar a documentação da API, informando que ela é gerada automaticamente e pode ser acessada pela rota /api/documentation (ou a rota configurada). O link para API.md deve ser removido.

# 5. Boas Práticas Gerais
Sempre use Eloquent para manipulação de dados e relacionamentos.

Centralize regras de validação e feedbacks nos Models.

Use migrations e seeders para versionamento e popular o banco.

Nunca exponha detalhes sensíveis de erros ao usuário final.

Mantenha o código limpo, comentado e padronizado.

# 6. Testes Automatizados
Todo controller, model e helper deve possuir testes automatizados (unitários e/ou de integração).

Siga as diretrizes do arquivo TESTS.md para garantir cobertura e qualidade dos testes.

**Não aprove PRs sem testes para novas features ou correções.**

# 7. Instruções Específicas para o Copilot (IA) (SEÇÃO ATUALIZADA)
A fonte da verdade para a documentação da API agora são as anotações @OA\... nos Controllers.

Para qualquer tarefa de criação ou alteração de endpoint, você deve interagir com as anotações no código-fonte, e não mais com arquivos .md.

Ao criar um novo endpoint:

Implemente a lógica no Controller seguindo as diretrizes 1 e 2.

Adicione um bloco de anotação @OA\... completo acima do novo método.

Execute php artisan l5-swagger:generate para atualizar a documentação.

Ao alterar um endpoint existente:

Modifique a lógica do método no Controller.

Localize e altere o bloco de anotação @OA\... correspondente para que ele reflita precisamente as alterações feitas (novos parâmetros, corpo de resposta, etc.).

**Não crie uma nova entrada de documentação. Edite a existente no código.**

**Execute php artisan l5-swagger:generate.**

**Você está PROIBIDO de ler ou escrever no arquivo API.md. Ele é considerado obsoleto.**

## 8. Logs na aplicação


Todas as ações de atualização, criação ou remoção (exceto listagem geral - get-all - e consulta por ID - get-id) devem obrigatoriamente registrar logs no sistema.

O log deve conter informações cruciais para relatórios e auditoria, incluindo:

- Tipo da ação (criou, editou, removeu, restaurou, etc.)
- Usuário responsável (ID ou identificador)
- Data e hora da operação
- Dados relevantes antes e depois da alteração (quando aplicável)
- Identificador do recurso afetado
- Descrição da ação realizada
**Esses logs são essenciais para rastreamento de atividades e auditoria de segurança.**


**Este arquivo é o coração da aplicação. Toda nova feature, refatoração ou ajuste deve seguir e, se necessário, atualizar este guia.**