<?php
require_once 'connection.php';
$ref = $_GET['ref'] ?? '';
$produit = null;
if ($ref !== '') {
    $stmt = $connection->prepare('SELECT * FROM produit WHERE reference = :ref');
    $stmt->execute([':ref' => $ref]);
    $produit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <?php if (!$produit): ?>
            <div class="alert alert-warning">Produit introuvable. <a href="index.php">Retour à l'accueil</a></div>
        <?php else: ?>
            <?php
            $photo = 'IMG/' . ($produit['photo'] ?: 'exemple') . '.jpg';
            $photoAttr = file_exists(__DIR__ . '/' . $photo) ? $photo : 'IMG/exemple.jpg';
            ?>
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <img src="<?php echo $photoAttr; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['designation']); ?>" onerror="this.src='IMG/exemple.jpg'">
                    </div>
                </div>
                <div class="col-md-7">
                    <h1><?php echo htmlspecialchars($produit['designation']); ?></h1>
                    <p class="text-muted">Référence : <?php echo htmlspecialchars($produit['reference']); ?></p>
                    <p class="fs-4 fw-bold"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                    <p>Stock disponible : <?php echo (int)$produit['quantite_en_stock']; ?></p>
                    <p>Catégorie : <?php echo htmlspecialchars($produit['code_de_la_categorie']); ?></p>
                    <a href="addbag.php?ref=<?php echo urlencode($produit['reference']); ?>" class="btn btn-success">Ajouter au panier</a>
                    <a href="cart.php" class="btn btn-outline-secondary">Voir le panier</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
