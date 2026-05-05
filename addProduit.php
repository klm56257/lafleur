<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ajouterProduit.php');
    exit;
}

$stmt = $connection->prepare(
    'INSERT INTO produit (
        reference,
        designation,
        photo,
        prix,
        quantite_en_stock,
        code_de_la_categorie
    ) VALUES (
        :referenceProduit,
        :designationProduit,
        :photoProduit,
        :prixProduit,
        :quantite_stock_Produit,
        :code_categorie_Produit
    )'
);
$stmt->bindParam(':referenceProduit', $_POST['referenceProduit'], PDO::PARAM_STR);
$stmt->bindParam(':designationProduit', $_POST['designationProduit'], PDO::PARAM_STR);
$stmt->bindParam(':photoProduit', $_POST['photoProduit'], PDO::PARAM_STR);
$stmt->bindParam(':prixProduit', $_POST['prixProduit'], PDO::PARAM_STR);
$stmt->bindParam(':quantite_stock_Produit', $_POST['quantite_stock_Produit'], PDO::PARAM_INT);
$stmt->bindParam(':code_categorie_Produit', $_POST['code_categorie_Produit'], PDO::PARAM_STR);

$stmt->execute();

header('Location: BackOffice.php');
exit;
?>



