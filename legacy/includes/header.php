<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
$title = $title ?? 'MiniCommerce';
$current = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/css/styles.css')) ?>" />
</head>
<body>
<nav class="navbar navbar-expand-lg bg-brand-dark navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= htmlspecialchars(url('index.php')) ?>">MiniCommerce</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('index.php')) ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $current === 'products.php' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('products.php')) ?>">Produtos</a></li>
        <li class="nav-item"><a class="nav-link <?= $current === 'cart.php' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('cart.php')) ?>">Carrinho (<?= cart_count() ?>)</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars(url('logout.php')) ?>">Sair</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= $current === 'login.php' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('login.php')) ?>">Entrar</a></li>
          <li class="nav-item"><a class="nav-link <?= $current === 'register.php' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('register.php')) ?>">Criar Conta</a></li>
        <?php endif; ?>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars(url('admin/dashboard.php')) ?>">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container my-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flash['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
