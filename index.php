<?php
// public/index.php

session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Si le formulaire est soumis, tenter l’authentification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['pass']  ?? '';

    $user = login($email, $pass);
    if ($user) {
        // Enregistrer l’utilisateur en session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        // Rediriger selon le rôle
        if ($user['role'] === 'buyer') {
            header('Location: buyer_interface.php');
        } else { // seller ou autre
            header('Location: seller_interface.php');
        }
        exit;
    } else {
        $error = 'Email ou mot de passe invalide.';
    }
}

// Titre pour l’en-tête
$page_title = 'Connexion';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-4">
    <h2 class="text-center mb-4">Se connecter</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          required
          autofocus
        >
      </div>

      <div class="mb-3">
        <label for="pass" class="form-label">Mot de passe</label>
        <input
          type="password"
          id="pass"
          name="pass"
          class="form-control"
          required
        >
      </div>

      <button type="submit" class="btn btn-primary w-100">Connexion</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
