<?php


session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_login();


$filter = $_GET['type'] ?? null;
if ($filter) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE type = ?");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}
$rawProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$products = [];
foreach ($rawProducts as $p) {
    $imgStmt = $pdo->prepare("SELECT url FROM product_images WHERE product_id = ?");
    $imgStmt->execute([$p['id']]);
    $imgs = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

    
    $first = $imgs[0] 
           ?? $p['image'] 
           ?? 'assets/img/placeholder.png';

   
    $p['display_image'] = ltrim($first, '/');
    $products[] = $p;
}


if (isset($_GET['add_to_cart'])) {
    $id = (int) $_GET['add_to_cart'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header('Location: buyer_interface.php?type=' . urlencode($filter));
    exit;
}

$page_title = 'Boutique';
include __DIR__ . '/includes/header.php';
?>

<div class="row">
  <?php foreach ($products as $prod): ?>
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img
          src="<?= htmlspecialchars($prod['display_image']) ?>"
          class="card-img-top"
          alt="<?= htmlspecialchars($prod['name']) ?>"
        >
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($prod['name']) ?></h5>
          <p class="card-text mt-auto"><?= number_format($prod['price'], 2, ',', ' ') ?> dh</p>
          <a
            href="?add_to_cart=<?= $prod['id'] ?>&type=<?= urlencode($filter) ?>"
            class="btn btn-success w-100"
          >Ajouter au panier</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
