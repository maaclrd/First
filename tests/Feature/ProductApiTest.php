<?php

use App\Models\Product;

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
