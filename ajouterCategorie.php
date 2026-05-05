<?php
session_start();
require_once 'connection.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie - La Fleur</title>
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
                        <h1 class="h3 mb-4">Ajouter une nouvelle catégorie</h1>
                        <form method="post" action="addCategorie.php">
                            <div class="mb-3">
                                <label for="codeCategorie" class="form-label">Code de la catégorie</label>
                                <input type="text" class="form-control" id="codeCategorie" name="codeCategorie" required>
                            </div>
                            <div class="mb-3">
                                <label for="nomCategorie" class="form-label">Nom de la catégorie</label>
                                <input type="text" class="form-control" id="nomCategorie" name="nomCategorie" required>
                            </div>
                            <button type="submit" class="btn btn-success">Ajouter la catégorie</button>
                            <a href="admin_dashboard.php" class="btn btn-outline-secondary ms-2">Retour à l'administration</a>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Catégories existantes</h2>
                        <?php if (count($categories) === 0): ?>
                            <div class="alert alert-info">Aucune catégorie disponible.</div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($categories as $categorie): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($categorie['code_de_la_categorie']); ?> — <?php echo htmlspecialchars($categorie['nom_de_la_categorie']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



