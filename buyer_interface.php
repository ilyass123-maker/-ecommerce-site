<?php
// public/buyer_interface.php

session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

// Récupérer les types de produits
$types = $pdo->query("SELECT DISTINCT type FROM products")->fetchAll(PDO::FETCH_COLUMN);
$filter = $_GET['type'] ?? null;

if ($filter) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE type = ?");
    $stmt->execute([$filter]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
}

// Gestion du panier (session)
if (isset($_GET['add_to_cart'])) {
    $prodId = (int)$_GET['add_to_cart'];
    $_SESSION['cart'][$prodId] = ($_SESSION['cart'][$prodId] ?? 0) + 1;
    // Redirection pour ne pas renvoyer le paramètre add_to_cart sur rafraîchissement
    header('Location: buyer_interface.php?type=' . urlencode($filter));
    exit;
}

// Titre de la page pour header.php
$page_title = 'Boutique';
include __DIR__ . '/includes/header.php';
?>

<div class="row">
  <?php foreach ($products as $prod): ?>
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="<?= htmlspecialchars($prod['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['name']) ?>">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($prod['name']) ?></h5>
          <p class="card-text mt-auto"><?= number_format($prod['price'], 2, ',', ' ') ?> €</p>
          <a 
            href="?add_to_cart=<?= $prod['id'] ?>&type=<?= urlencode($filter) ?>" 
            class="btn btn-success w-100"
          >
            Ajouter au panier
          </a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
