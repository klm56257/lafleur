<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';
$login = $_SESSION['login'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nom === '' || $prenom === '' || $adresse === '' || $tel === '') {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        if ($password !== '') {
            $stmt = $connection->prepare('UPDATE utilisateur SET nom = :nom, prenom = :prenom, adresse = :adresse, tel = :tel, mot_de_passe_user = :mdp WHERE mail_login = :login');
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':adresse' => $adresse,
                ':tel' => $tel,
                ':mdp' => sha1($password),
                ':login' => $login,
            ]);
        } else {
            $stmt = $connection->prepare('UPDATE utilisateur SET nom = :nom, prenom = :prenom, adresse = :adresse, tel = :tel WHERE mail_login = :login');
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':adresse' => $adresse,
                ':tel' => $tel,
                ':login' => $login,
            ]);
        }
        $success = 'Vos informations ont été mises à jour.';
    }
}

$stmt = $connection->prepare('SELECT * FROM utilisateur WHERE mail_login = :login');
$stmt->execute([':login' => $login]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <h1>Mon compte</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!$user): ?>
            <div class="alert alert-warning">Utilisateur introuvable.</div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Modifier mes informations</h2>
                            <form method="post" action="profile.php">
                                <div class="mb-3">
                                    <label for="login" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="login" value="<?php echo htmlspecialchars($user['mail_login']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo htmlspecialchars($user['adresse']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tel" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="tel" name="tel" value="<?php echo htmlspecialchars($user['tel']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe <small class="text-muted">(laisser vide pour conserver l'actuel)</small></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nouveau mot de passe">
                                </div>
                                <button type="submit" class="btn btn-success">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Actions</h2>
                            <p>Consultez vos commandes passées et suivez leur statut.</p>
                            <a href="order_history.php" class="btn btn-success">Voir mes commandes</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
