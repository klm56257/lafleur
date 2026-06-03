<?php 

try {
    $dns ='mysql:host=localhost;dbname=baselafleur;charset=utf8mb4';
    $uttilisateur ='root';
    $motDePasse ='';
    $connection = new PDO($dns, $uttilisateur, $motDePasse, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ]);
}catch (PDOException $e){
    echo "connection à MySQL impossible : ", $e->getMessage();
    die();
}

?>