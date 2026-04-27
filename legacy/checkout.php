<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$items = get_cart_items();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$items) {
        flash('warning', 'Carrinho vazio.');
        redirect('/cart.php');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $total = 0;
        foreach ($items as $item) {
            $q = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
            $q->execute([$item['id']]);
            $stock = (int) ($q->fetch()['stock'] ?? 0);
            if ($item['qty'] > $stock) {
                throw new Exception('Stock insuficiente para o produto: ' . $item['name']);
            }
            $total += $item['price'] * $item['qty'];
        }

        $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
        $orderStmt->execute([$_SESSION['user']['id'], $total, 'Pendente']);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
        $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');

        foreach ($items as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
            $stockStmt->execute([$item['qty'], $item['id']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];
        flash('success', 'Compra concluida com sucesso. Pedido #' . $orderId . ' criado.');
        redirect('/products.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('danger', 'Falha na compra: ' . $e->getMessage());
        redirect('/checkout.php');
    }
}

$title = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title mb-4">Checkout</h1>
<?php if (!$items): ?>
  <div class="alert alert-warning">Carrinho vazio.</div>
<?php else: ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h4 class="mb-3">Confirmacao da Compra</h4>
        <ul class="list-group mb-3">
          <?php foreach ($items as $item): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= htmlspecialchars($item['name']) ?> x <?= (int) $item['qty'] ?></span>
              <strong><?= money($item['price'] * $item['qty']) ?></strong>
            </li>
          <?php endforeach; ?>
        </ul>
        <h5>Total: <?= money(cart_total()) ?></h5>
        <form method="post"><button class="btn btn-brand mt-3">Confirmar Pedido</button></form>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
