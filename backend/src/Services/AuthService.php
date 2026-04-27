<?php

namespace App\Services;

use App\Repositories\UserRepository;
use InvalidArgumentException;

class AuthService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function register(array $input): array
    {
        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        if (strlen($firstName) < 2 || strlen($lastName) < 2) {
            throw new InvalidArgumentException('Nome e sobrenome invalidos.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalido.');
        }
        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Senha deve ter no minimo 6 caracteres.');
        }
        if ($password !== $confirmPassword) {
            throw new InvalidArgumentException('Senha e confirmar senha nao coincidem.');
        }

        if ($this->users->findByEmail($email)) {
            throw new InvalidArgumentException('Email ja registado.');
        }

        $name = $firstName . ' ' . $lastName;
        $id = $this->users->create($name, $email, password_hash($password, PASSWORD_DEFAULT));

        return ['id' => $id, 'name' => $name, 'email' => $email];
    }

    public function login(array $input): array
    {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new InvalidArgumentException('Credenciais invalidas.');
        }

        if ((int) $user['is_active'] === 0) {
            throw new InvalidArgumentException('Conta bloqueada pelo administrador.');
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'is_admin' => (int) $user['is_admin'],
        ];
    }
}
