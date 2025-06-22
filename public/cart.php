<?php
// public/cart.php

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Récupérer le panier depuis la session
$cart = $_SESSION['cart'] ?? [];

// Retirer un article du panier
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    unset($cart[$id]);
    $_SESSION['cart'] = $cart;
    header('Location: cart.php');
    exit;
}

$products = [];
$total = 0;

if ($cart) {
    // Charger les détails des produits en une seule requête
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $qty = $cart[$item['id']];
        $subtotal = $item['price'] * $qty;
        $products[] = [
            'item'     => $item,
            'qty'      => $qty,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
}

// Titre pour header.php
$page_title = 'Votre Panier';
include __DIR__ . '/../includes/header.php';
?>

<h2>Panier</h2>

<?php if (empty($products)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item']['name']) ?></td>
                    <td><?= number_format($row['item']['price'], 2, ',', ' ') ?> €</td>
                    <td><?= $row['qty'] ?></td>
                    <td><?= number_format($row['subtotal'], 2, ',', ' ') ?> €</td>
                    <td>
                        <a href="?remove=<?= $row['item']['id'] ?>" class="btn btn-sm btn-danger">
                            Retirer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="3">Total</th>
                <th><?= number_format($total, 2, ',', ' ') ?> €</th>
                <th></th>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
