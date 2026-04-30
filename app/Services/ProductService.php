<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(private readonly ProductRepositoryInterface $productRepository) {}

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->all($filters);
    }

    public function findById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data): ?Product
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
