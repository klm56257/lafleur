<?php
session_start();
require_once 'connection.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $tel = trim($_POST['tel'] ?? '');

    if ($email === '' || $password === '' || $nom === '' || $prenom === '' || $adresse === '' || $tel === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $connection->prepare('SELECT COUNT(*) FROM utilisateur WHERE mail_login = :email');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            $stmt = $connection->prepare('INSERT INTO utilisateur (mail_login, mot_de_passe_user, nom, prenom, adresse, tel) VALUES (:email, :mdp, :nom, :prenom, :adresse, :tel)');
            $stmt->execute([
                ':email' => $email,
                ':mdp' => sha1($password),
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':adresse' => $adresse,
                ':tel' => $tel,
            ]);
            $_SESSION['login'] = $email;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Créer un compte</h2>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="post" action="register.php">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse" required>
                            </div>
                            <div class="mb-3">
                                <label for="tel" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="tel" name="tel" required>
                            </div>
                            <button type="submit" class="btn btn-success">Créer un compte</button>
                            <a class="btn btn-link" href="login.php">Déjà inscrit ?</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
