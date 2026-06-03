<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$login = $_SESSION['login'];
$reference = trim($_POST['reference'] ?? '');
$note = (int)($_POST['note'] ?? 0);
$commentaire = trim($_POST['commentaire'] ?? '');

if ($reference === '' || $note < 1 || $note > 5) {
    header('Location: product.php?ref=' . urlencode($reference));
    exit;
}

// Vérifier que le user a acheté le produit
$achatStmt = $connection->prepare(
    'SELECT lc.reference FROM ligne_commande lc
     JOIN commande c ON lc.commande_id = c.id
     WHERE c.mail_login = ? AND lc.reference = ?'
);
$achatStmt->execute([$login, $reference]);
if (!$achatStmt->fetch()) {
    header('Location: product.php?ref=' . urlencode($reference) . '&erreur=achat');
    exit;
}

// Vérifier qu'il a pas déjà laissé un avis
$avisStmt = $connection->prepare('SELECT id FROM avis WHERE mail_login = ? AND reference = ?');
$avisStmt->execute([$login, $reference]);
if ($avisStmt->fetch()) {
    header('Location: product.php?ref=' . urlencode($reference) . '&erreur=existe');
    exit;
}

// Insérer l'avis
$insertStmt = $connection->prepare(
    'INSERT INTO avis (mail_login, reference, note, commentaire) VALUES (?, ?, ?, ?)'
);
$insertStmt->execute([$login, $reference, $note, $commentaire]);

header('Location: product.php?ref=' . urlencode($reference));
exit;