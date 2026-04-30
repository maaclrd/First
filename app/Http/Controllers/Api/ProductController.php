<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    #[OA\Get(
        path: '/api/products',
        tags: ['Produtos'],
        summary: 'Lista produtos',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Lista de produtos')]
    )]
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->all($request->only(['search', 'per_page']));

        return response()->json([
            'data' => [
                'items' => ProductResource::collection($products->items()),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ],
            'message' => 'Produtos listados com sucesso.',
            'errors' => null,
        ]);
    }

    #[OA\Post(
        path: '/api/products',
        tags: ['Produtos'],
        summary: 'Cria produto',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'price', type: 'number', format: 'float'),
                    new OA\Property(property: 'stock', type: 'integer'),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: 'Produto criado')]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Produto criado com sucesso.',
            'errors' => null,
        ], 201);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        tags: ['Produtos'],
        summary: 'Visualiza produto',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Produto encontrado'),
            new OA\Response(response: 404, description: 'Produto não encontrado'),
        ]
    )]
    public function show(int $product): JsonResponse
    {
        $found = $this->productService->findById($product);

        if (! $found) {
            return response()->json([
                'data' => null,
                'message' => 'Produto não encontrado.',
                'errors' => ['product' => ['Registro não localizado.']],
            ], 404);
        }

        return response()->json([
            'data' => new ProductResource($found),
            'message' => 'Produto carregado com sucesso.',
            'errors' => null,
        ]);
    }

    #[OA\Put(
        path: '/api/products/{id}',
        tags: ['Produtos'],
        summary: 'Atualiza produto',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'price', type: 'number', format: 'float'),
                    new OA\Property(property: 'stock', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Produto atualizado'),
            new OA\Response(response: 404, description: 'Produto não encontrado'),
        ]
    )]
    public function update(UpdateProductRequest $request, int $product): JsonResponse
    {
        $updated = $this->productService->update($product, $request->validated());

        if (! $updated) {
            return response()->json([
                'data' => null,
                'message' => 'Produto não encontrado.',
                'errors' => ['product' => ['Registro não localizado.']],
            ], 404);
        }

        return response()->json([
            'data' => new ProductResource($updated),
            'message' => 'Produto atualizado com sucesso.',
            'errors' => null,
        ]);
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        tags: ['Produtos'],
        summary: 'Remove produto',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Produto removido'),
            new OA\Response(response: 404, description: 'Produto não encontrado'),
        ]
    )]
    public function destroy(int $product): JsonResponse
    {
        $deleted = $this->productService->delete($product);

        if (! $deleted) {
            return response()->json([
                'data' => null,
                'message' => 'Produto não encontrado.',
                'errors' => ['product' => ['Registro não localizado.']],
            ], 404);
        }

        return response()->json([
            'data' => null,
            'message' => 'Produto removido com sucesso.',
            'errors' => null,
        ]);
    }
}
