<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$users = db()->query('SELECT id,name,email,is_admin,is_active,created_at FROM users ORDER BY id DESC')->fetchAll();
$title = 'Admin | Usuarios';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title mb-4">Gestao de Usuarios</h1>
<div class="table-responsive bg-white p-3 rounded shadow-sm">
  <table class="table table-hover align-middle">
    <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Perfil</th><th>Estado</th><th>Acao</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int) $u['id'] ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= (int) $u['is_admin'] === 1 ? 'Admin' : 'Cliente' ?></td>
          <td><?= (int) $u['is_active'] === 1 ? 'Ativo' : 'Bloqueado' ?></td>
          <td>
            <?php if ((int) $u['is_admin'] === 0): ?>
              <form method="post" action="<?= htmlspecialchars(url('actions/admin_toggle_user.php')) ?>">
                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>" />
                <button class="btn btn-sm <?= (int) $u['is_active'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                  <?= (int) $u['is_active'] === 1 ? 'Bloquear' : 'Ativar' ?>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

