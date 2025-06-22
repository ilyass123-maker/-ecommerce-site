<?php
// includes/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLogged  = !empty($_SESSION['user_id']);
$current   = basename($_SERVER['PHP_SELF']);
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Ma Boutique') ?></title>

  <link href="https://bootswatch.com/5/lux/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container d-flex align-items-center">

    <!-- Logo à gauche, un peu plus grand (60px) -->
    <a class="navbar-brand pe-3" href="index.php">
      <img src="assets/img/logo.png" alt="Logo" height="60">
    </a>

    <!-- Buy & Sell links -->
    <?php if ($isLogged): ?>
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= $current==='buyer_interface.php'?'active':'' ?>"
             href="buyer_interface.php">Buy</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='seller_interface.php'?'active':'' ?>"
             href="seller_interface.php">Sell</a>
        </li>
      </ul>
    <?php else: ?>
      <div class="me-auto"></div>
    <?php endif; ?>

    <!-- Auth/Cart links on the right -->
    <ul class="navbar-nav ms-auto">
      <?php if (!$isLogged): ?>
        <?php if ($current === 'index.php'): ?>
          <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
        <?php elseif ($current === 'signup.php'): ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Login</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
        <?php endif; ?>
      <?php else: ?>
        <li class="nav-item position-relative">
          <a class="nav-link" href="cart.php">
            Panier
            <?php if ($cartCount > 0): ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      <?php endif; ?>
    </ul>

  </div>
</nav>

<div class="container mt-4">
