<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 10);
        $search = trim((string) ($filters['search'] ?? ''));
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;
        $minStock = $filters['min_stock'] ?? null;

        $query = Product::query()->latest('id');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }
        if ($minStock !== null && $minStock !== '') {
            $query->where('stock', '>=', (int) $minStock);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): ?Product
    {
        $product = Product::find($id);

        if (! $product) {
            return null;
        }

        $product->update($data);

        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        $product = Product::find($id);

        if (! $product) {
            return false;
        }

        return (bool) $product->delete();
    }
}
