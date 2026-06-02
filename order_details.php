<?php
session_start();
require_once 'connection.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: order_history.php');
    exit;
}

$admin = !empty($_SESSION['admin']);
$login = $_SESSION['login'] ?? null;

$stmt = $connection->prepare('SELECT * FROM commande WHERE id = :id');
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();
if (!$order) {
    header('Location: order_history.php');
    exit;
}

if (!$admin && $order['mail_login'] !== $login) {
    header('Location: order_history.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$admin) {
    $adresse = trim($_POST['adresse']);
    if ($adresse !== '') {
        $update = $connection->prepare('UPDATE commande SET adresse_livraison = :adresse WHERE id = :id AND etat = :etat');
        $update->execute([':adresse' => $adresse, ':id' => $orderId, ':etat' => 'En attente']);
        header('Location: order_details.php?id=' . $orderId);
        exit;
    }
}

$itemsStmt = $connection->prepare('SELECT lc.*, p.designation, p.photo FROM ligne_commande lc JOIN produit p ON lc.reference = p.reference WHERE lc.commande_id = :id');
$itemsStmt->execute([':id' => $orderId]);
$items = $itemsStmt->fetchAll();

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['prix_unitaire'] * $item['quantite'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande #<?php echo $orderId; ?> - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Détails de la commande #<?php echo $orderId; ?></h1>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Date :</strong> <?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?></p>
                <p><strong>État :</strong> <?php echo htmlspecialchars($order['etat']); ?></p>
                <p><strong>Adresse de livraison :</strong> <?php echo htmlspecialchars($order['adresse_livraison'] ?? 'Non renseignée'); ?></p>
                <?php if (!empty($order['bon_de_reduction'])): ?>
                    <p><strong>Code promo utilisé :</strong> <?php echo htmlspecialchars($order['bon_de_reduction']); ?></p>
                    <p><strong>Sous-total avant réduction :</strong> <?php echo number_format($subtotal, 2, ',', ' '); ?> €</p>
                    <p class="text-success"><strong>Réduction :</strong> -<?php echo number_format($subtotal - $order['total'], 2, ',', ' '); ?> €</p>
                <?php endif; ?>
                <p><strong>Montant total :</strong> <?php echo number_format($order['total'], 2, ',', ' '); ?> €</p>
            </div>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['designation']); ?></td>
                            <td><?php echo (int)$item['quantite']; ?></td>
                            <td><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?> €</td>
                            <td><?php echo number_format($item['prix_unitaire'] * $item['quantite'], 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (!$admin && $order['etat'] === 'En attente'): ?>
            <div class="card">
                <div class="card-body">
                    <h2>Modifier l'adresse de livraison</h2>
                    <form method="post" action="order_details.php?id=<?php echo $orderId; ?>">
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse de livraison</label>
                            <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo htmlspecialchars($order['adresse_livraison']); ?>" required>
                        </div>
                        <button class="btn btn-primary">Mettre à jour l'adresse</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>