<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['product_id'] ?? '')) {
    flash('danger', 'Operacao invalida.');
    redirect('/products.php');
}

$productId = (int) $_POST['product_id'];
$stmt = db()->prepare('SELECT id, stock FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    flash('danger', 'Produto nao encontrado.');
    redirect('/products.php');
}

$cart = $_SESSION['cart'] ?? [];
$found = false;
foreach ($cart as &$item) {
    if ((int) $item['product_id'] === $productId) {
        if ((int) $item['qty'] + 1 > (int) $product['stock']) {
            flash('warning', 'Sem stock suficiente para aumentar quantidade.');
            redirect('/products.php');
        }
        $item['qty']++;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $cart[] = ['product_id' => $productId, 'qty' => 1];
}

$_SESSION['cart'] = $cart;
flash('success', 'Produto adicionado ao carrinho.');
redirect('/cart.php');
