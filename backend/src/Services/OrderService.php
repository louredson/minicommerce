<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use InvalidArgumentException;
use PDO;

class OrderService
{
    public function __construct(
        private PDO $pdo,
        private ProductRepository $products,
        private OrderRepository $orders
    ) {
    }

    public function checkout(int $userId, array $cart): array
    {
        if (!$cart) {
            throw new InvalidArgumentException('Carrinho vazio.');
        }

        $productIds = array_keys($cart);
        $dbProducts = $this->products->findManyByIds($productIds);

        $total = 0.0;
        foreach ($cart as $productId => $qty) {
            if (!isset($dbProducts[$productId])) {
                throw new InvalidArgumentException('Produto inexistente no carrinho.');
            }
            $stock = (int) $dbProducts[$productId]['stock'];
            if ($qty > $stock) {
                throw new InvalidArgumentException('Stock insuficiente para ' . $dbProducts[$productId]['name']);
            }
            $total += (float) $dbProducts[$productId]['price'] * $qty;
        }

        $this->pdo->beginTransaction();
        try {
            $orderId = $this->orders->createOrder($userId, $total, 'Pendente');
            foreach ($cart as $productId => $qty) {
                $unit = (float) $dbProducts[$productId]['price'];
                $this->orders->addItem($orderId, (int) $productId, $qty, $unit);
                $this->products->reduceStock((int) $productId, $qty);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return ['order_id' => $orderId, 'total_amount' => $total];
    }
}
