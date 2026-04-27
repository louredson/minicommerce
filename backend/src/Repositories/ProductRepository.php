<?php

namespace App\Repositories;

use PDO;

class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function categories(): array
    {
        return $this->pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
    }

    public function products(?int $categoryId): array
    {
        if ($categoryId) {
            $stmt = $this->pdo->prepare('SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url, c.id as category_id, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? ORDER BY p.id DESC');
            $stmt->execute([$categoryId]);
            return $stmt->fetchAll();
        }
        return $this->pdo->query('SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url, c.id as category_id, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url, c.id as category_id, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findManyByIds(array $ids): array
    {
        if (!$ids) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) $map[(int) $row['id']] = $row;
        return $map;
    }

    public function create(array $payload): int { /* unchanged */
        $stmt = $this->pdo->prepare('INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$payload['category_id'],$payload['name'],$payload['description'],$payload['price'],$payload['stock'],$payload['image_url']]);
        return (int) $this->pdo->lastInsertId();
    }
    public function update(int $id, array $payload): void { $stmt=$this->pdo->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image_url=? WHERE id=?'); $stmt->execute([$payload['category_id'],$payload['name'],$payload['description'],$payload['price'],$payload['stock'],$payload['image_url'],$id]); }
    public function delete(int $id): void { $stmt=$this->pdo->prepare('DELETE FROM products WHERE id = ?'); $stmt->execute([$id]); }
    public function reduceStock(int $productId, int $qty): void { $stmt=$this->pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?'); $stmt->execute([$qty, $productId]); }
}
