<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Core\PdfSimple;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;

class AdminController
{
    public function __construct(
        private ProductRepository $products,
        private OrderRepository $orders,
        private UserRepository $users
    ) {}

    public function dashboard(): void
    {
        JsonResponse::send(['success' => true, 'data' => [
            'products' => count($this->products->products(null)),
            'orders' => count($this->orders->listAll()),
            'users' => count($this->users->listAll()),
        ]]);
    }

    public function addProduct(array $body): void
    {
        try {
            $payload = [
                'category_id' => (int) ($body['category_id'] ?? 0),
                'name' => trim($body['name'] ?? ''),
                'description' => trim($body['description'] ?? ''),
                'price' => (float) ($body['price'] ?? 0),
                'stock' => (int) ($body['stock'] ?? 0),
                'image_url' => trim($body['image_url'] ?? ''),
            ];
            if ($payload['category_id'] <= 0 || $payload['name'] === '' || $payload['price'] <= 0 || $payload['stock'] < 0 || $payload['image_url'] === '') {
                throw new InvalidArgumentException('Dados do produto invalidos.');
            }
            if (!filter_var($payload['image_url'], FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $payload['image_url'])) {
                throw new InvalidArgumentException('A imagem deve ser um link valido (http/https).');
            }
            $id = $this->products->create($payload);
            JsonResponse::send(['success' => true, 'message' => 'Produto criado.', 'data' => ['id' => $id]], 201);
        } catch (InvalidArgumentException $e) {
            JsonResponse::send(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function listProducts(): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->products->products(null)]);
    }

    public function updateProduct(array $body): void
    {
        $id = (int) ($body['id'] ?? 0);
        if ($id <= 0) JsonResponse::send(['success' => false, 'message' => 'ID invalido.'], 422);

        $payload = [
            'category_id' => (int) ($body['category_id'] ?? 0),
            'name' => trim($body['name'] ?? ''),
            'description' => trim($body['description'] ?? ''),
            'price' => (float) ($body['price'] ?? 0),
            'stock' => (int) ($body['stock'] ?? 0),
            'image_url' => trim($body['image_url'] ?? ''),
        ];
        $this->products->update($id, $payload);
        JsonResponse::send(['success' => true, 'message' => 'Produto atualizado.']);
    }

    public function deleteProduct(array $body): void
    {
        $id = (int) ($body['id'] ?? 0);
        if ($id <= 0) JsonResponse::send(['success' => false, 'message' => 'ID invalido.'], 422);
        $this->products->delete($id);
        JsonResponse::send(['success' => true, 'message' => 'Produto removido.']);
    }

    public function uploadImage(): void
    {
        if (empty($_FILES['image'])) {
            JsonResponse::send(['success' => false, 'message' => 'Arquivo nao enviado.'], 422);
        }

        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            JsonResponse::send(['success' => false, 'message' => 'Formato invalido.'], 422);
        }

        $name = uniqid('prod_', true) . '.' . $ext;
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $target = $targetDir . $name;
        move_uploaded_file($file['tmp_name'], $target);

        $url = '/teste_front/backend/public/uploads/' . $name;
        JsonResponse::send(['success' => true, 'message' => 'Imagem enviada.', 'data' => ['url' => $url]]);
    }

    public function listOrders(): void
    {
        $from = isset($_GET['from']) ? trim((string) $_GET['from']) : null;
        $to = isset($_GET['to']) ? trim((string) $_GET['to']) : null;
        JsonResponse::send(['success' => true, 'data' => $this->orders->listAll($from ?: null, $to ?: null)]);
    }

    public function updateOrderStatus(array $body): void
    {
        $orderId = (int) ($body['order_id'] ?? 0);
        $status = trim($body['status'] ?? '');
        $allowed = ['Pendente', 'Pago', 'Enviado', 'Cancelado'];
        if ($orderId <= 0 || !in_array($status, $allowed, true)) {
            JsonResponse::send(['success' => false, 'message' => 'Dados invalidos.'], 422);
        }
        $this->orders->updateStatus($orderId, $status);
        JsonResponse::send(['success' => true, 'message' => 'Status atualizado.']);
    }

    public function listUsers(): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->users->listAll()]);
    }

    public function toggleUser(array $body): void
    {
        $userId = (int) ($body['user_id'] ?? 0);
        if ($userId <= 0) JsonResponse::send(['success' => false, 'message' => 'Usuario invalido.'], 422);
        $this->users->toggleActive($userId);
        JsonResponse::send(['success' => true, 'message' => 'Usuario atualizado.']);
    }

    public function ordersPdf(): void
    {
        $from = isset($_GET['from']) ? trim((string) $_GET['from']) : null;
        $to = isset($_GET['to']) ? trim((string) $_GET['to']) : null;
        $orders = $this->orders->listAll($from ?: null, $to ?: null);
        foreach ($orders as &$o) {
            $o['items'] = $this->orders->listItemsByOrder((int) $o['id']);
        }
        unset($o);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $pdf = PdfSimple::ordersReport($orders);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=relatorio-pedidos.pdf');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }
}
