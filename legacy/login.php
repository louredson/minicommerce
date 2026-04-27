<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect('/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, name, email, password_hash, is_admin, is_active FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && (int) $user['is_active'] === 0) {
        flash('warning', 'Conta bloqueada. Contacta o administrador.');
        redirect('/login.php');
    }

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'is_admin' => (int) $user['is_admin'],
        ];
        flash('success', 'Login efetuado com sucesso.');
        redirect('/index.php');
    }

    flash('danger', 'Credenciais invalidas.');
    redirect('/login.php');
}

$title = 'Entrar';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card p-4">
      <h2 class="mb-3">Entrar</h2>
      <form method="post">
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Senha</label><input type="password" name="password" class="form-control" required /></div>
        <button class="btn btn-brand w-100">Entrar</button>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
