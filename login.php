<?php
session_start();
require_once 'connection.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($loginInput === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $mdpHash = sha1($password);

        $stmt = $connection->prepare('SELECT * FROM utilisateur WHERE mail_login = :login AND mot_de_passe_user = :mdp');
        $stmt->execute([
            ':login' => $loginInput,
            ':mdp' => $mdpHash,
        ]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['login'] = $user['mail_login'];
            header('Location: index.php');
            exit;
        }

        $stmt = $connection->prepare('SELECT * FROM administrateur WHERE username_admin = :login AND mot_de_passe_admin = :mdp');
        $stmt->execute([
            ':login' => $loginInput,
            ':mdp' => $mdpHash,
        ]);
        $admin = $stmt->fetch();

        if ($admin) {
            $_SESSION['login'] = $admin['username_admin'];
            $_SESSION['admin'] = true;
            header('Location: admin_dashboard.php');
            exit;
        }

        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - La Fleur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css.css" rel="stylesheet">
</head>
<body>
    <?php include 'site_header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Connexion</h2>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="post" action="login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email ou identifiant</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-success">Se connecter</button>
                            <a class="btn btn-link" href="register.php">S'inscrire</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
