<?php
require_once 'connection.php';
$code = $_GET['code'] ?? '';
$category = null;
$produits = [];
if ($code !== '') {
    $stmt = $connection->prepare('SELECT * FROM categorie WHERE code_de_la_categorie = :code');
    $stmt->execute([':code' => $code]);
    $category = $stmt->fetch();
    if ($category) {
        $stmt = $connection->prepare('SELECT * FROM produit WHERE code_de_la_categorie = :code ORDER BY designation');
        $stmt->execute([':code' => $code]);
        $produits = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégorie - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <?php if (!$category): ?>
            <div class="alert alert-warning">Catégorie introuvable. <a href="index.php">Retour à l'accueil</a></div>
        <?php else: ?>
            <div class="mb-4">
                <h1><?php echo htmlspecialchars($category['nom_de_la_categorie']); ?></h1>
                <p>Produits disponibles dans cette catégorie.</p>
            </div>
            <?php if (count($produits) === 0): ?>
                <div class="alert alert-info">Aucun produit trouvé dans cette catégorie.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($produits as $produit): ?>
                        <?php
                        $photo = 'IMG/' . ($produit['photo'] ?: 'exemple') . '.jpg';
                        $photoAttr = file_exists(__DIR__ . '/' . $photo) ? $photo : 'IMG/exemple.jpg';
                        ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <img src="<?php echo $photoAttr; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['designation']); ?>" onerror="this.src='IMG/exemple.jpg'">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($produit['designation']); ?></h5>
                                    <p class="fw-bold my-2"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                                    <p class="text-muted">Stock : <?php echo (int)$produit['quantite_en_stock']; ?></p>
                                    <a class="btn btn-success mt-auto" href="product.php?ref=<?php echo urlencode($produit['reference']); ?>">Voir le produit</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
