<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
if (is_admin()) {
    flash('warning', 'Administrador nao pode adicionar produtos ao carrinho.');
    redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['product_id'] ?? '')) {
    flash('danger', 'Operacao invalida.');
    redirect('products.php');
}

$productId = (int) $_POST['product_id'];
$stmt = db()->prepare('SELECT id, stock FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    flash('danger', 'Produto nao encontrado.');
    redirect('products.php');
}

$sessionCart = $_SESSION['cart'] ?? [];
$cartMap = [];
foreach ($sessionCart as $row) {
    $pid = (int) ($row['product_id'] ?? 0);
    $qty = (int) ($row['qty'] ?? 0);
    if ($pid <= 0 || $qty <= 0) {
        continue;
    }
    $cartMap[$pid] = ($cartMap[$pid] ?? 0) + $qty;
}

$newQty = ($cartMap[$productId] ?? 0) + 1;
if ($newQty > (int) $product['stock']) {
    flash('warning', 'Sem stock suficiente para aumentar quantidade.');
    redirect('products.php');
}
$cartMap[$productId] = $newQty;

$cart = [];
foreach ($cartMap as $pid => $qty) {
    $cart[] = ['product_id' => $pid, 'qty' => $qty];
}

$_SESSION['cart'] = $cart;
flash('success', 'Produto adicionado ao carrinho. Quantidade atual deste item: ' . $newQty . '.');
$backUrl = $_SERVER['HTTP_REFERER'] ?? url('products.php');
$base = base_url('');
if (!str_contains($backUrl, $base . '/')) {
    $backUrl = url('products.php');
}
header('Location: ' . $backUrl);
exit;
