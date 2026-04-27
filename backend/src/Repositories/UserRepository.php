<?php

namespace App\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, password_hash, is_admin, is_active FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password_hash, is_admin, is_active) VALUES (?, ?, ?, 0, 1)');
        $stmt->execute([$name, $email, $passwordHash]);
        return (int) $this->pdo->lastInsertId();
    }

    public function listAll(): array
    {
        return $this->pdo->query('SELECT id, name, email, is_admin, is_active, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public function toggleActive(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ? AND is_admin = 0');
        $stmt->execute([$userId]);
    }

    public function updatePasswordByEmail(string $email, string $hash): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
        $stmt->execute([$hash, $email]);
    }
}
