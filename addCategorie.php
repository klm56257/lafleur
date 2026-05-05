<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$codeCategorie = $_POST['codeCategorie'] ?? '';
$nomCategorie = $_POST['nomCategorie'] ?? '';

if ($codeCategorie !== '' && $nomCategorie !== '') {
    $stmt = $connection->prepare('INSERT INTO categorie (code_de_la_categorie, nom_de_la_categorie) VALUES (:codeCateg, :nomCateg)');
    $stmt->bindParam(':nomCateg', $nomCategorie);
    $stmt->bindParam(':codeCateg', $codeCategorie);
    $stmt->execute();
}

header('Location: admin_dashboard.php');
exit;
?>



