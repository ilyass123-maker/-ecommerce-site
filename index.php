<?php


session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['pass']  ?? '';
    $user  = login($email, $pass);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: buyer_interface.php');
        exit;
    } else {
        $error = 'Email ou mot de passe invalide.';
    }
}

$page_title = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-4">
    <h2 class="text-center mb-4">Se connecter</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="pass" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <p class="text-center mt-3">
      Pas encore de compte ? <a href="signup.php">Sign Up</a>
    </p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
