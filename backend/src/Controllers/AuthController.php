<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Services\AuthService;
use App\Services\PasswordResetService;
use InvalidArgumentException;

class AuthController
{
    public function __construct(private AuthService $auth, private PasswordResetService $passwordReset)
    {
    }

    public function register(array $body): void
    {
        try {
            $user = $this->auth->register($body);
            JsonResponse::send(['success' => true, 'message' => 'Conta criada com sucesso.', 'data' => $user], 201);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function login(array $body): void
    {
        try {
            $user = $this->auth->login($body);
            $_SESSION['user'] = $user;
            JsonResponse::send(['success' => true, 'message' => 'Login efetuado com sucesso.', 'data' => $user]);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 401);
        }
    }

    public function logout(): void
    {
        session_destroy();
        session_start();
        JsonResponse::send(['success' => true, 'message' => 'Sessao encerrada.']);
    }

    public function requestPasswordReset(array $body): void
    {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $result = $this->passwordReset->request((string) ($body['email'] ?? ''), is_string($ip) ? $ip : null);
            JsonResponse::send([
                'success' => true,
                'message' => 'Se o email existir e estiver ativo, enviamos um codigo de 4 digitos.',
                'data' => $result
            ]);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function resetPassword(array $body): void
    {
        try {
            $this->passwordReset->reset(
                (string) ($body['email'] ?? ''),
                (string) ($body['token'] ?? ''),
                (string) ($body['password'] ?? ''),
                (string) ($body['confirm_password'] ?? '')
            );
            JsonResponse::send(['success' => true, 'message' => 'Senha atualizada com sucesso.']);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function me(): void
    {
        if (empty($_SESSION['user'])) {
            JsonResponse::send(['success' => false, 'message' => 'Nao autenticado.'], 401);
        }

        JsonResponse::send(['success' => true, 'data' => $_SESSION['user']]);
    }
}
