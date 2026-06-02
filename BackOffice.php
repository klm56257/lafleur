<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
$products = $connection->query('SELECT * FROM produit ORDER BY designation')->fetchAll();
$codes_promo = $connection->query('SELECT * FROM code_promo ORDER BY code')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">Back Office</h1>
                <p class="text-muted">Gestion des catégories, produits et codes promo.</p>
            </div>
            <a class="btn btn-secondary" href="admin_dashboard.php">Retour au dashboard</a>
        </div>

        <div class="row gy-4">
            <!-- CATEGORIES -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Catégories</h2>
                        <div class="mb-4">
                            <a class="btn btn-success me-2" href="ajouterCategorie.php">Ajouter</a>
                        </div>
                        <form class="row g-2 align-items-end mb-3" method="get" action="modifierCategorie.php">
                            <div class="col-auto">
                                <label for="modifyCategory" class="form-label mb-0">Modifier</label>
                            </div>
                            <div class="col">
                                <select id="modifyCategory" name="code" class="form-select" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?php echo htmlspecialchars($categorie['code_de_la_categorie']); ?>">
                                            <?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-primary">Modifier</button>
                            </div>
                        </form>
                        <form class="row g-2 align-items-end mb-4" method="get" action="supprimerCategorie.php">
                            <div class="col-auto">
                                <label for="deleteCategory" class="form-label mb-0">Supprimer</label>
                            </div>
                            <div class="col">
                                <select id="deleteCategory" name="code" class="form-select" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?php echo htmlspecialchars($categorie['code_de_la_categorie']); ?>">
                                            <?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Code</th><th>Nom</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($categories as $categorie): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($categorie['code_de_la_categorie']); ?></td>
                                        <td><?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRODUITS -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Produits</h2>
                        <div class="mb-4">
                            <a class="btn btn-success me-2" href="ajouterProduit.php">Ajouter</a>
                        </div>
                        <form class="row g-2 align-items-end mb-3" method="get" action="modifierProduit.php">
                            <div class="col-auto">
                                <label for="modifyProduct" class="form-label mb-0">Modifier</label>
                            </div>
                            <div class="col">
                                <select id="modifyProduct" name="ref" class="form-select" required>
                                    <option value="">Sélectionnez un produit</option>
                                    <?php foreach ($products as $produit): ?>
                                        <option value="<?php echo htmlspecialchars($produit['reference']); ?>">
                                            <?php echo htmlspecialchars($produit['designation']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-primary">Modifier</button>
                            </div>
                        </form>
                        <form class="row g-2 align-items-end mb-4" method="get" action="supprimerProduit.php">
                            <div class="col-auto">
                                <label for="deleteProduct" class="form-label mb-0">Supprimer</label>
                            </div>
                            <div class="col">
                                <select id="deleteProduct" name="ref" class="form-select" required>
                                    <option value="">Sélectionnez un produit</option>
                                    <?php foreach ($products as $produit): ?>
                                        <option value="<?php echo htmlspecialchars($produit['reference']); ?>">
                                            <?php echo htmlspecialchars($produit['designation']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Réf</th><th>Désignation</th><th>Catégorie</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($products as $produit): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($produit['reference']); ?></td>
                                        <td><?php echo htmlspecialchars($produit['designation']); ?></td>
                                        <td><?php echo htmlspecialchars($produit['code_de_la_categorie']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CODES PROMO -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Codes Promo</h2>
                        <div class="mb-4">
                            <a class="btn btn-success me-2" href="ajouterCodePromo.php">Ajouter</a>
                        </div>
                        <form class="row g-2 align-items-end mb-4" method="post" action="supprimerCodePromo.php">
                            <div class="col-auto">
                                <label class="form-label mb-0">Supprimer</label>
                            </div>
                            <div class="col">
                                <select name="code" class="form-select" required>
                                    <option value="">Sélectionnez un code</option>
                                    <?php foreach ($codes_promo as $code): ?>
                                        <option value="<?php echo htmlspecialchars($code['code']); ?>">
                                            <?php echo htmlspecialchars($code['code']); ?> — <?php echo $code['reduction']; ?>%
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Code</th><th>Réduction</th><th>Expiration</th><th>Utilisations</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($codes_promo as $code): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($code['code']); ?></td>
                                        <td><?php echo $code['reduction']; ?>%</td>
                                        <td><?php echo $code['date_expiration']; ?></td>
                                        <td><?php echo $code['max_utilisations']; ?> max</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>