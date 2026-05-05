<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'connection.php';

$code = $_REQUEST['code'] ?? null;
if (!$code) {
    header('Location: BackOffice.php');
    exit;
}

try {
    $sql = 'DELETE FROM categorie WHERE code_de_la_categorie = :codeCateg';
    $stmt = $connection->prepare($sql);
    $stmt->bindValue(':codeCateg', $code, PDO::PARAM_STR);
    $stmt->execute();
    header('Location: BackOffice.php');
    exit;
} catch (PDOException $e) {
    echo 'Erreur: ' . htmlspecialchars($e->getMessage());
    echo '<br><a href="BackOffice.php">Retour au BackOffice</a>';
}

    
        






?>