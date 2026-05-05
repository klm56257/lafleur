<?php header('Location: index.php'); exit; ?>
$produits = $connection->query('SELECT * FROM produit ORDER BY prix DESC LIMIT 8')->fetchAll();
?>
<main class="container py-5">
    <section class="text-center mb-5">
        <h1 class="display-5">Bienvenue sur La Fleur</h1>
        <p class="lead">Découvrez des fleurs fraîches, des bouquets et des plantes pour toutes les occasions.</p>
    </section>

    <section class="mb-5">
        <h2>Nos catégories</h2>
        <div class="row g-4">
            <?php foreach ($categories as $categorie): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?></h5>
                            <p class="card-text">Explorez des fleurs et bouquets de la catégorie <?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?>.</p>
                            <a class="btn btn-outline-success" href="categories.php?code=<?php echo urlencode($categorie['code_de_la_categorie']); ?>">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2>Produits phares</h2>
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