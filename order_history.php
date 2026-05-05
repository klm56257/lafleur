<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$login = $_SESSION['login'];
$stmt = $connection->prepare('SELECT * FROM commande WHERE mail_login = :login ORDER BY date_commande DESC');
$stmt->execute([':login' => $login]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Historique des commandes</h1>
        <?php if (count($orders) === 0): ?>
            <div class="alert alert-info">Vous n'avez pas encore passé de commande.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($orders as $order): ?>
                    <a class="list-group-item list-group-item-action" href="order_details.php?id=<?php echo $order['id']; ?>">
                        <strong>Commande n°<?php echo $order['id']; ?></strong> - <?php echo htmlspecialchars($order['etat']); ?> - <?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?> - <?php echo number_format($order['total'], 2, ',', ' '); ?> €
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
