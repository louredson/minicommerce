<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ctype_digit($_POST['user_id'] ?? '')) {
    flash('danger', 'Operacao invalida.');
    redirect('/admin/users.php');
}

$userId = (int) $_POST['user_id'];
if ($userId === (int) $_SESSION['user']['id']) {
    flash('warning', 'Nao podes bloquear a tua propria conta admin.');
    redirect('/admin/users.php');
}

$stmt = db()->prepare('UPDATE users SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?');
$stmt->execute([$userId]);
flash('info', 'Estado do usuario atualizado.');
redirect('/admin/users.php');
