<?php
// includes/header.php

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculer le nombre d’articles dans le panier (pour l’acheteur)
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Ma Boutique') ?></title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="https://bootswatch.com/5/lux/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="buyer_interface.php">Ma Boutique</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <!-- Ici vous pourriez boucler sur les catégories si injectées avant l'inclusion -->
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart.php">
            <i class="bi bi-cart"></i> Panier
            <?php if ($cartCount > 0): ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <?php if (!empty($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Déconnexion</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
