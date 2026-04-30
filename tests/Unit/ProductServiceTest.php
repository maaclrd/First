<?php

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Pagination\LengthAwarePaginator;

it('retorna lista paginada de produtos', function () {
    $repository = \Mockery::mock(ProductRepositoryInterface::class);
    $paginator = new LengthAwarePaginator([], 0, 10, 1);

    $repository->shouldReceive('all')
        ->once()
        ->with(['search' => 'abc'])
        ->andReturn($paginator);

    $service = new ProductService($repository);

    expect($service->all(['search' => 'abc']))->toBe($paginator);
});

it('retorna produto por id', function () {
    $repository = \Mockery::mock(ProductRepositoryInterface::class);
    $product = new Product(['name' => 'Produto', 'price' => 10, 'stock' => 5]);

    $repository->shouldReceive('findById')->once()->with(1)->andReturn($product);

    $service = new ProductService($repository);

    expect($service->findById(1))->toBe($product);
});

it('cria produto via repositorio', function () {
    $repository = \Mockery::mock(ProductRepositoryInterface::class);
    $payload = ['name' => 'Novo', 'price' => 20.50, 'stock' => 3];
    $product = new Product($payload);

    $repository->shouldReceive('create')->once()->with($payload)->andReturn($product);

    $service = new ProductService($repository);

    expect($service->create($payload))->toBe($product);
});

it('atualiza produto via repositorio', function () {
    $repository = \Mockery::mock(ProductRepositoryInterface::class);
    $payload = ['name' => 'Atualizado', 'price' => 30, 'stock' => 4];
    $product = new Product($payload);

    $repository->shouldReceive('update')->once()->with(2, $payload)->andReturn($product);

    $service = new ProductService($repository);

    expect($service->update(2, $payload))->toBe($product);
});

it('remove produto via repositorio', function () {
    $repository = \Mockery::mock(ProductRepositoryInterface::class);

    $repository->shouldReceive('delete')->once()->with(5)->andReturn(true);

    $service = new ProductService($repository);

    expect($service->delete(5))->toBeTrue();
});
