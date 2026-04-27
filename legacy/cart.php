<?php
require_once __DIR__ . '/includes/functions.php';
$title = 'MiniCommerce | Carrinho';
$items = get_cart_items();
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title mb-4">Carrinho</h1>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table align-middle">
        <thead><tr><th>Produto</th><th>Preco</th><th>Qtd</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
          <?php if (!$items): ?>
            <tr><td colspan="5" class="text-center py-4">Carrinho vazio.</td></tr>
          <?php endif; ?>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= money($item['price']) ?></td>
              <td>
                <form method="post" action="<?= htmlspecialchars(url('actions/update_cart.php')) ?>" class="d-flex" style="max-width: 130px;">
                  <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>" />
                  <input type="number" min="1" max="<?= (int) $item['stock'] ?>" name="qty" value="<?= (int) $item['qty'] ?>" class="form-control" />
                  <button class="btn btn-sm btn-outline-brand ms-2">Ok</button>
                </form>
              </td>
              <td><?= money($item['price'] * $item['qty']) ?></td>
              <td>
                <form method="post" action="<?= htmlspecialchars(url('actions/remove_from_cart.php')) ?>">
                  <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>" />
                  <button class="btn btn-sm btn-outline-danger">Remover</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card p-3">
      <h4>Resumo</h4>
      <h5>Total: <?= money(cart_total()) ?></h5>
      <a href="<?= htmlspecialchars(url('checkout.php')) ?>" class="btn btn-brand">Finalizar Compra</a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

