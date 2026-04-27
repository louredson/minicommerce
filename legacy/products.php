<?php
require_once __DIR__ . '/includes/functions.php';
$categoryId = isset($_GET['category']) && ctype_digit($_GET['category']) ? (int) $_GET['category'] : null;
$categories = get_categories();
$products = get_products($categoryId);
$title = 'MiniCommerce | Produtos';
require_once __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="section-title">Produtos</h1>
  <form method="get" class="d-flex gap-2">
    <select name="category" class="form-select">
      <option value="">Todas categorias</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($category['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-brand">Filtrar</button>
  </form>
</div>
<div class="row g-4">
  <?php foreach ($products as $p): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100">
        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="product-image card-img-top" alt="<?= htmlspecialchars($p['name']) ?>" />
        <div class="card-body d-flex flex-column">
          <div class="d-flex justify-content-between mb-2">
            <h5 class="mb-0"><?= htmlspecialchars($p['name']) ?></h5>
            <span class="badge badge-stock"><?= htmlspecialchars($p['category_name']) ?></span>
          </div>
          <p class="text-muted"><?= htmlspecialchars($p['description']) ?></p>
          <p class="fw-bold fs-5 mt-auto"><?= money((float) $p['price']) ?></p>
          <form method="post" action="<?= htmlspecialchars(url('actions/add_to_cart.php')) ?>">
            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>" />
            <button class="btn btn-brand w-100">Adicionar ao Carrinho</button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

