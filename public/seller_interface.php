<?php
// public/seller_interface.php

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Vérifier l’authentification
require_login();

// Restreindre aux vendeurs
if ($_SESSION['user_role'] !== 'seller') {
    header('Location: buyer_interface.php');
    exit;
}

// Création / mise à jour d’un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $type  = $_POST['type'];
    $img   = $_POST['image'];

    if (!empty($_POST['id'])) {
        // Mise à jour
        $stmt = $pdo->prepare('
            UPDATE products
            SET name = ?, price = ?, type = ?, image = ?
            WHERE id = ?
        ');
        $stmt->execute([$name, $price, $type, $img, $_POST['id']]);
    } else {
        // Création
        $stmt = $pdo->prepare('
            INSERT INTO products (name, price, type, image)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$name, $price, $type, $img]);
    }

    header('Location: seller_interface.php');
    exit;
}

// Suppression d’un produit
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([ (int) $_GET['delete'] ]);
    header('Location: seller_interface.php');
    exit;
}

// Récupérer tous les produits
$prods = $pdo->query('SELECT * FROM products ORDER BY id DESC')
             ->fetchAll(PDO::FETCH_ASSOC);

// Charger l’en-tête
$page_title = 'Gestion des produits';
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Gestion des produits</h2>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Prix (€)</th>
      <th>Type</th>
      <th>Image (URL)</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($prods as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= number_format($p['price'], 2, ',', ' ') ?></td>
        <td><?= htmlspecialchars($p['type']) ?></td>
        <td><?= htmlspecialchars($p['image']) ?></td>
        <td>
          <button
            type="button"
            class="btn btn-sm btn-info btn-edit-product"
            data-id="<?= $p['id'] ?>"
            data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
            data-price="<?= $p['price'] ?>"
            data-type="<?= htmlspecialchars($p['type'], ENT_QUOTES) ?>"
            data-image="<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>"
          >
            Éditer
          </button>
          <a
            href="?delete=<?= $p['id'] ?>"
            class="btn btn-sm btn-danger"
          >
            Supprimer
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<hr>

<h3 id="form"><?= empty($_GET['edit_id']) ? 'Ajouter un produit' : 'Modifier le produit' ?></h3>
<form method="post" class="mb-5">
  <input type="hidden" name="id" id="id">

  <div class="mb-3">
    <label for="name" class="form-label">Nom</label>
    <input type="text" name="name" id="name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="price" class="form-label">Prix (€)</label>
    <input type="number" step="0.01" name="price" id="price" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="type" class="form-label">Type</label>
    <input type="text" name="type" id="type" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="image" class="form-label">Image (URL)</label>
    <input type="url" name="image" id="image" class="form-control">
  </div>

  <button type="submit" class="btn btn-success">
    <?= empty($_POST['id']) ? 'Ajouter' : 'Mettre à jour' ?>
  </button>
</form>

<?php
// Charger le pied de page
include __DIR__ . '/../includes/footer.php';
?>
