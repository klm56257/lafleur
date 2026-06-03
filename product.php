<?php
session_start();
require_once 'connection.php';

$ref = $_GET['ref'] ?? '';
$produit = null;
$login = $_SESSION['login'] ?? null;

if ($ref !== '') {
    $stmt = $connection->prepare('SELECT * FROM produit WHERE reference = :ref');
    $stmt->execute([':ref' => $ref]);
    $produit = $stmt->fetch();
}

// Récupérer les avis
$avis = [];
$moyenne = null;
$dejaAvis = false;
$aAchete = false;

if ($produit) {
    $avisStmt = $connection->prepare(
        'SELECT a.*, u.nom, u.prenom FROM avis a
         JOIN utilisateur u ON a.mail_login COLLATE utf8mb4_0900_ai_ci = u.mail_login
         WHERE a.reference COLLATE utf8mb4_0900_ai_ci = ?
         ORDER BY a.date_avis DESC'
    );
    $avisStmt->execute([$ref]);
    $avis = $avisStmt->fetchAll();

    if (count($avis) > 0) {
        $moyenne = array_sum(array_column($avis, 'note')) / count($avis);
    }

    if ($login) {
        // A déjà laissé un avis ?
        $dejaAvisStmt = $connection->prepare('SELECT id FROM avis WHERE mail_login COLLATE utf8mb4_0900_ai_ci = ? AND reference COLLATE utf8mb4_0900_ai_ci = ?');
        $dejaAvisStmt->execute([$login, $ref]);
        $dejaAvis = (bool)$dejaAvisStmt->fetch();

        // A acheté le produit ?
        $achatStmt = $connection->prepare(
            'SELECT lc.reference FROM ligne_commande lc
             JOIN commande c ON lc.commande_id = c.id
             WHERE c.mail_login = ? AND lc.reference = ?'
        );
        $achatStmt->execute([$login, $ref]);
        $aAchete = (bool)$achatStmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <?php if (!$produit): ?>
            <div class="alert alert-warning">Produit introuvable. <a href="index.php">Retour à l'accueil</a></div>
        <?php else: ?>
            <?php
            $photo = 'IMG/' . ($produit['photo'] ?: 'exemple') . '.jpg';
            $photoAttr = file_exists(__DIR__ . '/' . $photo) ? $photo : 'IMG/exemple.jpg';
            ?>
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <img src="<?php echo $photoAttr; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['designation']); ?>" onerror="this.src='IMG/exemple.jpg'">
                    </div>
                </div>
                <div class="col-md-7">
                    <h1><?php echo htmlspecialchars($produit['designation']); ?></h1>
                    <p class="text-muted">Référence : <?php echo htmlspecialchars($produit['reference']); ?></p>
                    <p class="fs-4 fw-bold"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</p>
                    <p>Stock disponible : <?php echo (int)$produit['quantite_en_stock']; ?></p>
                    <p>Catégorie : <?php echo htmlspecialchars($produit['code_de_la_categorie']); ?></p>
                    <?php if ($moyenne !== null): ?>
                        <p>Note moyenne : <strong><?php echo number_format($moyenne, 1); ?>/5</strong> (<?php echo count($avis); ?> avis)</p>
                    <?php else: ?>
                        <p class="text-muted">Aucun avis pour ce produit.</p>
                    <?php endif; ?>
                    <a href="addbag.php?ref=<?php echo urlencode($produit['reference']); ?>" class="btn btn-success">Ajouter au panier</a>
                    <a href="cart.php" class="btn btn-outline-secondary">Voir le panier</a>
                </div>
            </div>

            <!-- AVIS -->
            <div class="mt-5">
                <h2 class="h4 mb-4">Avis clients</h2>

                <?php if (isset($_GET['erreur'])): ?>
                    <?php if ($_GET['erreur'] === 'achat'): ?>
                        <div class="alert alert-warning">Vous devez avoir acheté ce produit pour laisser un avis.</div>
                    <?php elseif ($_GET['erreur'] === 'existe'): ?>
                        <div class="alert alert-warning">Vous avez déjà laissé un avis pour ce produit.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($login && $aAchete && !$dejaAvis): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h5">Laisser un avis</h3>
                            <form method="post" action="addAvis.php">
                                <input type="hidden" name="reference" value="<?php echo htmlspecialchars($ref); ?>">
                                <div class="mb-3">
                                    <label class="form-label">Note</label>
                                    <select name="note" class="form-select" required>
                                        <option value="">Choisissez une note</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                        <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                        <option value="3">⭐⭐⭐ (3/5)</option>
                                        <option value="2">⭐⭐ (2/5)</option>
                                        <option value="1">⭐ (1/5)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Commentaire</label>
                                    <textarea name="commentaire" class="form-control" rows="3" placeholder="Votre avis sur ce produit..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Publier mon avis</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($login && !$aAchete): ?>
                    <div class="alert alert-info">Achetez ce produit pour pouvoir laisser un avis.</div>
                <?php elseif (!$login): ?>
                    <div class="alert alert-info">
                        <a href="login.php">Connectez-vous</a> pour laisser un avis.
                    </div>
                <?php endif; ?>

                <?php if (count($avis) === 0): ?>
                    <p class="text-muted">Aucun avis pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($avis as $a): ?>
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?></strong>
                                    <span class="text-muted"><?php echo date('d/m/Y', strtotime($a['date_avis'])); ?></span>
                                </div>
                                <div class="my-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php echo $i <= $a['note'] ? '⭐' : '☆'; ?>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($a['commentaire']): ?>
                                    <p class="mb-0"><?php echo htmlspecialchars($a['commentaire']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>