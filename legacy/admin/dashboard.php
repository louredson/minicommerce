<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$productsCount = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$ordersCount = (int) db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$usersCount = (int) db()->query('SELECT COUNT(*) FROM users WHERE is_admin = 0')->fetchColumn();
$revenue = (float) db()->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status IN ('Pago','Enviado')")->fetchColumn();

$title = 'Admin | Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
  <div class="col-md-3"><div class="card stat-card p-3"><h6>Produtos</h6><h2><?= $productsCount ?></h2></div></div>
  <div class="col-md-3"><div class="card stat-card p-3"><h6>Pedidos</h6><h2><?= $ordersCount ?></h2></div></div>
  <div class="col-md-3"><div class="card stat-card p-3"><h6>Clientes</h6><h2><?= $usersCount ?></h2></div></div>
  <div class="col-md-3"><div class="card stat-card p-3"><h6>Receita</h6><h2><?= money($revenue) ?></h2></div></div>
</div>
<div class="mt-4 d-flex gap-2">
  <a href="<?= htmlspecialchars(url('admin/products.php')) ?>" class="btn btn-brand">Gerir Produtos</a>
  <a href="<?= htmlspecialchars(url('admin/orders.php')) ?>" class="btn btn-outline-brand">Gerir Pedidos</a>
  <a href="<?= htmlspecialchars(url('admin/users.php')) ?>" class="btn btn-outline-brand">Gerir Usuarios</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

