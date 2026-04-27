<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$userId = (int) $_SESSION['user']['id'];

$ordersStmt = db()->prepare('SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC');
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll();

$orderItemsStmt = db()->prepare('SELECT oi.order_id, p.name, oi.quantity, oi.unit_price FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');

$title = 'Perfil do Usuario';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card p-4">
      <h4 class="mb-3">Meu Perfil</h4>
      <p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
      <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
      <p class="mb-0"><strong>Tipo:</strong> <?= is_admin() ? 'Administrador' : 'Cliente' ?></p>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card p-4">
      <h4 class="mb-3">Historico de Compras</h4>
      <?php if (is_admin()): ?>
        <div class="alert alert-info mb-0">Administrador nao realiza compras. Usa o painel Admin para gestao.</div>
      <?php elseif (!$orders): ?>
        <div class="alert alert-warning mb-0">Ainda nao existem compras realizadas.</div>
      <?php else: ?>
        <?php foreach ($orders as $order): ?>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
              <strong>Pedido #<?= (int) $order['id'] ?></strong>
              <span class="badge text-bg-secondary"><?= htmlspecialchars($order['status']) ?></span>
            </div>
            <p class="mb-2 text-muted">Data: <?= htmlspecialchars($order['created_at']) ?> | Total: <?= money((float) $order['total_amount']) ?></p>
            <ul class="mb-0">
              <?php
                $orderItemsStmt->execute([(int) $order['id']]);
                foreach ($orderItemsStmt->fetchAll() as $item):
              ?>
                <li><?= htmlspecialchars($item['name']) ?> x <?= (int) $item['quantity'] ?> (<?= money((float) $item['unit_price']) ?>)</li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
