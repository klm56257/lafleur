<?php
require_once 'connection.php';
$query = trim($_GET['q'] ?? '');
$results = [];
if ($query !== '') {
    $stmt = $connection->prepare('SELECT * FROM produit WHERE reference LIKE :query OR designation LIKE :query ORDER BY designation');
    $stmt->execute([':query' => "%$query%"]);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Recherche de produits</h1>
        <form class="mb-4" method="get" action="search.php">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Rechercher une référence ou un produit" value="<?php echo htmlspecialchars($query); ?>">
                <button class="btn btn-success" type="submit">Rechercher</button>
            </div>
        </form>
        <?php if ($query === ''): ?>
            <p>Entrez un mot-clé pour rechercher un produit.</p>
        <?php elseif (count($results) === 0): ?>
            <div class="alert alert-warning">Aucun produit trouvé pour "<?php echo htmlspecialchars($query); ?>".</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($results as $produit): ?>
                    <?php $photo = file_exists(__DIR__ . '/IMG/' . $produit['photo'] . '.jpg') ? 'IMG/' . $produit['photo'] . '.jpg' : 'IMG/exemple.jpg'; ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $photo; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['designation']); ?>" onerror="this.src='IMG/exemple.jpg'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($produit['designation']); ?></h5>
                                <p class="card-text">Référence : <?php echo htmlspecialchars($produit['reference']); ?></p>
                                <p class="fw-bold mt-auto"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                                <a class="btn btn-success" href="product.php?ref=<?php echo urlencode($produit['reference']); ?>">Voir</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
