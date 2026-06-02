<?php
session_start();
require_once 'connection.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$code = strtoupper(trim($_POST['code']));
$reduction = $_POST['reduction'];
$date_expiration = $_POST['date_expiration'];
$max_utilisations = $_POST['max_utilisations'];

$check = $connection->prepare('SELECT code FROM code_promo WHERE code = ?');
$check->execute([$code]);

if ($check->fetch()) {
    header('Location: ajouterCodePromo.php?erreur=exists');
    exit;
}

$stmt = $connection->prepare('INSERT INTO code_promo (code, reduction, date_expiration, max_utilisations) VALUES (?, ?, ?, ?)');
$stmt->execute([$code, $reduction, $date_expiration, $max_utilisations]);
header('Location: BackOffice.php');
exit;