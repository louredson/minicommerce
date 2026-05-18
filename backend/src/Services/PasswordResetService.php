<?php

namespace App\Services;

use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;

class PasswordResetService
{
    public function __construct(private UserRepository $users, private PasswordResetRepository $resets)
    {
    }

    public function request(string $email, ?string $ip = null): array
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido.');
        }

        if ($this->resets->countRecentRequests($email, 60) >= 2) {
            return ['delivered' => true];
        }

        $user = $this->users->findByEmail($email);
        if (!$user || (int)($user['is_active'] ?? 0) !== 1) {
            return ['delivered' => true];
        }

        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $codeHash = password_hash($code, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', time() + 900);
        $this->resets->invalidateByEmail($email);
        $this->resets->create($email, $codeHash, $expiresAt, $ip);

        $subject = 'Codigo de recuperacao - Buyprime';
        $message = "Seu codigo de recuperacao da Buyprime: {$code}\nValidade: 15 minutos.\nSe nao foi voce, ignore este email.";
        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $sent = @mail($email, $subject, $message, $headers);
        if (!$sent) {
            throw new InvalidArgumentException('Falha ao enviar email. Verifique SMTP do XAMPP.');
        }

        return ['delivered' => true];
    }

    public function reset(string $email, string $code, string $password, string $confirm): void
    {
        $email = trim($email);
        $code = trim($code);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido.');
        }
        if (!preg_match('/^\d{4}$/', $code)) {
            throw new InvalidArgumentException('Codigo invalido. Use 4 digitos.');
        }
        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Senha deve ter no minimo 6 caracteres.');
        }
        if ($password !== $confirm) {
            throw new InvalidArgumentException('Senha e confirmacao nao coincidem.');
        }

        $row = $this->resets->findActiveByEmail($email);
        if (!$row) {
            throw new InvalidArgumentException('Codigo invalido ou expirado.');
        }
        if ((int)($row['attempts'] ?? 0) >= 5) {
            $this->resets->markUsed((int)$row['id']);
            throw new InvalidArgumentException('Codigo bloqueado por tentativas em excesso. Gere um novo codigo.');
        }
        if (!password_verify($code, (string)$row['token'])) {
            $attempts = $this->resets->increaseAttempts((int)$row['id']);
            if ($attempts >= 5) {
                $this->resets->markUsed((int)$row['id']);
                throw new InvalidArgumentException('Codigo bloqueado por tentativas em excesso. Gere um novo codigo.');
            }
            throw new InvalidArgumentException('Codigo invalido.');
        }

        $this->users->updatePasswordByEmail($email, password_hash($password, PASSWORD_DEFAULT));
        $this->resets->markUsed((int) $row['id']);
    }
}
