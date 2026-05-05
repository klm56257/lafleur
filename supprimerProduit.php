<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'connection.php';

$ref = $_REQUEST['ref'] ?? null;
if (!$ref) {
    header('Location: BackOffice.php');
    exit;
}

try {
    $sql = 'DELETE FROM produit WHERE reference = :ref';
    $stmt = $connection->prepare($sql);
    $stmt->bindValue(':ref', $ref, PDO::PARAM_STR);
    $stmt->execute();
    header('Location: BackOffice.php');
    exit;
} catch (PDOException $e) {
    echo 'Erreur: ' . htmlspecialchars($e->getMessage());
    echo '<br><a href="BackOffice.php">Retour au BackOffice</a>';
}
?>