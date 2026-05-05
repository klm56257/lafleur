<?php
require_once 'connection.php';
$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
$produits = $connection->query('SELECT * FROM produit ORDER BY prix DESC LIMIT 8')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Italianno" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <section class="text-center mb-5">
            <h1 class="display-5">Bienvenue sur La Fleur</h1>
            <p class="lead">Découvrez des fleurs fraîches, des bouquets et des plantes pour toutes les occasions.</p>
        </section>


        <section>
            <h2>Produits </h2>
            <div class="row g-4">
                <?php foreach ($produits as $produit): ?>
                    <?php
                    $photo = 'IMG/' . ($produit['photo'] ?: 'exemple') . '.jpg';
                    $photoAttr = file_exists(__DIR__ . '/' . $photo) ? $photo : 'IMG/exemple.jpg';
                    ?>
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $photoAttr; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['designation']); ?>" onerror="this.src='IMG/exemple.jpg'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($produit['designation']); ?></h5>
                                <p class="card-text">Référence : <?php echo htmlspecialchars($produit['reference']); ?></p>
                                <p class="fw-bold my-2"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                                <a class="btn btn-success mt-auto" href="product.php?ref=<?php echo urlencode($produit['reference']); ?>">Voir le produit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>