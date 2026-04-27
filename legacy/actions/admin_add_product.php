<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $image = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $category <= 0 || $price <= 0 || $stock < 0) {
        flash('warning', 'Preenche todos os campos do produto corretamente.');
        redirect('/admin/products.php');
    }

    $stmt = db()->prepare('INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$category, $name, $description, $price, $stock, $image]);
    flash('success', 'Produto adicionado com sucesso.');
    redirect('/admin/products.php');
}
