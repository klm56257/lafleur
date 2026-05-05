<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$code = $_GET['code'] ?? null;
$category = null;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? null;
    $nom = trim($_POST['nomCategorie'] ?? '');
    if ($code === null || $nom === '') {
        $error = 'Veuillez sélectionner une catégorie et saisir un nom.';
    } else {
        $stmt = $connection->prepare('UPDATE categorie SET nom_de_la_categorie = :nom WHERE code_de_la_categorie = :code');
        $stmt->execute([':nom' => $nom, ':code' => $code]);
        header('Location: BackOffice.php');
        exit;
    }
}

$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
if ($code !== null) {
    $stmt = $connection->prepare('SELECT * FROM categorie WHERE code_de_la_categorie = :code');
    $stmt->execute([':code' => $code]);
    $category = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une catégorie - La Fleur</title>
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
                        <h1 class="h3 mb-4">Modifier une catégorie</h1>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="get" action="modifierCategorie.php" class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="codeSelect" class="form-label">Choisir une catégorie</label>
                                <select id="codeSelect" name="code" class="form-select" onchange="this.form.submit()">
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorieOption): ?>
                                        <option value="<?php echo htmlspecialchars($categorieOption['code_de_la_categorie']); ?>" <?php echo $code === $categorieOption['code_de_la_categorie'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($categorieOption['nom_de_la_categorie']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 align-self-end">
                                <a class="btn btn-outline-secondary w-100" href="BackOffice.php">Retour</a>
                            </div>
                        </form>
                        <?php if ($category): ?>
                            <form method="post" action="modifierCategorie.php">
                                <input type="hidden" name="code" value="<?php echo htmlspecialchars($category['code_de_la_categorie']); ?>">
                                <div class="mb-3">
                                    <label for="nomCategorie" class="form-label">Nom de la catégorie</label>
                                    <input type="text" class="form-control" id="nomCategorie" name="nomCategorie" value="<?php echo htmlspecialchars($category['nom_de_la_categorie']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="BackOffice.php" class="btn btn-outline-secondary ms-2">Annuler</a>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">Sélectionnez une catégorie pour la modifier.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
