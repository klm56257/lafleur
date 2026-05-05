<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';
$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h1 class="h3 mb-4">Ajouter un produit</h1>
                        <form method="post" action="addProduit.php">
                            <div class="mb-3">
                                <label for="referenceProduit" class="form-label">Référence du produit</label>
                                <input type="text" class="form-control" id="referenceProduit" name="referenceProduit" required>
                            </div>
                            <div class="mb-3">
                                <label for="designationProduit" class="form-label">Désignation</label>
                                <input type="text" class="form-control" id="designationProduit" name="designationProduit" required>
                            </div>
                            <div class="mb-3">
                                <label for="photoProduit" class="form-label">Nom de la photo</label>
                                <input type="text" class="form-control" id="photoProduit" name="photoProduit" required>
                                <div class="form-text">Le nom de fichier sera utilisé dans le dossier IMG, sans l'extension.</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="prixProduit" class="form-label">Prix</label>
                                    <input type="number" step="0.01" class="form-control" id="prixProduit" name="prixProduit" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantiteStockProduit" class="form-label">Quantité en stock</label>
                                    <input type="number" class="form-control" id="quantiteStockProduit" name="quantite_stock_Produit" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="codeCategorieProduit" class="form-label">Catégorie</label>
                                <select class="form-select" id="codeCategorieProduit" name="code_categorie_Produit" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?php echo htmlspecialchars($categorie['code_de_la_categorie']); ?>"><?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Ajouter le produit</button>
                            <a href="BackOffice.php" class="btn btn-outline-secondary ms-2">Retour au Back Office</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


