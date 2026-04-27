<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect('/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $name = trim($firstName . ' ' . $lastName);

    if (strlen($firstName) < 2 || strlen($lastName) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        flash('warning', 'Preenche dados validos. Nome e sobrenome com minimo de 2 letras e senha minima de 6 caracteres.');
        redirect('/register.php');
    }

    if ($password !== $confirmPassword) {
        flash('danger', 'Senha e Confirmar Senha nao coincidem.');
        redirect('/register.php');
    }

    $check = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        flash('danger', 'Email ja registado.');
        redirect('/register.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, is_admin) VALUES (?, ?, ?, 0)');
    $stmt->execute([$name, $email, $hash]);

    flash('success', 'Conta criada com sucesso. Agora podes entrar.');
    redirect('/login.php');
}

$title = 'Criar Conta';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card p-4">
      <h2 class="mb-3">Criar Conta</h2>
      <form method="post">
        <div class="mb-3"><label class="form-label">Nome</label><input type="text" name="first_name" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Sobrenome</label><input type="text" name="last_name" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Senha</label><input type="password" name="password" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Confirmar Senha</label><input type="password" name="confirm_password" class="form-control" required /></div>
        <button class="btn btn-brand w-100">Criar Conta</button>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
