<?php

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('lista produtos com estrutura padronizada', function () {
    Product::factory()->count(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'items',
                'pagination' => ['current_page', 'last_page', 'per_page', 'total'],
            ],
            'message',
            'errors',
        ]);
});

it('filtra produtos por preco minimo', function () {
    $low = Product::factory()->create(['price' => 50.00]);
    $mid = Product::factory()->create(['price' => 150.00]);
    $high = Product::factory()->create(['price' => 300.00]);

    $response = $this->getJson('/api/products?min_price=120');

    $response->assertOk()
        ->assertJsonPath('errors', null)
        ->assertJsonCount(2, 'data.items');

    $ids = collect($response->json('data.items'))->pluck('id')->all();

    expect($ids)
        ->toContain($mid->id, $high->id)
        ->not->toContain($low->id);
});

it('filtra produtos por preco maximo', function () {
    $low = Product::factory()->create(['price' => 40.00]);
    $mid = Product::factory()->create(['price' => 120.00]);
    $high = Product::factory()->create(['price' => 260.00]);

    $response = $this->getJson('/api/products?max_price=130');

    $response->assertOk()
        ->assertJsonPath('errors', null)
        ->assertJsonCount(2, 'data.items');

    $ids = collect($response->json('data.items'))->pluck('id')->all();

    expect($ids)
        ->toContain($low->id, $mid->id)
        ->not->toContain($high->id);
});

it('filtra produtos por estoque minimo', function () {
    $low = Product::factory()->create(['stock' => 2]);
    $mid = Product::factory()->create(['stock' => 8]);
    $high = Product::factory()->create(['stock' => 20]);

    $response = $this->getJson('/api/products?min_stock=8');

    $response->assertOk()
        ->assertJsonPath('errors', null)
        ->assertJsonCount(2, 'data.items');

    $ids = collect($response->json('data.items'))->pluck('id')->all();

    expect($ids)
        ->toContain($mid->id, $high->id)
        ->not->toContain($low->id);
});

it('cria um produto', function () {
    $payload = [
        'name' => 'Notebook Ultra',
        'description' => 'Modelo corporativo',
        'price' => 4599.90,
        'stock' => 18,
    ];

    $response = $this->postJson('/api/products', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Notebook Ultra')
        ->assertJsonPath('errors', null);

    $this->assertDatabaseHas('products', ['name' => 'Notebook Ultra']);
});

it('valida dados ao criar um produto', function () {
    Product::factory()->create(['name' => 'Nome Existente']);

    $response = $this->postJson('/api/products', [
        'name' => 'Nome Existente',
        'price' => -1,
        'stock' => -2,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'price', 'stock']);
});

it('rejeita preço zero pois o preço deve ser positivo', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'Produto Teste',
        'price' => 0,
        'stock' => 1,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['price']);
});

it('exibe um produto por id', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('errors', null);
});

it('atualiza um produto', function () {
    $product = Product::factory()->create();

    $response = $this->putJson("/api/products/{$product->id}", [
        'name' => 'Produto Atualizado',
        'description' => 'Descrição nova',
        'price' => 199.99,
        'stock' => 10,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Produto Atualizado');

    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Produto Atualizado']);
});

it('remove um produto', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonPath('data', null)
        ->assertJsonPath('errors', null);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
