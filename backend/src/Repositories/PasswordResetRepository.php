<?php

namespace App\Repositories;

use PDO;

class PasswordResetRepository
{
    public function __construct(private PDO $pdo)
    {
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  token VARCHAR(255) NOT NULL,
  attempts INT UNSIGNED NOT NULL DEFAULT 0,
  request_ip VARCHAR(45) NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;
        $this->pdo->exec($sql);

        $this->ensureColumn('attempts', 'ALTER TABLE password_resets ADD COLUMN attempts INT UNSIGNED NOT NULL DEFAULT 0');
        $this->ensureColumn('request_ip', 'ALTER TABLE password_resets ADD COLUMN request_ip VARCHAR(45) NULL');
    }

    private function ensureColumn(string $name, string $alterSql): void
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'password_resets' AND COLUMN_NAME = ?");
        $stmt->execute([$name]);
        if ((int) $stmt->fetchColumn() === 0) {
            $this->pdo->exec($alterSql);
        }
    }

    public function create(string $email, string $tokenHash, string $expiresAt, ?string $ip = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO password_resets (email, token, expires_at, request_ip, attempts) VALUES (?, ?, ?, ?, 0)');
        $stmt->execute([$email, $tokenHash, $expiresAt, $ip]);
    }

    public function invalidateByEmail(string $email): void
    {
        $stmt = $this->pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE email = ? AND used_at IS NULL');
        $stmt->execute([$email]);
    }

    public function countRecentRequests(string $email, int $seconds): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)');
        $stmt->execute([$email, $seconds]);
        return (int) $stmt->fetchColumn();
    }

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, token, attempts, expires_at, used_at FROM password_resets WHERE email = ? AND used_at IS NULL ORDER BY id DESC LIMIT 1');
        $stmt->execute([$email]);
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

    public function increaseAttempts(int $id): int
    {
        $stmt = $this->pdo->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?');
        $stmt->execute([$id]);
        $stmt = $this->pdo->prepare('SELECT attempts FROM password_resets WHERE id = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }
}
