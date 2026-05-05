<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$login = $_SESSION['login'];
$stmt = $connection->prepare('SELECT p.reference, p.designation, p.prix, p.photo, pa.quantite_d_article FROM pannier pa JOIN produit p ON pa.reference = p.reference WHERE pa.mail_login = :login');
$stmt->execute([':login' => $login]);
$items = $stmt->fetchAll();
$total = 0;
foreach ($items as $item) {
    $total += $item['prix'] * $item['quantite_d_article'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Mon panier</h1>
        <?php if (count($items) === 0): ?>
            <div class="alert alert-info">Votre panier est vide. <a href="index.php">Voir les produits</a></div>
        <?php else: ?>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $photo = 'IMG/' . ($item['photo'] ?: 'exemple') . '.jpg';
                            $photoAttr = file_exists(__DIR__ . '/' . $photo) ? $photo : 'IMG/exemple.jpg';
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo $photoAttr; ?>" alt="<?php echo htmlspecialchars($item['designation']); ?>" width="80" onerror="this.src='IMG/exemple.jpg'">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['designation']); ?></strong><br>
                                            Référence : <?php echo htmlspecialchars($item['reference']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['prix'], 2, ',', ' '); ?> €</td>
                                <td><?php echo (int)$item['quantite_d_article']; ?></td>
                                <td><?php echo number_format($item['prix'] * $item['quantite_d_article'], 2, ',', ' '); ?> €</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="addquantite.php?ref=<?php echo urlencode($item['reference']); ?>" class="btn btn-sm btn-outline-success">+</a>
                                        <a href="delquantite.php?ref=<?php echo urlencode($item['reference']); ?>" class="btn btn-sm btn-outline-warning">-</a>
                                        <a href="destroy-bag.php?ref=<?php echo urlencode($item['reference']); ?>" class="btn btn-sm btn-outline-danger">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="fs-4">Total : <?php echo number_format($total, 2, ',', ' '); ?> €</div>
                <a class="btn btn-success btn-lg" href="commande.php?total=<?php echo urlencode($total); ?>">Valider ma commande</a>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
