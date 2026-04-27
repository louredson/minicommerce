<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['product_id'] ?? '')) {
    flash('danger', 'Operacao invalida.');
    redirect('/cart.php');
}

$productId = (int) $_POST['product_id'];
$_SESSION['cart'] = array_values(array_filter($_SESSION['cart'] ?? [], fn($i) => (int) $i['product_id'] !== $productId));
flash('info', 'Produto removido do carrinho.');
redirect('/cart.php');

