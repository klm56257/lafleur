<?php
session_start();
require_once 'connection.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
$codes_promo = $connection->query('SELECT * FROM code_promo ORDER BY code')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un code promo - La Fleur</title>
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
                        <h1 class="h3 mb-4">Ajouter un code promo</h1>
                        <form method="post" action="addCodePromo.php">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code promo</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="ex: BIENVENUE10" required>
                                <small class="text-muted">Sera automatiquement mis en majuscules.</small>
                            </div>
                            <div class="mb-3">
                                <label for="reduction" class="form-label">Réduction (%)</label>
                                <input type="number" class="form-control" id="reduction" name="reduction" min="1" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="date_expiration" class="form-label">Date d'expiration</label>
                                <input type="date" class="form-control" id="date_expiration" name="date_expiration" required>
                            </div>
                            <div class="mb-3">
                                <label for="max_utilisations" class="form-label">Nombre max d'utilisations</label>
                                <input type="number" class="form-control" id="max_utilisations" name="max_utilisations" min="1" value="1" required>
                            </div>
                            <button type="submit" class="btn btn-success">Ajouter le code promo</button>
                            <a href="BackOffice.php" class="btn btn-outline-secondary ms-2">Retour au back office</a>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Codes promo existants</h2>
                        <?php if (count($codes_promo) === 0): ?>
                            <div class="alert alert-info">Aucun code promo disponible.</div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($codes_promo as $code): ?>
                                    <li class="list-group-item">
                                        <?php echo htmlspecialchars($code['code']); ?> — 
                                        <?php echo $code['reduction']; ?>% — 
                                        Expire le <?php echo $code['date_expiration']; ?> — 
                                        <?php echo $code['nb_utilisations']; ?>/<?php echo $code['max_utilisations']; ?> utilisations
                                    </li>
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