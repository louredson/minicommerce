<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['product_id'] ?? '') || !ctype_digit($_POST['qty'] ?? '')) {
    flash('danger', 'Atualizacao invalida.');
    redirect('/cart.php');
}

$productId = (int) $_POST['product_id'];
$qty = max(1, (int) $_POST['qty']);

$stmt = db()->prepare('SELECT stock FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product || $qty > (int) $product['stock']) {
    flash('warning', 'Quantidade acima do stock disponivel.');
    redirect('/cart.php');
}

foreach ($_SESSION['cart'] ?? [] as &$item) {
    if ((int) $item['product_id'] === $productId) {
        $item['qty'] = $qty;
    }
}
unset($item);

flash('info', 'Quantidade atualizada.');
redirect('/cart.php');

