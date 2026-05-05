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

$stmt = $connection->prepare('UPDATE pannier SET quantite_d_article = quantite_d_article + 1 WHERE mail_login = :login AND reference = :ref');
$stmt->execute([':login' => $user, ':ref' => $ref]);

header('Location: cart.php');
exit;
?>