<?php

namespace App\Repositories;

use PDO;

class PasswordResetRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $email, string $code, string $expiresAt): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$email, $code, $expiresAt]);
    }

    public function findValid(string $code): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, expires_at, used_at FROM password_resets WHERE token = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        if (!$row) return null;
        if (!empty($row['used_at'])) return null;
        if (strtotime($row['expires_at']) < time()) return null;
        return $row;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
