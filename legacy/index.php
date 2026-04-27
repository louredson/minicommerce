<?php
$title = 'MiniCommerce | Home';
require_once __DIR__ . '/includes/header.php';
$featured = array_slice(get_products(), 0, 3);
?>
<section class="hero p-5 mb-4">
  <h1 class="display-5 fw-bold">Mini E-Commerce Interativo</h1>
  <p class="lead">Navega livremente. Para comprar, cria conta e entra no sistema.</p>
  <a href="<?= htmlspecialchars(url('products.php')) ?>" class="btn btn-brand btn-lg">Ver Produtos</a>
</section>
<section>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="section-title">Destaques</h2>
    <a href="<?= htmlspecialchars(url('products.php')) ?>" class="btn btn-outline-brand">Ver Todos</a>
  </div>
  <div class="row g-4">
    <?php foreach ($featured as $p): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" class="product-image card-img-top" alt="<?= htmlspecialchars($p['name']) ?>" />
          <div class="card-body d-flex flex-column">
            <h5><?= htmlspecialchars($p['name']) ?></h5>
            <p class="text-muted"><?= htmlspecialchars($p['description']) ?></p>
            <p class="fw-bold fs-5 mt-auto"><?= money((float) $p['price']) ?></p>
            <form method="post" action="<?= htmlspecialchars(url('actions/add_to_cart.php')) ?>">
              <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>" />
              <button class="btn btn-brand w-100">Adicionar</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

