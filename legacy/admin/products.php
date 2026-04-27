<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$categories = get_categories();
$products = db()->query('SELECT p.id,p.name,p.price,p.stock,c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
$title = 'Admin | Produtos';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title mb-4">Gestao de Produtos</h1>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card p-3">
      <h5>Adicionar Produto</h5>
      <form method="post" action="<?= htmlspecialchars(url('actions/admin_add_product.php')) ?>">
        <div class="mb-2"><input name="name" class="form-control" placeholder="Nome" required /></div>
        <div class="mb-2"><select name="category_id" class="form-select" required><option value="">Categoria</option><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-2"><input name="price" type="number" step="0.01" min="0.01" class="form-control" placeholder="Preco" required /></div>
        <div class="mb-2"><input name="stock" type="number" min="0" class="form-control" placeholder="Stock" required /></div>
        <div class="mb-2"><input name="image_url" class="form-control" placeholder="URL da imagem" required /></div>
        <div class="mb-2"><textarea name="description" class="form-control" rows="3" placeholder="Descricao"></textarea></div>
        <button class="btn btn-brand w-100">Adicionar</button>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="table-responsive bg-white p-3 rounded shadow-sm">
      <table class="table table-hover">
        <thead><tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Preco</th><th>Stock</th></tr></thead>
        <tbody><?php foreach ($products as $p): ?><tr><td><?= (int) $p['id'] ?></td><td><?= htmlspecialchars($p['name']) ?></td><td><?= htmlspecialchars($p['category_name']) ?></td><td><?= money((float) $p['price']) ?></td><td><?= (int) $p['stock'] ?></td></tr><?php endforeach; ?></tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

