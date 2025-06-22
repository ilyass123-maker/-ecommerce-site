<?php
// includes/header.php

// Démarre la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLogged   = !empty($_SESSION['user_id']);
$current    = basename($_SERVER['PHP_SELF']);
$cartCount  = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Ma Boutique') ?></title>

    <!-- 1) Bootstrap (load first) -->
    <link href="https://bootswatch.com/5/lux/bootstrap.min.css" rel="stylesheet">

    <!-- 2) Vos styles personnalisés (absolute path ensures correct loading) -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">

    <!-- Logo centré -->
    <a class="navbar-brand mx-auto" href="index.php">
      <img src="/assets/img/logo.png" alt="Logo" height="50">
    </a>

    <!-- Liens de droite -->
    <ul class="navbar-nav ms-auto">
      <?php if (!$isLogged): ?>
        <?php if ($current === 'index.php'): ?>
          <li class="nav-item">
            <a class="nav-link" href="signup.php">Sign Up</a>
          </li>
        <?php elseif ($current === 'signup.php'): ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php">Login</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="signup.php">Sign Up</a>
          </li>
        <?php endif; ?>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart.php">
            Panier
            <?php if ($cartCount > 0): ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      <?php endif; ?>
    </ul>

  </div>
</nav>

<div class="container mt-4">
