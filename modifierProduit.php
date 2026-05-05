<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$ref = $_GET['ref'] ?? null;
$product = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ref = $_POST['ref'] ?? null;
    $designation = trim($_POST['designationProduit'] ?? '');
    $photo = trim($_POST['photoProduit'] ?? '');
    $prix = trim($_POST['prixProduit'] ?? '');
    $quantite = trim($_POST['quantite_stock_Produit'] ?? '');
    $codeCategorie = trim($_POST['code_categorie_Produit'] ?? '');

    if (!$ref || $designation === '' || $photo === '' || $prix === '' || $quantite === '' || $codeCategorie === '') {
        $error = 'Veuillez remplir tous les champs du formulaire.';
    } else {
        $stmt = $connection->prepare(
            'UPDATE produit SET designation = :designation, photo = :photo, prix = :prix, quantite_en_stock = :quantite, code_de_la_categorie = :code WHERE reference = :reference'
        );
        $stmt->execute([
            ':designation' => $designation,
            ':photo' => $photo,
            ':prix' => $prix,
            ':quantite' => $quantite,
            ':code' => $codeCategorie,
            ':reference' => $ref,
        ]);
        header('Location: BackOffice.php');
        exit;
    }
}

$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
if ($ref !== null) {
    $stmt = $connection->prepare('SELECT * FROM produit WHERE reference = :ref');
    $stmt->execute([':ref' => $ref]);
    $product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit - La Fleur</title>
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
                        <h1 class="h3 mb-4">Modifier un produit</h1>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="get" action="modifierProduit.php" class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="productSelect" class="form-label">Choisir un produit</label>
                                <select id="productSelect" name="ref" class="form-select" onchange="this.form.submit()">
                                    <option value="">Sélectionnez un produit</option>
                                    <?php foreach ($connection->query('SELECT reference, designation FROM produit ORDER BY designation') as $produitOption): ?>
                                        <option value="<?php echo htmlspecialchars($produitOption['reference']); ?>" <?php echo $ref === $produitOption['reference'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($produitOption['designation']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 align-self-end">
                                <a class="btn btn-outline-secondary w-100" href="BackOffice.php">Retour</a>
                            </div>
                        </form>
                        <?php if ($product): ?>
                            <form method="post" action="modifierProduit.php">
                                <input type="hidden" name="ref" value="<?php echo htmlspecialchars($product['reference']); ?>">
                                <div class="mb-3">
                                    <label for="designationProduit" class="form-label">Désignation</label>
                                    <input type="text" class="form-control" id="designationProduit" name="designationProduit" value="<?php echo htmlspecialchars($product['designation']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="photoProduit" class="form-label">Nom de la photo</label>
                                    <input type="text" class="form-control" id="photoProduit" name="photoProduit" value="<?php echo htmlspecialchars($product['photo']); ?>" required>
                                    <div class="form-text">Le nom de fichier sera utilisé dans le dossier IMG, sans l'extension.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="prixProduit" class="form-label">Prix</label>
                                        <input type="number" step="0.01" class="form-control" id="prixProduit" name="prixProduit" value="<?php echo htmlspecialchars($product['prix']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="quantiteStockProduit" class="form-label">Quantité en stock</label>
                                        <input type="number" class="form-control" id="quantiteStockProduit" name="quantite_stock_Produit" value="<?php echo htmlspecialchars($product['quantite_en_stock']); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="codeCategorieProduit" class="form-label">Catégorie</label>
                                    <select class="form-select" id="codeCategorieProduit" name="code_categorie_Produit" required>
                                        <option value="">Sélectionnez une catégorie</option>
                                        <?php foreach ($categories as $categorieOption): ?>
                                            <option value="<?php echo htmlspecialchars($categorieOption['code_de_la_categorie']); ?>" <?php echo $product['code_de_la_categorie'] === $categorieOption['code_de_la_categorie'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($categorieOption['nom_de_la_categorie']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="BackOffice.php" class="btn btn-outline-secondary ms-2">Annuler</a>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">Sélectionnez un produit pour le modifier.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
