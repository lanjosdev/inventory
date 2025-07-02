# TESTS.md - Guia de Testes da Aplicação

## 1. Objetivo
Este documento centraliza as diretrizes e exemplos para criação, organização e execução de testes automatizados (unitários e de integração) na aplicação.

## 2. Estrutura dos Testes
- Os testes ficam na pasta `tests/`.
- Use `tests/Unit` para testes unitários de models, helpers e regras de negócio isoladas.
- Use `tests/Feature` para testes de endpoints, controllers e fluxos completos.

## 3. Boas Práticas
- Todo controller, model e helper deve ter cobertura mínima de testes.
- Testes devem cobrir casos de sucesso, falha de validação, exceções e fluxos alternativos.
- Use factories para criar dados de teste.
- Utilize transações nos testes para garantir isolamento (`RefreshDatabase`).
- Nomeie métodos de teste de forma descritiva: `test_usuario_nao_autenticado_nao_pode_criar_loja`.

## 4. Exemplos

### Teste unitário de Model
```php
test('store model cria loja corretamente', function () {
    $store = Store::factory()->create(['name' => 'Loja Teste']);
    expect($store->name)->toBe('Loja Teste');
});
```

### Teste de endpoint (Feature)
```php
test('usuário autenticado pode criar loja', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $response = $this->withToken($token)->postJson('/api/stores', [
        'name' => 'Loja Nova',
        'fk_companie' => 1,
        'cnpj' => '12345678901234',
        'country' => 'Brasil',
        'state' => 'SP',
        'city' => 'Campinas',
        'address' => 'Rua X',
        'cep' => '123456789',
    ]);
    $response->assertStatus(201);
});
```

## 5. Execução dos Testes

Rode todos os testes com:
```bash
php artisan test
```
Ou, para rodar um teste específico:
```bash
php artisan test --filter=nomeDoTeste
```

## 6. Cobertura
- Busque sempre aumentar a cobertura dos testes, principalmente para regras de negócio críticas e integrações.
- Use ferramentas como PHPUnit e Pest para facilitar a escrita e leitura dos testes.

---

> **Todo novo código deve ser acompanhado de testes automatizados. Consulte este guia sempre que criar ou alterar funcionalidades.**
