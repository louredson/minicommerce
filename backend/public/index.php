<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\OrderController;
use App\Core\Database;
use App\Core\JsonResponse;
use App\Middleware\AuthMiddleware;
use App\Repositories\OrderRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\OrderService;
use App\Services\PasswordResetService;

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) require_once $path;
});

session_start();
$config = require __DIR__ . '/../src/Config/config.php';
$pdo = Database::connection($config);

header('Access-Control-Allow-Origin: ' . $config['cors']['origin']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$body = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$route = '/' . ltrim(str_replace($base, '', $path), '/');

$userRepo = new UserRepository($pdo);
$productRepo = new ProductRepository($pdo);
$orderRepo = new OrderRepository($pdo);
$resetRepo = new PasswordResetRepository($pdo);
$authController = new AuthController(new AuthService($userRepo), new PasswordResetService($userRepo, $resetRepo));
$catalogController = new CatalogController($productRepo);
$cartController = new CartController($productRepo);
$orderController = new OrderController(new OrderService($pdo, $productRepo, $orderRepo), $orderRepo, $productRepo);
$adminController = new AdminController($productRepo, $orderRepo, $userRepo);

try {
    if ($method === 'POST' && $route === '/auth/register') $authController->register($body);
    if ($method === 'POST' && $route === '/auth/login') $authController->login($body);
    if ($method === 'POST' && $route === '/auth/logout') $authController->logout();
    if ($method === 'POST' && $route === '/auth/forgot-password') $authController->requestPasswordReset($body);
    if ($method === 'POST' && $route === '/auth/reset-password') $authController->resetPassword($body);
    if ($method === 'GET' && $route === '/auth/me') $authController->me();

    if ($method === 'GET' && $route === '/categories') $catalogController->categories();
    if ($method === 'GET' && $route === '/products') $catalogController->products(isset($_GET['category_id']) ? (int) $_GET['category_id'] : null);
    if ($method === 'GET' && $route === '/products/item') $catalogController->productById((int) ($_GET['id'] ?? 0));

    if ($method === 'POST' && $route === '/cart/add') { AuthMiddleware::requireCustomer(); $cartController->add($body); }
    if ($method === 'PUT' && $route === '/cart/update') { AuthMiddleware::requireCustomer(); $cartController->update($body); }
    if ($method === 'DELETE' && $route === '/cart/remove') { AuthMiddleware::requireCustomer(); $cartController->remove($body); }
    if ($method === 'DELETE' && $route === '/cart/clear') { AuthMiddleware::requireCustomer(); $cartController->clear(); }
    if ($method === 'GET' && $route === '/cart') { AuthMiddleware::requireCustomer(); $orderController->cartSummary(); }

    if ($method === 'POST' && $route === '/checkout') { $u = AuthMiddleware::requireCustomer(); $orderController->checkout((int)$u['id']); }
    if ($method === 'GET' && $route === '/orders/my') { $u = AuthMiddleware::requireLogin(); $orderController->myOrders((int)$u['id']); }

    if ($method === 'GET' && $route === '/admin/dashboard') { AuthMiddleware::requireAdmin(); $adminController->dashboard(); }
    if ($method === 'GET' && $route === '/admin/products') { AuthMiddleware::requireAdmin(); $adminController->listProducts(); }
    if ($method === 'POST' && $route === '/admin/products') { AuthMiddleware::requireAdmin(); $adminController->addProduct($body); }
    if ($method === 'PUT' && $route === '/admin/products') { AuthMiddleware::requireAdmin(); $adminController->updateProduct($body); }
    if ($method === 'DELETE' && $route === '/admin/products') { AuthMiddleware::requireAdmin(); $adminController->deleteProduct($body); }
    if ($method === 'POST' && $route === '/admin/products/upload') { AuthMiddleware::requireAdmin(); $adminController->uploadImage(); }
    if ($method === 'GET' && $route === '/admin/orders') { AuthMiddleware::requireAdmin(); $adminController->listOrders(); }
    if ($method === 'PUT' && $route === '/admin/orders/status') { AuthMiddleware::requireAdmin(); $adminController->updateOrderStatus($body); }
    if ($method === 'GET' && $route === '/admin/orders/report.pdf') { AuthMiddleware::requireAdmin(); $adminController->ordersPdf(); }
    if ($method === 'GET' && $route === '/admin/users') { AuthMiddleware::requireAdmin(); $adminController->listUsers(); }
    if ($method === 'PUT' && $route === '/admin/users/toggle') { AuthMiddleware::requireAdmin(); $adminController->toggleUser($body); }

    JsonResponse::send(['success' => false, 'message' => 'Endpoint nao encontrado.'], 404);
} catch (Throwable $e) {
    JsonResponse::send(['success' => false, 'message' => 'Erro interno.', 'error' => $e->getMessage()], 500);
}
