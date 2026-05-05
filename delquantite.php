<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['login'];
$ref = $_REQUEST['ref'] ?? '';
if ($ref === '') {
    header('Location: cart.php');
    exit;
}

$stmt = $connection->prepare('SELECT quantite_d_article FROM pannier WHERE mail_login = :login AND reference = :ref');
$stmt->execute([':login' => $user, ':ref' => $ref]);
$ligne = $stmt->fetch();

if ($ligne) {
    $quantite = $ligne['quantite_d_article'] - 1;
    if ($quantite <= 0) {
        $delete = $connection->prepare('DELETE FROM pannier WHERE mail_login = :login AND reference = :ref');
        $delete->execute([':login' => $user, ':ref' => $ref]);
    } else {
        $update = $connection->prepare('UPDATE pannier SET quantite_d_article = :quantite WHERE mail_login = :login AND reference = :ref');
        $update->execute([':quantite' => $quantite, ':login' => $user, ':ref' => $ref]);
    }
}

header('Location: cart.php');
exit;
?>