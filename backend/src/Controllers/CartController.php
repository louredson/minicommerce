<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Repositories\ProductRepository;

class CartController
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function add(array $body): void
    {
        $productId = (int) ($body['product_id'] ?? 0);
        if ($productId <= 0) JsonResponse::send(['success'=>false,'message'=>'Produto invalido.'],422);
        $product = $this->products->findById($productId);
        if (!$product) JsonResponse::send(['success'=>false,'message'=>'Produto nao encontrado.'],404);

        $cart = $_SESSION['cart'] ?? [];
        $qty = ($cart[$productId] ?? 0) + 1;
        if ($qty > (int)$product['stock']) JsonResponse::send(['success'=>false,'message'=>'Stock insuficiente.'],422);
        $cart[$productId] = $qty;
        $_SESSION['cart'] = $cart;
        JsonResponse::send(['success'=>true,'message'=>'Produto adicionado.','data'=>['count'=>array_sum($cart),'item_qty'=>$qty]]);
    }

    public function update(array $body): void
    {
        $productId = (int) ($body['product_id'] ?? 0);
        $qty = (int) ($body['qty'] ?? 0);
        if ($productId <= 0 || $qty <= 0) JsonResponse::send(['success'=>false,'message'=>'Dados invalidos.'],422);
        $product = $this->products->findById($productId);
        if (!$product) JsonResponse::send(['success'=>false,'message'=>'Produto nao encontrado.'],404);
        if ($qty > (int)$product['stock']) JsonResponse::send(['success'=>false,'message'=>'Quantidade acima do stock.'],422);

        $cart = $_SESSION['cart'] ?? [];
        $cart[$productId] = $qty;
        $_SESSION['cart'] = $cart;
        JsonResponse::send(['success'=>true,'message'=>'Carrinho atualizado.','data'=>['count'=>array_sum($cart)]]);
    }

    public function remove(array $body): void
    {
        $productId = (int) ($body['product_id'] ?? 0);
        $cart = $_SESSION['cart'] ?? [];
        unset($cart[$productId]);
        $_SESSION['cart'] = $cart;
        JsonResponse::send(['success'=>true,'message'=>'Produto removido do carrinho.','data'=>['count'=>array_sum($cart)]]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
        JsonResponse::send(['success'=>true,'message'=>'Compra cancelada. Carrinho limpo.']);
    }
}
