<?php

namespace App\Repositories;

use PDO;

class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function createOrder(int $userId, float $total, string $status = 'Pendente'): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $total, $status]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addItem(int $orderId, int $productId, int $qty, float $unitPrice): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
        $stmt->execute([$orderId, $productId, $qty, $unitPrice]);
    }

    public function listByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function listAll(?string $from = null, ?string $to = null): array
    {
        $sql = 'SELECT o.id, o.total_amount, o.status, o.created_at, u.name as customer_name FROM orders o JOIN users u ON u.id = o.user_id';
        $where = [];
        $params = [];
        if ($from) { $where[] = 'DATE(o.created_at) >= ?'; $params[] = $from; }
        if ($to) { $where[] = 'DATE(o.created_at) <= ?'; $params[] = $to; }
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY o.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listItemsByOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT p.name, oi.quantity, oi.unit_price FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
    }
}
