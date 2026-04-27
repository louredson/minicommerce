<?php
require_once __DIR__ . '/../config/database.php';

function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $appRoot = realpath(__DIR__ . '/..');
        $base = '';

        if ($docRoot && $appRoot) {
            $docRootNorm = str_replace('\\', '/', $docRoot);
            $appRootNorm = str_replace('\\', '/', $appRoot);
            if (stripos($appRootNorm, $docRootNorm) === 0) {
                $relative = trim(substr($appRootNorm, strlen($docRootNorm)), '/');
                $base = $relative === '' ? '' : '/' . $relative;
            }
        }
    }

    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return base_url($path);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function is_admin(): bool
{
    return is_logged_in() && (int) $_SESSION['user']['is_admin'] === 1;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Precisas entrar na conta para comprar.');
        redirect('/login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        flash('danger', 'Acesso restrito a administradores.');
        redirect('/index.php');
    }
}

function require_customer(): void
{
    require_login();
    if (is_admin()) {
        flash('warning', 'Administrador nao pode realizar compras.');
        redirect('products.php');
    }
}

function money(float $value): string
{
    return 'Kz ' . number_format($value, 2, ',', '.');
}

function cart_count(): int
{
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $count += (int) $item['qty'];
    }
    return $count;
}

function cart_total(): float
{
    $total = 0;
    foreach (get_cart_items() as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function get_categories(): array
{
    return db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
}

function get_products(?int $categoryId = null): array
{
    if ($categoryId) {
        $stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? ORDER BY p.id DESC');
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    return db()->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
}

function get_cart_items(): array
{
    $sessionCart = $_SESSION['cart'] ?? [];
    if (!$sessionCart) {
        return [];
    }

    $ids = array_map(fn($i) => (int) $i['product_id'], $sessionCart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT id, name, price, stock, image_url FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    $products = [];
    foreach ($stmt->fetchAll() as $product) {
        $products[(int) $product['id']] = $product;
    }

    $items = [];
    foreach ($sessionCart as $item) {
        $pid = (int) $item['product_id'];
        if (!isset($products[$pid])) {
            continue;
        }
        $items[] = [
            'id' => $pid,
            'name' => $products[$pid]['name'],
            'price' => (float) $products[$pid]['price'],
            'stock' => (int) $products[$pid]['stock'],
            'image_url' => $products[$pid]['image_url'],
            'qty' => (int) $item['qty'],
        ];
    }

    return $items;
}
