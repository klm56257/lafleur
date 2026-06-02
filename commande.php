<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$login = $_SESSION['login'];
$error = '';
$codePromo = '';
$reduction = 0;
$montantReduit = 0;

$cartStmt = $connection->prepare(
    'SELECT pa.reference, pa.quantite_d_article, p.designation, p.prix, p.photo, p.quantite_en_stock
     FROM pannier pa
     JOIN produit p ON pa.reference = p.reference
     WHERE pa.mail_login = :login'
);
$cartStmt->execute([':login' => $login]);
$items = $cartStmt->fetchAll();
if (count($items) === 0) {
    header('Location: cart.php');
    exit;
}

$userStmt = $connection->prepare('SELECT adresse FROM utilisateur WHERE mail_login = :login');
$userStmt->execute([':login' => $login]);
$user = $userStmt->fetch();

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['prix'] * $item['quantite_d_article'];
}

$adresseLivraison = $user['adresse'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card = trim($_POST['card'] ?? '');
    $ccv = trim($_POST['ccv'] ?? '');
    $titulaire = trim($_POST['id'] ?? '');
    $adresseLivraison = trim($_POST['livraison'] ?? '');
    $codePromo = strtoupper(trim($_POST['code_promo'] ?? ''));

    if ($card === '' || $ccv === '' || $titulaire === '' || $adresseLivraison === '') {
        $error = 'Veuillez remplir tous les champs du formulaire de commande.';
    } else {
        foreach ($items as $item) {
            if ($item['quantite_d_article'] > $item['quantite_en_stock']) {
                $error = 'La quantité demandée pour "' . htmlspecialchars($item['designation']) . '" dépasse le stock disponible.';
                break;
            }
        }
    }

    if ($error === '' && $codePromo !== '') {
        $promoStmt = $connection->prepare(
            'SELECT cp.* FROM code_promo cp
             LEFT JOIN utilisation_code_promo ucp ON cp.code = ucp.code AND ucp.mail_login = ?
             WHERE cp.code = ? AND cp.date_expiration >= CURDATE() AND ucp.mail_login IS NULL'
        );
        $promoStmt->execute([$login, $codePromo]);
        $promo = $promoStmt->fetch();

        if (!$promo) {
            $error = 'Code promo invalide, expiré ou déjà utilisé.';
            $codePromo = '';
        } else {
            $reduction = $promo['reduction'];
            $montantReduit = $subtotal * ($reduction / 100);
        }
    }

    $total = $subtotal - $montantReduit;

    if ($error === '') {
        try {
            $connection->beginTransaction();

            $orderStmt = $connection->prepare(
                'INSERT INTO commande (mail_login, etat, adresse_livraison, bon_de_reduction, total) VALUES (:login, :etat, :adresse, :bon, :total)'
            );
            $orderStmt->execute([
                ':login' => $login,
                ':etat' => 'Confirmé',
                ':adresse' => $adresseLivraison,
                ':bon' => $codePromo ?: null,
                ':total' => $total,
            ]);
            $orderId = $connection->lastInsertId();

            $lineStmt = $connection->prepare(
                'INSERT INTO ligne_commande (commande_id, reference, quantite, prix_unitaire) VALUES (:commande_id, :reference, :quantite, :prix_unitaire)'
            );
            foreach ($items as $item) {
                $lineStmt->execute([
                    ':commande_id' => $orderId,
                    ':reference' => $item['reference'],
                    ':quantite' => $item['quantite_d_article'],
                    ':prix_unitaire' => $item['prix'],
                ]);
            }

            if ($codePromo !== '') {
                $insertUtilisation = $connection->prepare(
                    'INSERT INTO utilisation_code_promo (mail_login, code) VALUES (?, ?)'
                );
                $insertUtilisation->execute([$login, $codePromo]);
            }

            $clearStmt = $connection->prepare('DELETE FROM pannier WHERE mail_login = :login');
            $clearStmt->execute([':login' => $login]);

            $connection->commit();
            header('Location: order_details.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $connection->rollBack();
            $error = 'Une erreur est survenue lors de l\'enregistrement de la commande. Veuillez réessayer.';
        }
    }
}

$total = $subtotal - $montantReduit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer commande - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Passer commande</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Informations de paiement</h2>
                        <form method="post" action="commande.php">
                            <div class="mb-3">
                                <label for="card" class="form-label">Numéro de carte</label>
                                <input type="text" class="form-control" id="card" name="card" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="ccv" class="form-label">CCV</label>
                                    <input type="text" class="form-control" id="ccv" name="ccv" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="id" class="form-label">Titulaire de la carte</label>
                                    <input type="text" class="form-control" id="id" name="id" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="livraison" class="form-label">Adresse de livraison</label>
                                <input type="text" class="form-control" id="livraison" name="livraison" value="<?php echo htmlspecialchars($adresseLivraison); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="code_promo" class="form-label">Code promo</label>
                                <input type="text" class="form-control" id="code_promo" name="code_promo" value="<?php echo htmlspecialchars($codePromo); ?>" placeholder="Entrez votre code promo (optionnel)">
                            </div>
                            <button type="submit" class="btn btn-success">Confirmer ma commande</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Récapitulatif de la commande</h2>
                        <div class="list-group mb-3">
                            <?php foreach ($items as $item): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['designation']); ?></strong><br>
                                            Quantité : <?php echo (int)$item['quantite_d_article']; ?>
                                        </div>
                                        <div><?php echo number_format($item['prix'] * $item['quantite_d_article'], 2, ',', ' '); ?> €</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="mb-1">Sous-total : <strong><?php echo number_format($subtotal, 2, ',', ' '); ?> €</strong></p>
                        <?php if ($montantReduit > 0): ?>
                            <p class="mb-1 text-success">
                                Code promo (<?php echo $reduction; ?>%) : 
                                -<?php echo number_format($montantReduit, 2, ',', ' '); ?> €
                            </p>
                        <?php endif; ?>
                        <h3>Total : <?php echo number_format($total, 2, ',', ' '); ?> €</h3>
                    </div>
                </div>
                <a href="cart.php" class="btn btn-outline-secondary">Retour au panier</a>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>