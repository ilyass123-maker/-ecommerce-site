<?php
// seller_interface.php

session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$userId = $_SESSION['user_id'];

// Ensure upload dir exists
$uploadDir = __DIR__ . '/assets/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle form submission (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float) $_POST['price'];
    $type        = trim($_POST['type']);

    if (!empty($_POST['id'])) {
        // Update existing product
        $id = (int) $_POST['id'];
        $stmt = $pdo->prepare("
            UPDATE products
            SET name = ?, description = ?, price = ?, type = ?
            WHERE id = ? AND seller_id = ?
        ");
        $stmt->execute([$name, $description, $price, $type, $id, $userId]);

        // Remove old images
        $del = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
        $del->execute([$id]);
    } else {
        // Insert new product
        $stmt = $pdo->prepare("
            INSERT INTO products
              (name, description, price, type, seller_id)
            VALUES (?,?,?,?,?)
        ");
        $stmt->execute([$name, $description, $price, $type, $userId]);
        $id = $pdo->lastInsertId();
    }

    // Process uploaded files
    if (!empty($_FILES['images'])) {
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp  = $_FILES['images']['tmp_name'][$i];
                $orig = basename($_FILES['images']['name'][$i]);
                $ext  = pathinfo($orig, PATHINFO_EXTENSION);
                $file = uniqid('img_') . "." . $ext;
                if (move_uploaded_file($tmp, $uploadDir . $file)) {
                    $ins = $pdo->prepare("
                        INSERT INTO product_images (product_id, url)
                        VALUES (?, ?)
                    ");
                    $ins->execute([$id, "assets/uploads/$file"]);
                }
            }
        }
    }

    header('Location: seller_interface.php');
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Delete files from disk
    $stmt = $pdo->prepare("SELECT url FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $urls = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($urls as $u) {
        $path = __DIR__ . '/' . $u;
        if (is_file($path)) {
            unlink($path);
        }
    }

    // Delete from DB
    $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")
        ->execute([$id]);
    $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?")
        ->execute([$id, $userId]);

    header('Location: seller_interface.php');
    exit;
}

// Fetch products and their images
$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
$stmt->execute([$userId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$prods = [];
foreach ($rows as $p) {
    $imgStmt = $pdo->prepare("SELECT url FROM product_images WHERE product_id = ?");
    $imgStmt->execute([$p['id']]);
    $imgs = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
    $p['images'] = $imgs;
    $prods[] = $p;
}

$page_title = 'Vos Annonces';
include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">Gérer vos annonces</h2>
<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Description</th>
      <th>Prix (€)</th>
      <th>Type</th>
      <th>Aperçu</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($prods as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= nl2br(htmlspecialchars($p['description'])) ?></td>
        <td><?= number_format($p['price'], 2, ',', ' ') ?></td>
        <td><?= htmlspecialchars($p['type']) ?></td>
        <td>
          <?php if (!empty($p['images'][0])): ?>
            <img
              src="<?= htmlspecialchars($p['images'][0]) ?>"
              style="height:50px; width:auto"
              alt=""
            >
          <?php else: ?>
            <span class="text-muted">—</span>
          <?php endif; ?>
        </td>
        <td>
          <button
            class="btn btn-sm btn-info btn-edit-product"
            data-id="<?= $p['id'] ?>"
            data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
            data-desc="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>"
            data-price="<?= $p['price'] ?>"
            data-type="<?= htmlspecialchars($p['type'], ENT_QUOTES) ?>"
            data-images="<?= htmlspecialchars(implode(',', $p['images']), ENT_QUOTES) ?>"
          >Éditer</button>
          <a
            href="?delete=<?= $p['id'] ?>"
            class="btn btn-sm btn-danger"
            onclick="return confirm('Supprimer cette annonce ?')"
          >Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<hr>

<h3 id="form">
  <?= empty($_POST['id']) ? 'Ajouter une annonce' : 'Modifier l’annonce' ?>
</h3>
<form method="post" class="mb-5" enctype="multipart/form-data">
  <input type="hidden" name="id" id="id">

  <div class="mb-3">
    <label for="name" class="form-label">Nom</label>
    <input type="text" name="name" id="name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea
      name="description"
      id="description"
      class="form-control"
      rows="3"
      required
    ></textarea>
  </div>

  <div class="mb-3">
    <label for="price" class="form-label">Prix (€)</label>
    <input
      type="number"
      step="0.01"
      name="price"
      id="price"
      class="form-control"
      required
    >
  </div>

  <div class="mb-3">
    <label for="type" class="form-label">Type</label>
    <input
      type="text"
      name="type"
      id="type"
      class="form-control"
      required
    >
  </div>

  <div class="mb-3">
    <label class="form-label">Photos (max 3)</label>
    <input type="file" name="images[]" class="form-control mb-2" accept="image/*">
    <input type="file" name="images[]" class="form-control mb-2" accept="image/*">
    <input type="file" name="images[]" class="form-control" accept="image/*">
  </div>

  <button type="submit" class="btn btn-success">
    <?= empty($_POST['id']) ? 'Ajouter' : 'Mettre à jour' ?>
  </button>
</form>

<script>
// Pré-remplissage du formulaire d'édition
document.querySelectorAll('.btn-edit-product').forEach(btn => {
  btn.addEventListener('click', () => {
    const [id,name,desc,price,type,imgs] = [
      btn.dataset.id,
      btn.dataset.name,
      btn.dataset.desc,
      btn.dataset.price,
      btn.dataset.type,
      btn.dataset.images
    ];

    document.getElementById('id').value          = id;
    document.getElementById('name').value        = name;
    document.getElementById('description').value = desc;
    document.getElementById('price').value       = price;
    document.getElementById('type').value        = type;

    // Clear previous previews and inputs
    document.querySelectorAll('input[name="images[]"]').forEach(inp => {
      inp.value = '';
      const prev = inp.parentNode.querySelector('img');
      if (prev) prev.remove();
    });

    // Show existing previews
    imgs.split(',').forEach((url,i) => {
      if (!url) return;
      const inp = document.querySelectorAll('input[name="images[]"]')[i];
      const img = document.createElement('img');
      img.src   = url;
      img.style = 'height:40px; display:block; margin:4px 0';
      inp.parentNode.insertBefore(img, inp);
    });

    location.hash = '#form';
  });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
