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

    public function request(string $email): array
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido.');
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new InvalidArgumentException('Email nao encontrado.');
        }

        $code = (string) random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + 900);
        $this->resets->create($email, $code, $expiresAt);

        $subject = 'Codigo de recuperacao de senha';
        $message = "Seu codigo de recuperacao: {$code}. Expira em 15 minutos.";
        @mail($email, $subject, $message);

        return ['code' => $code, 'expires_at' => $expiresAt];
    }

    public function reset(string $code, string $password, string $confirm): void
    {
        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Senha deve ter no minimo 6 caracteres.');
        }
        if ($password !== $confirm) {
            throw new InvalidArgumentException('Senha e confirmacao nao coincidem.');
        }

        $row = $this->resets->findValid($code);
        if (!$row) {
            throw new InvalidArgumentException('Codigo invalido ou expirado.');
        }

        $this->users->updatePasswordByEmail($row['email'], password_hash($password, PASSWORD_DEFAULT));
        $this->resets->markUsed((int) $row['id']);
    }
}
