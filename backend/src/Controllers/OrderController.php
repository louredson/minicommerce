<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use InvalidArgumentException;

class OrderController
{
    public function __construct(
        private OrderService $service,
        private OrderRepository $orders,
        private ProductRepository $products
    ) {
    }

    public function cartSummary(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $ids = array_map('intval', array_keys($cart));
        if (!$ids) {
            JsonResponse::send(['success' => true, 'data' => ['items' => [], 'total' => 0, 'count' => 0]]);
        }

        $products = $this->products->findManyByIds($ids);
        $items = [];
        $total = 0;
        foreach ($cart as $productId => $qty) {
            if (!isset($products[$productId])) {
                continue;
            }
            $price = (float) $products[$productId]['price'];
            $subtotal = $price * $qty;
            $total += $subtotal;
            $items[] = [
                'id' => (int) $productId,
                'name' => $products[$productId]['name'],
                'price' => $price,
                'qty' => (int) $qty,
                'subtotal' => $subtotal,
                'stock' => (int) $products[$productId]['stock'],
            ];
        }

        JsonResponse::send(['success' => true, 'data' => ['items' => $items, 'total' => $total, 'count' => array_sum($cart)]]);
    }

    public function checkout(int $userId): void
    {
        try {
            $result = $this->service->checkout($userId, $_SESSION['cart'] ?? []);
            $_SESSION['cart'] = [];
            JsonResponse::send(['success' => true, 'message' => 'Compra concluida.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function myOrders(int $userId): void
    {
        $orders = $this->orders->listByUser($userId);
        foreach ($orders as &$order) {
            $order['items'] = $this->orders->listItemsByOrder((int) $order['id']);
        }
        unset($order);

        JsonResponse::send(['success' => true, 'data' => $orders]);
    }
}
