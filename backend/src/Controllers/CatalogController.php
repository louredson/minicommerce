<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Repositories\ProductRepository;

class CatalogController
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function categories(): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->products->categories()]);
    }

    public function products(?int $categoryId): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->products->products($categoryId)]);
    }

    public function productById(int $id): void
    {
        $product = $this->products->findById($id);
        if (!$product) JsonResponse::send(['success'=>false,'message'=>'Produto nao encontrado.'],404);
        JsonResponse::send(['success'=>true,'data'=>$product]);
    }
}
