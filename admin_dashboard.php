<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$products = $connection->query('SELECT * FROM produit ORDER BY designation')->fetchAll();
$categories = $connection->query('SELECT * FROM categorie ORDER BY nom_de_la_categorie')->fetchAll();
$stockByCategory = $connection->query('SELECT code_de_la_categorie, SUM(quantite_en_stock) AS total_stock FROM produit GROUP BY code_de_la_categorie')->fetchAll();
$orders = $connection->query('SELECT * FROM commande ORDER BY date_commande DESC LIMIT 20')->fetchAll();
$topProducts = $connection->query('SELECT p.reference, p.designation, SUM(l.quantite) AS total_quantity FROM ligne_commande l JOIN produit p ON l.reference = p.reference GROUP BY l.reference ORDER BY total_quantity DESC LIMIT 10')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Lafleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Administration</h1>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2>Inventaire</h2>
                        <p>Nombre de produits : <?php echo count($products); ?></p>
                        <p>Nombre de catégories : <?php echo count($categories); ?></p>
                        <table class="table table-sm">
                            <thead><tr><th>Catégorie</th><th>Stock total</th></tr></thead>
                            <tbody>
                                <?php foreach ($stockByCategory as $row): ?>
                                    <tr><td><?php echo htmlspecialchars($row['code_de_la_categorie']); ?></td><td><?php echo (int)$row['total_stock']; ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="d-flex gap-2 mt-3">
                            <a class="btn btn-primary" href="BackOffice.php">Accéder au Back Office</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2>Top produits vendus</h2>
                        <table class="table table-sm">
                            <thead><tr><th>Produit</th><th>Qté vendues</th></tr></thead>
                            <tbody>
                                <?php foreach ($topProducts as $row): ?>
                                    <tr><td><?php echo htmlspecialchars($row['designation']); ?></td><td><?php echo (int)$row['total_quantity']; ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2>Commandes récentes</h2>
                        <table class="table table-sm">
                            <thead><tr><th>ID</th><th>Client</th><th>Date</th><th>Total</th><th>État</th><th>Détails</th></tr></thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['mail_login']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?></td>
                                        <td><?php echo number_format($order['total'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo htmlspecialchars($order['etat']); ?></td>
                                        <td><a href="order_details.php?id=<?php echo $order['id']; ?>">Voir</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
