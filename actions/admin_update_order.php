<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['order_id'] ?? '')) {
    flash('danger', 'Operacao invalida.');
    redirect('/admin/orders.php');
}

$orderId = (int) $_POST['order_id'];
$status = $_POST['status'] ?? '';
$allowed = ['Pendente', 'Pago', 'Enviado', 'Cancelado'];
if (!in_array($status, $allowed, true)) {
    flash('warning', 'Status invalido.');
    redirect('/admin/orders.php');
}

$stmt = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
$stmt->execute([$status, $orderId]);
flash('info', 'Status do pedido atualizado.');
redirect('/admin/orders.php');
