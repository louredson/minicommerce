<?php

namespace App\Middleware;

use App\Core\JsonResponse;

class AuthMiddleware
{
    public static function requireLogin(): array
    {
        if (empty($_SESSION['user'])) {
            JsonResponse::send(['success' => false, 'message' => 'Nao autenticado.'], 401);
        }

        return $_SESSION['user'];
    }

    public static function requireAdmin(): array
    {
        $user = self::requireLogin();
        if ((int) $user['is_admin'] !== 1) {
            JsonResponse::send(['success' => false, 'message' => 'Acesso restrito ao admin.'], 403);
        }

        return $user;
    }

    public static function requireCustomer(): array
    {
        $user = self::requireLogin();
        if ((int) $user['is_admin'] === 1) {
            JsonResponse::send(['success' => false, 'message' => 'Administrador nao pode comprar.'], 403);
        }

        return $user;
    }
}
