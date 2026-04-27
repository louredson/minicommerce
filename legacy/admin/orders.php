<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$orders = db()->query('SELECT o.id,o.total_amount,o.status,o.created_at,u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();
$title = 'Admin | Pedidos';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title mb-4">Gestao de Pedidos</h1>
<div class="table-responsive bg-white p-3 rounded shadow-sm">
  <table class="table table-hover align-middle">
    <thead><tr><th>Pedido</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th><th>Acao</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int) $o['id'] ?></td>
          <td><?= htmlspecialchars($o['name']) ?></td>
          <td><?= htmlspecialchars($o['created_at']) ?></td>
          <td><?= money((float) $o['total_amount']) ?></td>
          <td><?= htmlspecialchars($o['status']) ?></td>
          <td>
            <form method="post" action="<?= htmlspecialchars(url('actions/admin_update_order.php')) ?>" class="d-flex gap-2">
              <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>" />
              <select name="status" class="form-select form-select-sm">
                <?php foreach (['Pendente','Pago','Enviado','Cancelado'] as $st): ?>
                  <option value="<?= $st ?>" <?= $o['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-sm btn-brand">Salvar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

